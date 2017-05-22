<?php

namespace Develoid\TranslatorBundle\Microsoft;

use Develoid\TranslatorBundle\Exception\InvalidTranslationException;
use Develoid\TranslatorBundle\Model\TranslatorInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class Translator implements TranslatorInterface
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $translateEndpoint = 'http://api.microsofttranslator.com/v2/Http.svc/Translate?text=%s&from=%s&to=%s';

    /**
     * @var string
     */
    private $translateArrayEndpoint = 'http://api.microsofttranslator.com/V2/Http.svc/TranslateArray';

    /**
     * @var string
     */
    private $speakEndpoint = 'http://api.microsofttranslator.com/v2/Http.svc/Speak?text=%s&language=%s';

    /**
     * @var string
     */
    private $detectEndpoint = 'http://api.microsofttranslator.com/v2/Http.svc/Detect?text=%s';

    /**
     * @var string
     */
    private $tokenUrl = 'https://api.cognitive.microsoft.com/sts/v1.0/issueToken';

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var \DateTime
     */
    private $accessTokenExpirationDate;

    /**
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->client = new Client();
        $this->apiKey = $apiKey;
        $this->accessToken  = null;
        $this->accessTokenExpirationDate = null;
    }

    /**
     * @param $text
     * @param $source
     * @param $target
     * @param bool $all
     * @return array|string
     */
    public function translate($text, $source, $target, $all = false)
    {
        if  (is_array($text)) {
            return $this->translateArray($text, $source, $target);
        }

        $text = urlencode($text);
        $url = sprintf($this->translateEndpoint, $text, $source, $target);
        $response = $this->getResponse($url);
        $response = (array) simplexml_load_string($response);

        if ($all) {
            return $response;
        }

        return $response[0];
    }

    /**
     * @param $texts
     * @param $source
     * @param $target
     * @return array
     */
    public function translateArray($texts, $source, $target)
    {
        $response = $this->getTranslateArrayResponse(
            $this->translateArrayEndpoint,
            $this->getTranslateArrayXmlRequest($texts, $source, $target)
        );
        $response = simplexml_load_string($response);

        $result = array();
        foreach($response->TranslateArrayResponse as $translatedArrObject){
            $result[] = (string) $translatedArrObject->TranslatedText;
        }

        return $result;
    }

    /**
     * Detects the language of the specified text.
     *
     * @param string $text The text to detect the language for.
     * @return string
     */
    public function detect($text)
    {
        $text = urlencode($text);
        $url = sprintf($this->detectEndpoint, $text);
        $response = $this->getResponse($url);
        $response = simplexml_load_string($response);

        return $response;
    }

    /**
     * @param string $text
     * @param string $source
     * @return string
     * @throws InvalidTranslationException
     */
    public function speak($text, $source)
    {
        $text = urlencode($text);
        $url = sprintf($this->speakEndpoint, $text, $source);
        $response = $this->getResponse($url);

        return $response;
    }

    /**
     * @param $url
     * @return string
     * @throws InvalidTranslationException
     */
    private function getResponse($url)
    {
        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getToken(),
                    'Content-Type' => 'text/xml'
                ]
            ]);

            return (string) $response->getBody();
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            $error = json_decode($body, true);

            throw new InvalidTranslationException(
                'Microsoft: ' . $error['error_description'],
                $e->getResponse()->getStatusCode(),
                $e
            );
        }
    }

    /**
     * @param $url
     * @param $xmlRequest
     * @return string
     * @throws InvalidTranslationException
     */
    private function getTranslateArrayResponse($url, $xmlRequest)
    {
        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getToken(),
                    'Content-Type' => 'text/xml'
                ],
                'body' => $xmlRequest,
            ]);

            return (string) $response->getBody();
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();

            throw new InvalidTranslationException(
                'Microsoft: ' . $body,
                $e->getResponse()->getStatusCode(),
                $e
            );
        }
    }

    /**
     * @param $texts
     * @param $source
     * @param $target
     * @return string
     */
    private function getTranslateArrayXmlRequest($texts, $source, $target)
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startElement('TranslateArrayRequest');
        $xml->startElement('AppId');
        $xml->endElement(); //AppId
        $xml->writeElement('From', $source);
        $xml->startElement('Options');
        $xml->startElement('ContentType');
        $xml->writeAttribute('xmlns', 'http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2');
        $xml->writeRaw('text/plain');
        $xml->endElement(); //ContentType
        $xml->endElement(); //Options
        $xml->startElement('Texts');
        foreach ($texts as $text) {
            $xml->startElement('string');
            $xml->writeAttribute('xmlns', 'http://schemas.microsoft.com/2003/10/Serialization/Arrays');
            $xml->writeCdata($text);
            $xml->endElement(); //string
        }
        $xml->endElement(); //Texts
        $xml->writeElement('To', $target);
        $xml->endElement(); //TranslateArrayRequest

        return $xml->flush();
    }

    /**
     * @return string
     * @throws InvalidTranslationException
     */
    private function getToken()
    {
        if (null !== $this->accessToken && false === $this->tokenIsExpired()) {
            return $this->accessToken;
        }

        try {
            $response = $this->client->post($this->tokenUrl, [
                'headers' =>[
                    'Ocp-Apim-Subscription-Key' => $this->apiKey,
                ],
            ]);
            $body = (string) $response->getBody();

            $this->accessToken = $body;
            $this->accessTokenExpirationDate = new \DateTime('now +8 minutes');

            return $this->accessToken;
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            $error = json_decode($body, true);

            throw new InvalidTranslationException(
                'Microsoft: ' . $error['message'],
                $e->getResponse()->getStatusCode(),
                $e
            );
        }
    }

    /**
     * @return bool
     */
    private function tokenIsExpired()
    {
        if (null === $this->accessTokenExpirationDate) {
            return true;
        }

        $now = new \DateTime('now');

        return ($now->getTimestamp() >= $this->accessTokenExpirationDate->getTimestamp());
    }
}
