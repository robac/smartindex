<?php

namespace Smartindex\Configuration;

use Smartindex\Exception\ConfigurationException;
use Smartindex\Manager\ThemeManager;
use Smartindex\Renderer\DefaultIndexRenderer;
use Smartindex\Utils\Utils;

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
        'indexId' => NULL,
        'nsFrontPage' => 'start',
        'theme' => 'default',
        'ajaxDepth' => 1,
        'openDepth' => 1,
        'indexClass' => '',
        'showMain' => false,
        'syntaxRenderer' => NULL,
        'indexRenderer' => NULL
    );

    private $themesInfo;


    public function __construct(Array $attributes = NULL)
    {
        if ( ! is_null($attributes)) {
            $this ->setAttributes($attributes);
        }

        $this->attributes['indexId'] = Utils::generateIndexId();
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


    public function validate($include_renderers = true) {
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

        if ($include_renderers && (is_null($this->attributes['syntaxRenderer']))) {
            throw new ConfigurationException("there is not syntax renderer.");
        }

        if ($include_renderers &&(is_null($this->attributes['indexRenderer']))) {
            throw new ConfigurationException("there is not index renderer.");
        }
    }

    public function loadTheme() {
        $manager = new ThemeManager($this);
        $theme_info = $manager->getThemeInfo($this->attributes['theme']);
        $this->attributes['syntaxRenderer'] = new $theme_info['syntaxRenderer']($this);
        $this->attributes['indexRenderer'] = new $theme_info['indexRenderer']($this);
        $this->attributes['indexClass'] = $theme_info['css-class'];
    }
}