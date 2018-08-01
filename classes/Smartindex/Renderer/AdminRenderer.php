<?php
namespace Smartindex\Renderer;

class AdminRenderer implements \Smartindex\Renderer\iIndexRenderer {

    private $useWrapper = true;
    private $wrapperClasses = array();
    private $wrapperId;

    private $config;
    private $basicData;


    public function setWrapper($useWrapper, $id = NULL) {
        $this->useWrapper = $useWrapper;

        $this->wrapperClasses[] = \Smartindex\Configuration\IndexConfiguration::TREE_CLASS;
        $this->wrapperClasses[] = $this->config->getAttribute('cssClass');
        if ($this->config->getAttribute('highlite')) {
            $this->wrapperClasses[] = \Smartindex\Configuration\IndexConfiguration::HIGHLITE_CLASS;
        }

        $this->wrapperId = $id;
    }

    public function __construct(\Smartindex\Configuration\IndexConfiguration $config) {
        $this->config = $config;
    }

    public function render($data, &$document) {
        $sorter = new \Smartindex\Sorter\DefaultSorter($this->config);
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

