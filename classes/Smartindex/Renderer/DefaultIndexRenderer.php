<?php
namespace Smartindex\Renderer;

use Smartindex\Indexer\iIndexBuilder;
use Smartindex\Indexer\DefaultIndexBuilder;
use Smartindex\Renderer\iIndexRenderer;
use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\IndexTools;
use Smartindex\Utils\HtmlHelper;

class DefaultIndexRenderer implements iIndexRenderer {
    private $config;
    private $index;

    public function __construct(IndexConfiguration $config) {
        $this->config = $config;
    }
    
    public function render(&$document) {
        $indexer = new DefaultIndexBuilder($this->config);
        $this->index = $indexer->getIndex();

        $this->buildList($this->config->getAttribute('namespace'), $document, 1);
    }
    
    private function buildList($namespace, &$document, $level) {
        if ( ! array_key_exists($namespace, $this->index))
                return "";
        
        $document .= "<ul>";
        
        foreach($this->index[$namespace][iIndexBuilder::KEY_DIRS] as $ns)  {
            $classes = array(self::CLASS_NAMESPACE);
            
            if (($this->config->getAttribute('loadLevel') > $level) || (isset($this->index[$ns][iIndexBuilder::KEY_FOLLOW]))) {
                $classes[] = self::CLASS_OPEN;
            } else {
                $classes[] = self::CLASS_CLOSED;
            }

                
            $document .= "<li". HtmlHelper::createIdClassesPart(NULL, $classes)."><div>"
                         . HtmlHelper::createSitemapLink(IndexTools::getPageId($namespace, $ns), $ns)
                         ."</div>";
            
            $this->buildList($this->index, IndexTools::getPageId($namespace, $ns), $document, $level+1);
            $document .= "</li>";
        }
        
        
        foreach($this->index[$namespace][iIndexBuilder::KEY_PAGES] as $key => $page) {
            $heading = $this->index[$namespace][iIndexBuilder::KEY_PAGES_TITLE][$key];
            if ($heading == "")
                $heading = $page;
            $document .= "<li". HtmlHelper::createIdClassesPart(NULL, array(self::CLASS_PAGE))."><div>"
                         . HtmlHelper::createInternalLink(IndexTools::getPageId($namespace, $page), NULL, $heading, NULL, NULL)
                         ."</div ></li>";
        }
        
        $document .= "</ul>";
    }
}
