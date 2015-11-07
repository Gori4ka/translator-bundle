<?php

namespace Develoid\TranslatorBundle;

use Develoid\TranslatorBundle\Model\TranslatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Translator implements TranslatorInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ContainerInterface $container
     * @param string $default
     */
    public function __construct(ContainerInterface $container, $default)
    {
        $this->translator = $container->get(sprintf('develoid_translator.%s_translator', $default));
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
