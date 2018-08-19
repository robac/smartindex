<?php

namespace Smartindex\Index;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\IndexTools;
use Smartindex\Index\iIndexBuilder;

class DefaultIndexBuilder implements iIndexBuilder
{
    private $config;
    private $index;
    private $loadLevel = 1;
    private $followPath = "";

    public function __construct(IndexConfiguration $config)
    {
        $this->config = $config;
        $this->loadLevel = $this->config->getAttribute('loadLevel');
        $this->followPath = $this->config->getAttribute('followPath');
    }

    public function getIndex()
    {
        $this->index = new Index();

        $namespace = $this->config->getAttribute('namespace');
        $this->addNamespaceToIndex($namespace, 1);

        return $this->index;
    }

    private function addNamespaceToIndex($namespace, $level)
    {
        $followNS = array();

        //open namespace directory
        $dirPath = IndexTools::getNamespaceDataDirectory($namespace);
        $dh = @opendir($dirPath);
        if ( ! $dh)
            return;

        $tryFollow = $level < $this->loadLevel || IndexTools::isSubnamespace($this->followPath);

        //traverse all files inside directory
        while (($file = readdir($dh)) !== false) {
            if ( ! IndexTools::fileIsPage($file))
                continue;

            $item = IndexTools::excludePageExtension($file);
            //*** CHANGE IT
            /*$itemTitle = p_get_first_heading(IndexTools::getPageId($namespace, $item), METADATA_DONT_RENDER);
            if (is_null($itemTitle)) {*/
                $itemTitle = $item;
            //}
            //*** CHANGE IT
            $isNamespace = is_dir(IndexTools::getPagePath($dirPath, $file));

            $this->index->addItem($namespace, $item, $itemTitle, $isNamespace);

            if ($tryFollow && $isNamespace)
                if (($level < $this->loadLevel) || IndexTools::isSubnamespace($this->followPath, $item) || ($this->followPath == $item))
                    $followNS[] = $item;
        }
        closedir($dh);

        foreach ($followNS as $subNS) {
            $this->addNamespaceToIndex(IndexTools::getItemId($namespace, $subNS), $level+1);
        }
    }

}