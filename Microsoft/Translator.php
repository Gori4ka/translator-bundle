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
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $translateEndpoint = 'http://api.microsofttranslator.com/v2/Http.svc/Translate?text=%s&from=%s&to=%s';

    /**
     * @var string
     */
    private $speakEndpoint = 'http://api.microsofttranslator.com/v2/Http.svc/Speak?text=%s&language=%s';

    /**
     * @var string
     */
    private $grantType = 'client_credentials';

    /**
     * @var string
     */
    private $scopeUrl = 'http://api.microsofttranslator.com';

    /**
     * @var string
     */
    private $authUrl = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @param $clientId
     * @param $clientSecret
     */
    public function __construct($clientId, $clientSecret)
    {
        $this->client = new Client();
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken  = $this->getToken();
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
     * @param $text
     * @param $language
     * @return mixed
     */
    public function speak($text, $language)
    {
        $text = urlencode($text);
        $url = sprintf($this->speakEndpoint, $text, $language);
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
                    'Authorization' => 'Bearer ' . $this->accessToken,
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
     * @return string
     * @throws InvalidTranslationException
     */
    private function getToken()
    {
        try {
            $config = [
                'grant_type' => $this->grantType,
                'scope' => $this->scopeUrl,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ];

            $response = $this->client->post($this->authUrl, [
                'body' => http_build_query($config)
            ]);
            $body = (string) $response->getBody();
            $body = json_decode($body, true);

            return $body['access_token'];
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
}
