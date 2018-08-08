<?php

namespace Smartindex\Indexer;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\IndexTools;
use Smartindex\Indexer\iIndexBuilder;

class DefaultIndexBuilder implements iIndexBuilder
{
    private $config;
    private $index;

    public function __construct(IndexConfiguration $config)
    {
        $this->config = $config;
    }

    public function getIndex()
    {
        $this->index = new Index();

        $namespace = $this->config->getAttribute('namespace');
        $this->addNamespaceToIndex($namespace, $namespace, 1);

        return $this->index;
    }

    private function addNamespaceToIndex($namespace, $namespaceID, $level)
    {
        $namespaces = array();

        //open namespace directory
        $dirPath = IndexTools::getNamespaceDataDirectory($namespaceID);
        $dh = @opendir($dirPath);
        if ( ! $dh)
            return;

        $tryFollow = $level < $this->config->getAttribute('loadLevel') || IndexTools::isSubnamespace($this->config->getAttribute('followPath'));

        //traverse all files inside directory
        while (($file = readdir($dh)) !== false) {
            if ( ! IndexTools::fileIsPage($file))
                continue;

            $itemPath = IndexTools::getPagePath($dirPath, $file);
            $itemId = IndexTools::excludePageExtension($file);
            $itemTitle = p_get_first_heading(IndexTools::getPageId($namespace, $itemId), METADATA_DONT_RENDER);
            $isNamespace = is_dir($itemPath);

            $this->index->addItem($namespaceID, $itemId, $itemTitle, $isNamespace);

            if ($tryFollow && $isNamespace)
                if (($level < $this->config->getAttribute('loadLevel')) || IndexTools::isSubnamespace($this->config->getAttribute('followPath'), $itemId) || ($this->config->getAttribute('followPath') == $itemId))
                    $namespaces[] = $itemId;
        }
        closedir($dh);

        print_r($this->index->namespace);

        foreach ($namespaces as $subnamespace) {

        }

        if (($level < $this->config->getAttribute('loadLevel')) || $this->info[$level][iIndexBuilder::INFO_FOLLOW]) {
            foreach ($index[$namespace][iIndexBuilder::KEY_DIRS] as $subNamespace) {
                $isFollow = $this->checkFollowPath($subNamespace, $level + 1);
                if ($isFollow) {
//                    $data[$namespace][iIndexer::KEY_FOLLOW] = true;
                }
                if (($level < $this->config->getAttribute('loadLevel')) || $isFollow) {
                    $this->addInfo($level + 1, $subNamespace);
                    $this->addNamespaceToIndex($index, $level + 1);
                }
            }
        }
    }

}