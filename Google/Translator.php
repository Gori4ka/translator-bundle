<?php

namespace Develoid\TranslatorBundle\Google;

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
    private $endpoint = 'https://www.googleapis.com/language/translate/v2?key=%s&q=%s&source=%s&target=%s';

    /**
     * @var Client
     */
    private $client;

    /**
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client();
    }

    /**
     * @param $text
     * @param $source
     * @param $target
     * @param bool $all
     * @return array|string
     * @throws InvalidTranslationException
     */
    public function translate($text, $source, $target, $all = false)
    {
        try {
            $text = urlencode($text);
            $url = sprintf($this->endpoint, $this->apiKey, $text, $source, $target);
            $response = $this->client->get($url);
            $body = (string) $response->getBody();
            $body = json_decode($body, true);

            if ($all) {
                return array_values($body['data']['translations']);
            }

            return $body['data']['translations'][0]['translatedText'];
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody();
            $error = json_decode($body, true)['error'];

            throw new InvalidTranslationException('Google: ' . $error['message'], $error['code'], $e);
        }
    }
}
