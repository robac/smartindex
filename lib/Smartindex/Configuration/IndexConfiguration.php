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
        'followPath' => '',
        'indexId' => NULL,
        'nsFrontPage' => 'start',
        'theme' => 'default',
        'ajaxDepth' => 1,
        'openDepth' => 1,
        'loadLevel' => 1,
        'indexClass' => '',
        'showMain' => false,
        'syntaxRenderer' => NULL,
        'theme-info' => NULL,
        'indexRenderer' => NULL,
        'namespacesFirst' => true,
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

        /*if (is_null($this->ajaxDepth)) {
            $this->ajaxDepth = $this->load;
        }*/

        if (is_null($this->attributes['theme-info'])) {
            $this->loadTheme();
        }

        if ($this->attributes['loadLevel'] < 1) {
            throw new ConfigurationException("invalid attribute loadLevel $this->attributes['loadLevel'].");
        }
    }

    public function loadTheme() {
        $manager = new ThemeManager($this);
        $this->attributes['theme-info'] = $manager->getThemeInfo($this->attributes['theme']);
        $this->attributes['indexClass'] = $this->attributes['theme-info']['css-class'];
    }
}