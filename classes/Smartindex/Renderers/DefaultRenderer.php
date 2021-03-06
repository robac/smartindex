<?php
namespace Smartindex\Renderers;

class DefaultRenderer implements \Smartindex\Renderers\iIndexRenderer {
    private $useWrapper = true;
    private $wrapperClasses = array();
    private $wrapperId;
    
    private $config;
    private $basicData;
    
    
    public function setWrapper($useWrapper, $id = NULL) {
        $this->useWrapper = $useWrapper;
        
        $this->wrapperClasses[] = \SmartIndexConf::TREE_CLASS;
        $this->wrapperClasses[] = $this->config->cssClass;
        if ($this->config->highlite) {
            $this->wrapperClasses[] = \SmartIndexConf::HIGHLITE_CLASS;
        }
        
        $this->wrapperId = $id;
    }

    public function __construct(\SmartIndexConf $config) {
        $this->config = $config;
    }
    
    public function render($data, &$document) {
        if ($this->useWrapper) {
            $document .= "<div".\HtmlHelper::createIdClassesPart($this->wrapperId, $this->wrapperClasses).">";
        }
        
        $this->basicData = $data;
        //if ($this->config->showMain) $document .= "<ul><li class=\"namespace open\"><div><a href=\"#\">root</a></div>";
        $this->buildList($data, $this->config->namespace, $document, 1);
        //if ($this->config->showMain) $document .= "</li></ul>";
        
        if ($this->useWrapper) {
            $document .= "</div>";
        }
    }
    
    private function buildList($data, $namespace, &$document, $level) {
        if (!array_key_exists($namespace, $data))
                return "";
        
        $document .= "<ul>";
        
        foreach($data[$namespace][\PageSeeker::KEY_DIRS] as $ns)  {
            $classes = array(self::CLASS_NAMESPACE);
            
            if (($this->config->openDepth > $level) || (isset($data[$ns][\PageSeeker::KEY_FOLLOW]))) {
                $classes[] = self::CLASS_OPEN;
            } else {
                $classes[] = self::CLASS_CLOSED;
            }

                
            $document .= "<li".\HtmlHelper::createIdClassesPart(NULL, $classes)."><div>"
                         .\HtmlHelper::createSitemapLink(\PageTools::constructPageName($namespace, $ns), $ns)
                         ."</div>";
            
            $this->buildList($data, \PageTools::constructPageName($namespace, $ns), $document, $level+1);
            $document .= "</li>";
        }
        
        
        foreach($data[$namespace][\PageSeeker::KEY_PAGES] as $key => $page) {
            $heading = $data[$namespace][\PageSeeker::KEY_PAGES_TITLE][$key];
            if ($heading == "")
                $heading = $page;
            $document .= "<li".\HtmlHelper::createIdClassesPart(NULL, array(self::CLASS_PAGE))."><div>"
                         .\HtmlHelper::createInternalLink(\PageTools::constructPageName($namespace, $page), NULL, $heading, NULL, NULL)
                         ."</div ></li>";
        }
        
        $document .= "</ul>";
    }
}
