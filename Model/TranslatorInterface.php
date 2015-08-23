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
}
