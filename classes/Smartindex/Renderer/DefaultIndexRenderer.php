<?php
namespace Smartindex\Renderer;

use Smartindex\Index\iIndexBuilder;
use Smartindex\Index\DefaultIndexBuilder;
use Smartindex\Index\Index;
use Smartindex\Renderer\iIndexRenderer;
use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\IndexTools;
use Smartindex\Utils\HtmlHelper;
use Smartindex\Manager\TemplateManager;
use splitbrain\phpcli\Exception;

class DefaultIndexRenderer implements iIndexRenderer {
    private $config;
    private $index;

    public function __construct(IndexConfiguration $config) {
        $this->config = $config;
    }
    
    public function render(&$document) {
        $indexBuilder = new DefaultIndexBuilder($this->config);
        $this->index = $indexBuilder->getIndex();
        $document .= $this->renderIndex($this->config->getAttribute('namespace'));
    }

    public function isNamespace($namespace, $id) {
        return $this->index->namespace[$namespace][$id][Index::IS_NS];
    }


    private function renderIndex($namespace) {
        if ( ! array_key_exists($namespace, $this->index->namespace))
            return;

        $template = TemplateManager::getTemplate('renderer/index/default.tpl', array(
            'getItemId' => '\\Smartindex\\Utils\\IndexTools::getItemId',
            'getPageURL' => '\\Smartindex\\Utils\\IndexTools::getPageURL',
            'isNamespace' => array($this, 'isNamespace'),
        ));

        return $template->render(array(
            'items' => $this->index->namespace,
            'namespace' => $namespace,
        ));
    }
}
