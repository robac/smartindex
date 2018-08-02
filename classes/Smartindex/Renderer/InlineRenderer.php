<?php
/**
 * Created by PhpStorm.
 * User: poch
 * Date: 02.08.2018
 * Time: 15:02
 */

namespace Smartindex\Renderer;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\HtmlHelper;

class InlineRenderer implements iRenderer
{
    private $config;
    private $type;

    public function __construct(IndexConfiguration $config)
    {
        $this->config = $config;
    }

    public function render(&$document)
    {
        switch ($this->type) {
            case 'JsonTreeConfig':
                $this->renderJsonTreeConfiguration($document);
        }
    }

    public function setType($type) {
        if ($type !== 'JsonTreeConfig') {
            throw new RenderernException('Unknown inline type $type.');
        }
        $this->type = $type;
    }

    private function renderJsonTreeConfiguration(&$document) {
        $JSONConfig = new \stdClass();
        $JSONConfig->depth = $this->config->getAttribute('ajaxDepth');
        $JSONConfig->theme = $this->config->getAttribute('theme');

        $document .= HtmlHelper::createInlineScript(HtmlHelper::createInlineJSON($this->config->getAttribute('treeId')."_conf", $JSONConfig));
    }
}