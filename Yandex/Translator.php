<?php

namespace Develoid\TranslatorBundle\Yandex;

use Develoid\TranslatorBundle\Exception\InvalidTranslationException;
use Develoid\TranslatorBundle\Model\TranslatorInterface;
use Yandex\Translate\Translator as YandexTranslator;

class Translator implements TranslatorInterface
{
    /**
     * @var YandexTranslator
     */
    private $translator;

    /**
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->translator = new YandexTranslator($apiKey);
    }

    /**
     * @param $text
     * @param $source
     * @param $target
     * @param bool $all
     * @return string
     * @throws InvalidTranslationException
     */
    public function translate($text, $source, $target, $all = false)
    {
        try {
            $translation = $this->translator->translate($text, $source . '-' . $target);

            return (string) $translation;
        } catch (\Exception $e) {
            throw new InvalidTranslationException('Yandex: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
