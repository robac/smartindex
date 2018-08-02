<?php

namespace Smartindex\Configuration;

use Smartindex\Exception\ConfigurationException;
use Smartindex\Renderer\DefaultIndexRenderer;
use Smartindex\Utils\Utils;
use ThemesCollector;

class IndexConfiguration
{
    const TREE_CLASS = 'smartindex-treeview';
    const HIGHLIGHT_CLASS = 'smartindex-highlite';
    const THEME_CLASS_PATTERN = 'smartindex-{theme}-theme';

    protected $attributes = array(
        'namespace' => '',
        'highlight' => true,
        'baseDir' => NULL,
        'followPath' => '',
        'treeId' => NULL,
        'nsFrontPage' => 'start',
        'theme' => 'default',
        'ajaxDepth' => 1,
        'openDepth' => 1,
        'cssClass' => '',
        'showMain' => false,
    );

    private $themesInfo;


    public function __construct(Array $attributes = NULL)
    {
        if ( ! is_null($attributes)) {
            $this ->setAttributes($attributes);
        }
    }


    public function setAttributes(Array $attributes) {
        foreach ($attributes as $k => $v) {
            if (array_key_exists($k, $this->attributes)) {
                $this->attributes[$k] = $v;
            } else {
                throw new ConfigurationException("Invalid configuration attribute: $k");
            }
        }
    }


    public function getAttribute($name) {
        return $this->attributes[$name];
    }


    public function setAttribute($name, $value) {
        if (array_key_exists($name, $this->attributes)) {
            $this->attributes[$name] = $value;
        } else {
            throw new ConfigurationException("Invalid configuration attribute: $name");
        }
    }


    public function validate() {
        global $conf;

        if (is_null($this->attributes['baseDir'])) {
            $this->attributes['baseDir'] = $conf['datadir'];
        }

        if (is_null($this->ajaxDepth)) {
            $this->ajaxDepth = $this->openDepth;
        }

        if ($this->attributes['openDepth'] < 1) {
            throw new ConfigurationException("invalid attribute openDepth $this->attributes['openDepth'].");
        }
    }


    private function loadThemesInfo()
    {
        $this->themesInfo = unserialize(file_get_contents(SMARTINDEX_DIR . 'theme.dat'));
    }

    public function getRenderer()
    {
        return new DefaultIndexRenderer($this);
    }

    public function checkRender()
    {
        if (is_null($this->attributes['treeId'])) {
            $this->attributes['treeId'] = Utils::getFloatMicrotime("smartindex_");
        }

        $this->loadThemesInfo();
        if ( ! isset($this->themesInfo[$this->attributes['theme']])) {
            $this->error = "nezname tema";
        }

        $this->attributes['cssClass'] = $this->themesInfo[$this->attributes['theme']][ThemesCollector::KEY_CSS];
    }

}