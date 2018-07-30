<?php

namespace Smartindex\Indexer;

use Smartindex\Configuration\SmartIndexConf;
use Smartindex\Utils\PageTools;

class DefaultIndexer implements \Smartindex\Indexer\iIndexer
{
    private $config;
    private $info;
    private $follow;

    public function __construct(SmartIndexConf $config)
    {
        $this->config = $config;
        $this->init();
    }

    private function checkFollowPath($namespace, $level)
    {
        if ($this->info[$level - 1][iIndexer::INFO_FOLLOW] && isset($this->follow[$level]) && ($this->follow[$level] == $namespace)) {
            $this->info[$level][self::INFO_FOLLOW] = true;
            return true;
        } else {
            return false;
        }
    }

    private function init()
    {
        $this->info = array();
        $this->info[1][self::INFO_NS] = $this->config->namespace;
        $this->info[1][self::INFO_DIR] = PageTools::getPageDirFromNamespace($this->config->baseDir, $this->config->namespace);
        $this->info[1][self::INFO_FOLLOW] = true;

        $this->follow = explode(PageTools::$NS_SEPARATOR, $this->config->followPath);
        unset($this->follow[count($this->follow) - 1]);
        array_unshift($this->follow, NULL, NULL);
    }

    private function addInfo($level, $dir)
    {
        $this->info[$level][self::INFO_NS] = PageTools::constructPageName($this->info[$level - 1][self::INFO_NS], $dir);
        $this->info[$level][self::INFO_DIR] = $this->info[$level - 1][self::INFO_DIR] . '/' . $NS_SEPARATOR . $dir;
    }


    public function getIndex(SmartIndexConf $config)
    {
        $index = array();
        $this->search($index, 1);

        return $index;
    }

    private function search(&$data, $level)
    {
        $namespace = $this->info[$level][self::INFO_NS];

        $data[$namespace][self::KEY_DIRS] = array();
        $data[$namespace][self::KEY_PAGES] = array();
        $data[$namespace][self::KEY_PAGES_TITLE] = array();

        $dh = @opendir($this->info[$level][self::INFO_DIR]);
        if (!$dh) return;

        while (($file = readdir($dh)) !== false) {
            if (preg_match('/^[\._]/', $file)) continue;
            $filePath = $this->info[$level][self::INFO_DIR] . '/' . $file;
            if (is_dir($filePath)) {
                $data[$namespace][self::KEY_DIRS][] = $file;
                continue;
            }
            $pagename = PageTools::excludePageExtension($file);
            $data[$namespace][self::KEY_PAGES][] = $pagename;
            $title = p_get_first_heading(PageTools::constructPageName($this->info[$level][self::INFO_NS], $pagename));
            $data[$namespace][self::KEY_PAGES_TITLE][] = ($title != null) ? $title : $pagename;
        }
        closedir($dh);
        /*        array_multisort(array_map('strtolower', $data[$namespace][self::KEY_PAGES_TITLE]), SORT_STRING,
                                $data[$namespace][self::KEY_PAGES], SORT_STRING);*/

        if (($level < $this->config->openDepth) || $this->info[$level][self::INFO_FOLLOW]) {
            foreach ($data[$namespace][self::KEY_DIRS] as $subdir) {
                $isFollow = $this->checkFollowPath($subdir, $level + 1);
                if ($isFollow) {
//                    $data[$namespace][self::KEY_FOLLOW] = true;
                }
                if (($level < $this->config->openDepth) || $isFollow) {
                    $this->addInfo($level + 1, $subdir);
                    $this->search($data, $level + 1);
                }
            }
        }
    }

}