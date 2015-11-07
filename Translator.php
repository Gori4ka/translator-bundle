<?php

namespace Develoid\TranslatorBundle;

use Develoid\TranslatorBundle\Model\TranslatorInterface;

class Translator implements TranslatorInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
        return $this->translator->translate($text, $source, $target, $all);
    }

    /**
     * Detects the language of the specified text.
     * @param string $text The text to detect the language for.
     * @return string
     */
    public function detect($text)
    {
        return $this->translator->detect($text);
    }
}
