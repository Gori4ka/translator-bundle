<?php

namespace Develoid\TranslatorBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Develoid\TranslatorBundle\Model\TranslatorInterface;

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
     * @return string
     */
    public function translate($text, $source, $target)
    {
        return $this->translator->translate($text, $source, $target);
    }
}
