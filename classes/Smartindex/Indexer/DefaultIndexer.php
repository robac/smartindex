<?php

namespace Smartindex\Indexer;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\PageTools;

class DefaultIndexer implements \Smartindex\Indexer\iIndexer
{
    private $config;
    private $info;
    private $follow;

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
        $this->info[1][iIndexer::INFO_NS] = $this->config->namespace;
        $this->info[1][iIndexer::INFO_DIR] = PageTools::getPageDirFromNamespace($this->config->baseDir, $this->config->namespace);
        $this->info[1][iIndexer::INFO_FOLLOW] = true;

        $this->follow = explode(PageTools::$NS_SEPARATOR, $this->config->followPath);
        unset($this->follow[count($this->follow) - 1]);
        array_unshift($this->follow, NULL, NULL);
    }

    private function addInfo($level, $dir)
    {
        $this->info[$level][iIndexer::INFO_NS] = PageTools::constructPageName($this->info[$level - 1][iIndexer::INFO_NS], $dir);
        $this->info[$level][iIndexer::INFO_DIR] = $this->info[$level - 1][iIndexer::INFO_DIR] . '/' . $NS_SEPARATOR . $dir;
    }


    public function getIndex(IndexConfiguration $config)
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
            if (preg_match('/^[\._]/', $file)) continue;
            $filePath = $this->info[$level][iIndexer::INFO_DIR] . '/' . $file;
            if (is_dir($filePath)) {
                $index[$namespace][iIndexer::KEY_DIRS][] = $file;
                continue;
            }
            $pagename = PageTools::excludePageExtension($file);
            $index[$namespace][iIndexer::KEY_PAGES][] = $pagename;
            $title = p_get_first_heading(PageTools::constructPageName($this->info[$level][iIndexer::INFO_NS], $pagename));
            $index[$namespace][iIndexer::KEY_PAGES_TITLE][] = ($title != null) ? $title : $pagename;
        }
        closedir($dh);
        /*        array_multisort(array_map('strtolower', $data[$namespace][iIndexer::KEY_PAGES_TITLE]), SORT_STRING,
                                $data[$namespace][iIndexer::KEY_PAGES], SORT_STRING);*/

        if (($level < $this->config->openDepth) || $this->info[$level][iIndexer::INFO_FOLLOW]) {
            foreach ($index[$namespace][iIndexer::KEY_DIRS] as $subdir) {
                $isFollow = $this->checkFollowPath($subdir, $level + 1);
                if ($isFollow) {
//                    $data[$namespace][iIndexer::KEY_FOLLOW] = true;
                }
                if (($level < $this->config->openDepth) || $isFollow) {
                    $this->addInfo($level + 1, $subdir);
                    $this->search($index, $level + 1);
                }
            }
        }
    }

}