<?php

class ThemesCollector {
    const CONF_THEME = 'theme';
    const CONF_CSS_CLASS = 'css-class';
    const CONF_RENDERER_FILE = 'renderer-file';
    const CONF_RENDERER_CLASS = 'renderer-class';
    
    const KEY_CSS = 'css';
    const KEY_RENDERER_F = 'rend-f';
    const KEY_RENDERER_C = 'rend-c';
    
    private function loadCSSFile($file, $location = NULL) {
        if(!@file_exists($file)) 
            return '';
        $css = io_readFile($file);
        if(is_null($location))
            return $css;

        $css = preg_replace('#(url\([ \'"]*)(?!/|data:|http://|https://| |\'|")#','\\1'.$location, $css);
        $css = preg_replace('#(@import\s+[\'"])(?!/|data:|http://|https://)#', '\\1'.$location, $css);

        return $css;
    } 
    
    private function loadJsFile($file) {
        if(!@file_exists($file)) 
            return '';

        return io_readFile($file);
    }
    
    private function createThemeConf($themeConf, $dir, &$themes) {
        if ($themeConf[self::CONF_THEME] != $dir) {
            return false;
        } else {
            $theme = $themeConf[self::CONF_THEME];
        }
        $themeInfo = array();
        
        if (!isset($themeConf[self::CONF_CSS_CLASS])) {
            return false;
        }
        $themeInfo[self::KEY_CSS] = $themeConf[self::CONF_CSS_CLASS];
        
        if (isset($themeConf[self::CONF_RENDERER_CLASS])) {
            $themeInfo[self::KEY_RENDERER_C] = $themeConf[self::CONF_RENDERER_CLASS];
            if (isset($themeConf[self::CONF_RENDERER_FILE])) {
                $themeInfo[self::KEY_RENDERER_F] = $themeConf[self::CONF_RENDERER_FILE];
            } else {
                $themeInfo[self::KEY_RENDERER_F] = $themeConf[self::CONF_RENDERER_CLASS].'.php';
            }
            
            if (!file_exists(THEMES_DIR.$dir.'/'.$themeInfo[self::KEY_RENDERER_F])) {
                return false;
            }
        }
        
        $themes[$theme] = $themeInfo;
        return true;
    }
    
    private function addTheme($dirPath, $dir, $jsCollection, $cssCollection, &$themes) {
        if (!file_exists($dirPath."/theme.info.txt"))
            return;
        
        $themeConf = confToHash($dirPath."/theme.info.txt", true);
       
        if (!$this->createThemeConf($themeConf, $dir, $themes)) {
            return;
        }
        
        $this->addJsFile($jsCollection, $dirPath.'/script.js', $dir);
        $this->addCssFile($cssCollection, $dirPath.'/screen.css', $dir);
    }
    
    private function addCssFile($collection, $filePath, $dir=NULL) {
        if (file_exists($filePath)) {
            if (is_null($dir)) {
                $css = $this->loadCSSFile($filePath);
            } else {
                $css = $this->loadCSSFile($filePath, THEMES_URL.$dir.'/');
            }
            @fwrite($collection, DOKU_LF.$css);
        }
    }
    
    private function addJsFile($collection, $filePath) {
        if (file_exists($filePath)) {
                $js = $this->loadJsFile($filePath, THEMES_URL.$dir.'/');
        }

        @fwrite($collection, DOKU_LF.$js);
    }
    
    public function collect() {
        $jsCollection = fopen(SMARTINDEX_DIR.'script.js', 'w');
        $cssCollection = fopen(SMARTINDEX_DIR.'screen.css', 'w');
        
        $this->addJsFile($jsCollection, THEMES_DIR.'script.js');
        $this->addCssFile($cssCollection, THEMES_DIR.'screen.css');
        
        $themes = array();
        $dh = @opendir(THEMES_DIR);
        if (!$dh) return;
        while(($file = readdir($dh)) !== false) {
            if(preg_match('/^[\._]/',$file)) 
                    continue;
            $filePath = THEMES_DIR.$file;
            if(is_dir($filePath)){
                $this->addTheme($filePath, $file, $jsCollection, $cssCollection, $themes);
            }
        }
        closedir($dh);
        @fflush($jsCollection);
        @fclose($jsCollection);
        @fflush($cssCollection);
        @fclose($cssCollection);
        
        $infoFile = fopen(SMARTINDEX_DIR.'theme.dat', 'w');
        @fwrite($infoFile, serialize($themes));
        @fflush($infoFile);
        @fclose($infoFile);
    }
}
