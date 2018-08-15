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
                return;

        $pages = "";
        $document .= "<ul>";
        
        foreach ($this->index->namespace[$namespace] as $item=>$data) {
            $isNamespace = $data[Index::IS_NS];

            if ($data[Index::IS_NS]) {
                if ($data[Index::IS_OPEN])
                    $classes = array(self::CLASS_NAMESPACE, self::CLASS_OPEN);
                else
                    $classes = array(self::CLASS_NAMESPACE, self::CLASS_CLOSED);
            } else {
                $classes = array(self::CLASS_PAGE);
            }

            $itemHTML =
                "<li ".
                HtmlHelper::getClassAttribute($classes).
                "><div>"
                .HtmlHelper::createSitemapLink(IndexTools::getPageId($namespace, $item), $data[Index::TITLE])
                ."</div>";


            if (( ! $data[Index::IS_NS]) && ($this->config->getAttribute('namespacesFirst'))) {
                $pages .= $itemHTML;
            } else {
                $document .= $itemHTML;
            }

            if ($isNamespace) {
                $this->renderNamespace(IndexTools::getPageId($namespace, $item), $document, $level+1);
            }

        }

        if ( ! $data[Index::IS_NS]) {
            $document .= $pages;
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
