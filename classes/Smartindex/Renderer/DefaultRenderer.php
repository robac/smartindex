<?php
namespace Smartindex\Renderer;

class DefaultRenderer implements \Smartindex\Renderer\iIndexRenderer {
    private $useWrapper = true;
    private $wrapperClasses = array();
    private $wrapperId;
    
    private $config;
    private $basicData;
    
    
    public function setWrapper($useWrapper, $id = NULL) {
        $this->useWrapper = $useWrapper;
        
        $this->wrapperClasses[] = \Smartindex\Configuration\IndexConfiguration::TREE_CLASS;
        $this->wrapperClasses[] = $this->config->getAttribute('cssClass');
        if ($this->config->getAttribute('highlight')) {
            $this->wrapperClasses[] = \Smartindex\Configuration\IndexConfiguration::HIGHLITE_CLASS;
        }
        
        $this->wrapperId = $id;
    }

    public function __construct(\Smartindex\Configuration\IndexConfiguration $config) {
        $this->config = $config;
    }
    
    public function render($data, &$document) {
        if ($this->useWrapper) {
            $document .= "<div". \Smartindex\Utils\HtmlHelper::createIdClassesPart($this->wrapperId, $this->wrapperClasses).">";
        }
        
        $this->basicData = $data;
        //if ($this->config->showMain) $document .= "<ul><li class=\"namespace open\"><div><a href=\"#\">root</a></div>";
        $this->buildList($data, $this->config->getAttribute('namespace'), $document, 1);

        if ($this->useWrapper) {
            $document .= "</div>";
        }
    }
    
    private function buildList($data, $namespace, &$document, $level) {
        if ( ! array_key_exists($namespace, $data))
                return "";
        
        $document .= "<ul>";
        
        foreach($data[$namespace][\Smartindex\Indexer\iIndexer::KEY_DIRS] as $ns)  {
            $classes = array(self::CLASS_NAMESPACE);
            
            if (($this->config->getAttribute('openDepth') > $level) || (isset($data[$ns][\Smartindex\Indexer\iIndexer::KEY_FOLLOW]))) {
                $classes[] = self::CLASS_OPEN;
            } else {
                $classes[] = self::CLASS_CLOSED;
            }

                
            $document .= "<li". \Smartindex\Utils\HtmlHelper::createIdClassesPart(NULL, $classes)."><div>"
                         . \Smartindex\Utils\HtmlHelper::createSitemapLink(\Smartindex\Utils\IndexTools::constructPageName($namespace, $ns), $ns)
                         ."</div>";
            
            $this->buildList($data, \Smartindex\Utils\IndexTools::constructPageName($namespace, $ns), $document, $level+1);
            $document .= "</li>";
        }
        
        
        foreach($data[$namespace][\Smartindex\Indexer\iIndexer::KEY_PAGES] as $key => $page) {
            $heading = $data[$namespace][\Smartindex\Indexer\iIndexer::KEY_PAGES_TITLE][$key];
            if ($heading == "")
                $heading = $page;
            $document .= "<li". \Smartindex\Utils\HtmlHelper::createIdClassesPart(NULL, array(self::CLASS_PAGE))."><div>"
                         . \Smartindex\Utils\HtmlHelper::createInternalLink(\Smartindex\Utils\IndexTools::constructPageName($namespace, $page), NULL, $heading, NULL, NULL)
                         ."</div ></li>";
        }
        
        $document .= "</ul>";
    }
}
