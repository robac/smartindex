<?php
namespace Smartindex\Renderer;

use Smartindex\Indexer\iIndexer;
use Smartindex\Utils\HtmlHelper;
use Smartindex\Renderer\iIndexRenderer;
use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\IndexTools;

class DefaultIndexRenderer implements iIndexRenderer {
    private $config;
    private $index;
    
    
    public function getWrapperClasses() {
        $wrapperClasses = array();
        $wrapperClasses[] = IndexConfiguration::TREE_CLASS;
        $wrapperClasses[] = $this->config->getAttribute('cssClass');
        if ($this->config->getAttribute('highlight')) {
            $wrapperClasses[] = IndexConfiguration::HIGHLIGHT_CLASS;
        }

        return $wrapperClasses;
    }

    public function __construct(IndexConfiguration $config) {
        $this->config = $config;
    }
    
    public function render(&$document) {
        $indexer = new DefaultIndexer($this->config);
        $this->index = $indexer->getIndex();

        if ($this->config->getAttribute('wrapper')) {
            $document .= "<div". HtmlHelper::createIdClassesPart($this->getAttribute('treeId'), $this->getWrapperClasses()).">";
        }
        
        $this->buildList($this->config->getAttribute('namespace'), $document, 1);

        if ($this->config->getAttribute('wrapper')) {
            $document .= "</div>";
        }

        $ajaxConfig = new \stdClass();
        $ajaxConfig->url  = AJAX_URL;
        $ajaxConfig->depth = $this->config->getAttribute('ajaxDepth');
        $ajaxConfig->theme = $this->config->getAttribute('theme');

        $x = HtmlHelper::createInlineScript(HtmlHelper::createInlineJSON($this->config->getAttribute('treeId')."_conf", $ajaxConfig));
        $document .= HtmlHelper::createInlineScript(HtmlHelper::createInlineJSON($this->config->getAttribute('treeId')."_conf", $ajaxConfig));
    }
    
    private function buildList($namespace, &$document, $level) {
        if ( ! array_key_exists($namespace, $this->index))
                return "";
        
        $document .= "<ul>";
        
        foreach($this->index[$namespace][iIndexer::KEY_DIRS] as $ns)  {
            $classes = array(self::CLASS_NAMESPACE);
            
            if (($this->config->getAttribute('openDepth') > $level) || (isset($this->index[$ns][iIndexer::KEY_FOLLOW]))) {
                $classes[] = self::CLASS_OPEN;
            } else {
                $classes[] = self::CLASS_CLOSED;
            }

                
            $document .= "<li". HtmlHelper::createIdClassesPart(NULL, $classes)."><div>"
                         . HtmlHelper::createSitemapLink(IndexTools::constructPageName($namespace, $ns), $ns)
                         ."</div>";
            
            $this->buildList($this->index, IndexTools::constructPageName($namespace, $ns), $document, $level+1);
            $document .= "</li>";
        }
        
        
        foreach($this->index[$namespace][iIndexer::KEY_PAGES] as $key => $page) {
            $heading = $this->index[$namespace][iIndexer::KEY_PAGES_TITLE][$key];
            if ($heading == "")
                $heading = $page;
            $document .= "<li". HtmlHelper::createIdClassesPart(NULL, array(self::CLASS_PAGE))."><div>"
                         . HtmlHelper::createInternalLink(IndexTools::constructPageName($namespace, $page), NULL, $heading, NULL, NULL)
                         ."</div ></li>";
        }
        
        $document .= "</ul>";
    }
}
