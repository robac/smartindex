<?php

namespace Smartindex\Configuration;
use ThemesCollector;

class IndexConfiguration
{
    const TREE_CLASS = 'smartindex-treeview';
    const HIGHLITE_CLASS = 'smartindex-highlite';
    const THEME_CLASS_PATTERN = 'smartindex-{theme}-theme';

    private $tagAttributes = array(
        TagAttributes::_NAMESPACE => array('namespace', 'string'),
        TagAttributes::NS_FRONT_PAGE => array('nsFrontPage', 'string'),
        TagAttributes::HIGHLIGHT => array('highlight', 'boolean'),
        TagAttributes::THEME => array('theme', 'string'),
        TagAttributes::AJAX_DEPTH => array('ajaxDepth', 'numeric'),
        TagAttributes::OPEN_DEPTH => array('openDepth', 'numeric'),
        TagAttributes::SHOW_MAIN_NAMESPACE => array('showMain', 'string'),
    );

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
        'target' => 'both',
        'showMain' => false
    );

    public $error = NULL;

    private $themesInfo;

    protected $validationError = NULL;

    public function __construct(Array $attributes = NULL)
    {
        if ( ! is_null($attributes)) {
            $this ->setAttributes($attributes);
        }
    }

    public function getValidationError() {
        return $this->validationError;
    }

    public function setAttributes(Array $attributes) {
        foreach ($attributes as $k => $v) {
            if (array_key_exists($k, $this->attributes)) {
                $this->attributes[$k] = $v;
            } else {
                throw new \Smartindex\Exception\ConfigurationException("Invalid configuration attribute: $k");
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
            throw new \Smartindex\Exception\ConfigurationException("Invalid configuration attribute: $name");
        }
    }

    public function setAttributesFromTag($match)
    {
        $params = substr($match, 12, strlen($match) - 14);
        preg_match_all('/([a-zA-Z\-]+)\s*=\s*"([^"]*)"/i', $params, $res, PREG_SET_ORDER);

        foreach ($res as $val) {
            $tag_attr = $val[1];
            $value = $val[2];

            if ( ! array_key_exists($tag_attr, $this->tagAttributes)) {
                throw new \Smartindex\Exception\ConfigurationException("unknown tag attribute $tag_attr");
            }

            $conf_attr = $this->tagAttributes[$tag_attr][0];
            $conf_type = $this->tagAttributes[$tag_attr][1];

            if ( ! array_key_exists($conf_attr, $this->attributes)) {
                throw new \Smartindex\Exception\ConfigurationException("unknown internal attribute $conf_attr");
            }

            switch($conf_type) {
                case 'string':
                    $this->attributes[$conf_attr] = $value;
                    break;
                case 'booolean':
                    $this->attributes[$conf_attr] = \Smartindex\Utils\Utils::parseBoolean($value);
                    break;
                case 'numeric':
                    $this->attributes[$conf_attr] = \Smartindex\Utils\Utils::parseNumeric($value);
                    break;
                default:
                    throw new \Smartindex\Exception\ConfigurationException("unknown attribute type $conf_type");
            }
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
            throw new \Smartindex\Exception\ConfigurationException("invalid attribute openDepth $this->attributes['openDepth'].");
        }
    }


    private function loadThemesInfo()
    {
        $this->themesInfo = unserialize(file_get_contents(SMARTINDEX_DIR . 'theme.dat'));
    }

    public function getRenderer()
    {
        return new \Smartindex\Renderer\DefaultRenderer($this);
    }

    public function checkRender()
    {
        if (is_null($this->attributes['treeId'])) {
            $this->attributes['treeId'] = \Smartindex\Utils\Utils::getFloatMicrotime("smartindex_");
        }

        $this->loadThemesInfo();
        if ( ! isset($this->themesInfo[$this->attributes['theme']])) {
            $this->error = "nezname tema";
        }

        $this->attributes['cssClass'] = $this->themesInfo[$this->attributes['theme']][ThemesCollector::KEY_CSS];
    }

}