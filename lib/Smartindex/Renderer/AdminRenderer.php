<?php
namespace Smartindex\Renderer;

use Smartindex\Index\DefaultIndexBuilder;
use Smartindex\Renderer\iIndexRenderer;
use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Sorter\DefaultSorter;
use Smartindex\Manager\TemplateManager;
use Smartindex\Index\Index;

class AdminRenderer implements iIndexRenderer {
    private $config;
    private $index;


    public function __construct(IndexConfiguration $config) {
        $this->config = $config;
    }

    public function isNamespace($namespace, $id) {
        return $this->index->namespace[$namespace][$id][Index::IS_NS];
    }

    public function render(&$document) {
        $sorter = new DefaultSorter($this->config);

        $template = TemplateManager::getTemplate('renderer/admin.tpl', array(
            'isNamespace' => array($this, 'isNamespace'),
        ));

        $this->index = (new DefaultIndexBuilder($this->config))->getIndex();

        $document .= $template->render(array(
            'sectoken' => getSecurityToken(),
            'namespace' => $this->config->getAttribute('namespace'),
            'items' => $this->index->namespace,
        ));
    }


}

