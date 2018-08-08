<?php
namespace Smartindex\Renderer;

use Smartindex\Index\iIndexBuilder;
use Smartindex\Index\DefaultIndexBuilder;
use Smartindex\Index\Index;
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
        $indexBuilder = new DefaultIndexBuilder($this->config);
        $this->index = $indexBuilder->getIndex();

        $this->renderNamespace($this->config->getAttribute('namespace'), $document, 1);
    }
    
    private function renderNamespace($namespace, &$document, $level) {
        if ( ! array_key_exists($namespace, $this->index->namespace))
                return "";
        
        $document .= "<ul>";
        
        foreach ($this->index->namespace[$namespace] as $item=>$data) {
            if ($data[Index::IS_NS]) {
                $classes = array(self::CLASS_NAMESPACE);
            } else {
                $classes = array(self::CLASS_PAGE);
            }

            $document .=
                "<li ".
                HtmlHelper::getClassAttribute($classes).
                "><div>"
                .HtmlHelper::createSitemapLink(IndexTools::getPageId($namespace, $item), $data[Index::TITLE])
                ."</div>";

        }



        /*foreach($this->index[$namespace][iIndexBuilder::KEY_DIRS] as $ns)  {
            $classes = array(self::CLASS_NAMESPACE);
            
            $document .= "<li". HtmlHelper::createIdClassesPart(NULL, $classes)."><div>"
                         . HtmlHelper::createSitemapLink(IndexTools::getPageId($namespace, $ns), $ns)
                         ."</div>";
            
            $this->renderNamespace($this->index, IndexTools::getPageId($namespace, $ns), $document, $level+1);
            $document .= "</li>";
        }
        
        
        foreach($this->index[$namespace][iIndexBuilder::KEY_PAGES] as $key => $page) {
            $heading = $this->index[$namespace][iIndexBuilder::KEY_PAGES_TITLE][$key];
            if ($heading == "")
                $heading = $page;
            $document .= "<li". HtmlHelper::createIdClassesPart(NULL, array(self::CLASS_PAGE))."><div>"
                         . HtmlHelper::createInternalLink(IndexTools::getPageId($namespace, $page), NULL, $heading, NULL, NULL)
                         ."</div ></li>";
        }*/
        
        $document .= "</ul>";

    }
}
