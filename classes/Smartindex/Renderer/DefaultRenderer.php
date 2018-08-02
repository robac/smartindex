<?php
namespace Smartindex\Renderer;

use Smartindex\Utils\HtmlHelper;
use Smartindex\Renderer\iIndexRenderer;
use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Configuration\IndexTools;

class DefaultRenderer implements IndexRenderer {
    private $useWrapper = true;
    private $wrapperClasses = array();
    private $wrapperId;
    
    private $config;
    private $basicData;
    
    
    public function setWrapper($useWrapper, $id = NULL) {
        $this->useWrapper = $useWrapper;
        
        $this->wrapperClasses[] = IndexConfiguration::TREE_CLASS;
        $this->wrapperClasses[] = $this->config->getAttribute('cssClass');
        if ($this->config->getAttribute('highlight')) {
            $this->wrapperClasses[] = IndexConfiguration::HIGHLITE_CLASS;
        }
        
        $this->wrapperId = $id;
    }

    public function __construct(IndexConfiguration $config) {
        $this->config = $config;
    }
    
    public function render($data, &$document) {
        if ($this->useWrapper) {
            $document .= "<div". HtmlHelper::createIdClassesPart($this->wrapperId, $this->wrapperClasses).">";
        }
        
        $this->basicData = $data;
        $this->buildList($data, $this->config->getAttribute('namespace'), $document, 1);

        if ($this->useWrapper) {
            $document .= "</div>";
        }

        $ajaxConfig = new stdClass();
        $ajaxConfig->url  = AJAX_URL;
        $ajaxConfig->depth = $this->config->getAttribute('ajaxDepth');
        $ajaxConfig->theme = $this->config->getAttribute('theme');

        $document->doc .= HtmlHelper::createInlineScript(HtmlHelper::createInlineJSON($this->config->getAttribute('treeId')."_conf", $ajaxConfig));
    }
    
    private function buildList($data, $namespace, &$document, $level) {
        if ( ! array_key_exists($namespace, $data))
                return "";
        
        $document .= "<ul>";
        
        foreach($data[$namespace][iIndexer::KEY_DIRS] as $ns)  {
            $classes = array(self::CLASS_NAMESPACE);
            
            if (($this->config->getAttribute('openDepth') > $level) || (isset($data[$ns][iIndexer::KEY_FOLLOW]))) {
                $classes[] = self::CLASS_OPEN;
            } else {
                $classes[] = self::CLASS_CLOSED;
            }

                
            $document .= "<li". HtmlHelper::createIdClassesPart(NULL, $classes)."><div>"
                         . HtmlHelper::createSitemapLink(IndexTools::constructPageName($namespace, $ns), $ns)
                         ."</div>";
            
            $this->buildList($data, IndexTools::constructPageName($namespace, $ns), $document, $level+1);
            $document .= "</li>";
        }
        
        
        foreach($data[$namespace][iIndexer::KEY_PAGES] as $key => $page) {
            $heading = $data[$namespace][iIndexer::KEY_PAGES_TITLE][$key];
            if ($heading == "")
                $heading = $page;
            $document .= "<li". HtmlHelper::createIdClassesPart(NULL, array(self::CLASS_PAGE))."><div>"
                         . HtmlHelper::createInternalLink(IndexTools::constructPageName($namespace, $page), NULL, $heading, NULL, NULL)
                         ."</div ></li>";
        }
        
        $document .= "</ul>";
    }
}
