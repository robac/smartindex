<?php

namespace Smartindex\Indexer;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\IndexTools;
use Smartindex\Indexer\iIndexer;

class DefaultIndexer implements iIndexer
{
    private $config;
    private $info;

    public function __construct(IndexConfiguration $config)
    {
        $this->config = $config;
        $this->init();
    }

    private function checkFollowPath($namespace, $level)
    {
        if ($this->info[$level - 1][iIndexer::INFO_FOLLOW] && isset($this->follow[$level]) && ($this->follow[$level] == $namespace)) {
            $this->info[$level][iIndexer::INFO_FOLLOW] = true;
            return true;
        } else {
            return false;
        }
    }

    private function init()
    {
        $this->info = array();
        $this->info[1][iIndexer::INFO_NS] = $this->config->getAttribute('namespace');
        $this->info[1][iIndexer::INFO_DIR] = IndexTools::getNamespacePagesDir($this->config->getAttribute('namespace'));
        $this->info[1][iIndexer::INFO_FOLLOW] = true;

        $this->follow = explode(IndexTools::$NS_SEPARATOR, $this->config->getAttribute('followPath'));
        unset($this->follow[count($this->follow) - 1]);
        array_unshift($this->follow, NULL, NULL);
    }

    private function addInfo($level, $dir)
    {
        $this->info[$level][iIndexer::INFO_NS] = IndexTools::getPageId($this->info[$level - 1][iIndexer::INFO_NS], $dir);
        $this->info[$level][iIndexer::INFO_DIR] = $this->info[$level - 1][iIndexer::INFO_DIR] . '/' . $NS_SEPARATOR . $dir;
    }


    public function getIndex()
    {
        $index = array();
        $this->search($index, 1);

        return $index;
    }

    private function search(&$index, $level)
    {
        $namespace = $this->info[$level][iIndexer::INFO_NS];

        $index[$namespace][iIndexer::KEY_DIRS] = array();
        $index[$namespace][iIndexer::KEY_PAGES] = array();
        $index[$namespace][iIndexer::KEY_PAGES_TITLE] = array();

        $dh = @opendir($this->info[$level][iIndexer::INFO_DIR]);
        if (!$dh) return;

        while (($file = readdir($dh)) !== false) {

            if ( ! IndexTools::isFilePage($file))
                continue;

            $filePath = $this->info[$level][iIndexer::INFO_DIR] . '/' . $file;
            if (is_dir($filePath)) {
                $index[$namespace][iIndexer::KEY_DIRS][] = $file;
                continue;
            }
            $pagename = IndexTools::excludePageExtension($file);
            $index[$namespace][iIndexer::KEY_PAGES][] = $pagename;
            $title = p_get_first_heading(IndexTools::getPageId($this->info[$level][iIndexer::INFO_NS], $pagename));
            $index[$namespace][iIndexer::KEY_PAGES_TITLE][] = ($title != null) ? $title : $pagename;
        }
        closedir($dh);

        if (($level < $this->config->getAttribute('openDepth')) || $this->info[$level][iIndexer::INFO_FOLLOW]) {
            foreach ($index[$namespace][iIndexer::KEY_DIRS] as $subdir) {
                $isFollow = $this->checkFollowPath($subdir, $level + 1);
                if ($isFollow) {
//                    $data[$namespace][iIndexer::KEY_FOLLOW] = true;
                }
                if (($level < $this->config->getAttribute('openDepth')) || $isFollow) {
                    $this->addInfo($level + 1, $subdir);
                    $this->search($index, $level + 1);
                }
            }
        }
    }

}