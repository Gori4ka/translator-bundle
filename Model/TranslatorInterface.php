<?php

namespace Develoid\TranslatorBundle\Model;

interface TranslatorInterface
{
    /**
     * @param $text
     * @param $source
     * @param $target
     * @param bool $all
     * @return string|array
     */
    public function translate($text, $source, $target, $all = false);

    /**
     * Detects the language of the specified text.
     * @param string $text The text to detect the language for.
     * @return string
     */
    public function detect($text);
}
