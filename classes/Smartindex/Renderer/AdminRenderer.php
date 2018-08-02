<?php
namespace Smartindex\Renderer;

use Smartindex\Renderer\iIndexRenderer;
use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Sorter\DefaultSorter;

class AdminRenderer implements iIndexRenderer {

    private $useWrapper = true;
    private $wrapperClasses = array();
    private $wrapperId;

    private $config;
    private $basicData;


    public function setWrapper($useWrapper, $id = NULL) {
        $this->useWrapper = $useWrapper;

        $this->wrapperClasses[] = IndexConfiguration::TREE_CLASS;
        $this->wrapperClasses[] = $this->config->getAttribute('cssClass');
        if ($this->config->getAttribute('highlite')) {
            $this->wrapperClasses[] = IndexConfiguration::HIGHLITE_CLASS;
        }

        $this->wrapperId = $id;
    }

    public function __construct(IndexConfiguration $config) {
        $this->config = $config;
    }

    public function render($data, &$document) {
        $sorter = new DefaultSorter($this->config);
        $template = new \Monotek\MiniTPL\Template(TEMPLATES_DIR);
        $template->load("admin.tpl");
        $pages = $sorter->sort($data);
        $template->assign("namespace", $this->config->getAttribute('namespace'));
        $template->assign("page_titles", $pages[0]);
        $template->assign("isnamespace", $pages[1]);
        $template->assign("page_ids", $pages[2]);
        $template->assign("sectoken", getSecurityToken());
        $template->render();
    }


}

