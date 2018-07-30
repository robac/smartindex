<?php

class SmartIndexConf {
    const TREE_CLASS = 'smartindex-treeview';
    const HIGHLITE_CLASS = 'smartindex-highlite';
    const THEME_CLASS_PATTERN = 'smartindex-{theme}-theme';
    
    const PARAM_NAMESPACE = 'namespace';
    const PARAM_NS_FRONT_PAGE = 'nsfrontpage';
    const PARAM_HIGHLITE = 'highlite';
    const PARAM_THEME = 'theme';
    const PARAM_AJAX_DEPTH = 'ajaxdepth';
    const PARAM_OPEN_DEPTH = 'opendepth';
    const SHOW_MAIN_NAMESPACE='showmain';
    
    
    public $namespace = '';
    public $highlite = true;
    public $baseDir = NULL;
    public $followPath = '';
    public $treeId = NULL;
    public $nsFrontPage = 'start';
    public $theme = 'default';
    public $ajaxDepth = 2;
    public $openDepth=0;
    public $cssClass;
    public $target='both';
    public $showMain = false;
    
    public $error = NULL;
    
    private $themesInfo;
    
    public function readFromTag($match) {
        $params = substr($match, 12, strlen($match)-14);
        preg_match_all('/([a-zA-Z\-]+)\s*=\s*"([^"]*)"/i',$params, $res, PREG_SET_ORDER);
        foreach ($res as $val) {
            switch (strtolower($val[1])) {
                case self::PARAM_NAMESPACE:
                    $this->namespace = $val[2];
                    break;
                
                case self::PARAM_NS_FRONT_PAGE:
                    $this->nsFrontPage = $val[2];
                    break;
                    
                case self::PARAM_THEME:
                    $this->theme = $val[2];
                    break;
                    
                case self::PARAM_HIGHLITE:
                    $this->highlite = \Smartindex\Utils\Utils::parseBoolean($val[2]);
                    break;
                
                case self::SHOW_MAIN_NAMESPACE:
                    $this->showMain = \Smartindex\Utils\Utils::parseBoolean($val[2]);
                    break;
                
                case self::PARAM_OPEN_DEPTH:
                    $this->openDepth = $val[2];
                    break;
                
                case self::PARAM_AJAX_DEPTH:
                    $this->ajaxDepth = $val[2];
                    break;
                
                case self::TARGET:
                    $this->target = $val[2];
                    break;
                
                default:
                    $this->error = "invalid param {$val[1]}";
            }
            
        }
    }
    
    public function checkHandle() {
        global $conf;
        
        if (is_null($this->baseDir)) {
            $this->baseDir = $conf['datadir'];
        }
        
        if ($this->openDepth < 1) {
            $this->error = "Chyba recLevel";
        }
        
        if (is_null($this->highlite)) {
            $this->error = "chyba highlite";
        }
        
        if (is_null($this->ajaxDepth)) {
            $this->ajaxDepth = $this->openDepth;
        }
        
    }
    
    private function loadThemesInfo() {
        $this->themesInfo = unserialize(file_get_contents(SMARTINDEX_DIR.'theme.dat'));
    }
    
    public function getRenderer() {
        if (!isset($this->themesInfo[$this->theme][ThemesCollector::KEY_RENDERER_C])) {
            return new \Smartindex\Renderer\DefaultRenderer($this);
        } else {
            require_once(THEMES_DIR.$this->theme.'/'.$this->themesInfo[$this->theme][ThemesCollector::KEY_RENDERER_F]);
            //return new $this->themesInfo[$this->theme][ThemesCollector::KEY_RENDERER_C]($this);
        }
    }
    
    public function checkRender() {
        if (is_null($this->treeId)) {
            $this->treeId = \Smartindex\Utils\Utils::getFloatMicrotime("smartindex_");
        }
        
        $this->loadThemesInfo();
        if (!isset($this->themesInfo[$this->theme])) {
            $this->error = "nezname tema";
        }
        
        $this->cssClass = $this->themesInfo[$this->theme][ThemesCollector::KEY_CSS];
    }
    
}