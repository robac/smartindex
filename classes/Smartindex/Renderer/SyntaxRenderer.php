<?php

namespace Smartindex\Renderer;

use Smartindex\Factory\RendererFactory;
use Smartindex\Utils\HtmlHelper;
use Smartindex\Configuration\IndexConfiguration;

class SyntaxRenderer implements iRenderer
{
    private $config;

    public function __construct(IndexConfiguration $config)
    {
        $this->config = $config;
    }

    public function render(&$document)
    {
        $index_renderer = $this->config->getAttribute('indexRenderer');
        $json_renderer = new InlineRenderer($this->config);
        $json_renderer->setType('JsonTreeConfig');

        $document .= "<div". HtmlHelper::createIdClassesPart($this->config->getAttribute('indexId'), $this->getWrapperClasses()).">";
        $index_renderer->render($document);
        $document .= "</div>";
        $json_renderer->render($document);
    }

    public function getWrapperClasses() {
        $wrapperClasses = array();
        $wrapperClasses[] = IndexConfiguration::TREE_CLASS;
        $wrapperClasses[] = $this->config->getAttribute('indexClass');
        if ($this->config->getAttribute('highlight')) {
            $wrapperClasses[] = IndexConfiguration::HIGHLIGHT_CLASS;
        }

        return $wrapperClasses;
    }
}