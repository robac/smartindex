<?php
class AdminRenderer implements iIndexRenderer {

    private $useWrapper = true;
    private $wrapperClasses = array();
    private $wrapperId;
    
    private $config;
    private $basicData;
    
    
    public function setWrapper($useWrapper, $id = NULL) {
        $this->useWrapper = $useWrapper;
        
        $this->wrapperClasses[] = SmartIndexConf::TREE_CLASS;
        $this->wrapperClasses[] = $this->config->cssClass;
        if ($this->config->highlite) {
            $this->wrapperClasses[] = SmartIndexConf::HIGHLITE_CLASS;
        }
        
        $this->wrapperId = $id;
    }

    public function __construct(SmartIndexConf $config) {
        $this->config = $config;
    }
    
    public function render($data, &$document) {

/*        if ($this->useWrapper) {
            $document .= "<div".HtmlHelper::createIdClassesPart($this->wrapperId, $this->wrapperClasses).">";
        }
        
        $this->basicData = $data;
        //if ($this->config->showMain) $document .= "<ul><li class=\"namespace open\"><div><a href=\"#\">root</a></div>";
        $this->buildList($data, $this->config->namespace, $document, 1);
        //if ($this->config->showMain) $document .= "</li></ul>";
        
        if ($this->useWrapper) {
            $document .= "</div>";
        }*/

        $sorter = new DefaultSorter($this->config);

        $template = new Monotek\MiniTPL\Template(TEMPLATES_DIR);
        $template->load("admin.php");
        $pages = $sorter->sort($data);
        $template->assign("namespaces", $pages[0]);
        $template->assign("namespace", $this->config->namespace);
        $template->assign("isnamespace", $pages[1]);
        $template->render();
    }
    

}
