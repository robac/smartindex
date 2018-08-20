<?php

namespace Smartindex\Configuration;

use Smartindex\Exception\ConfigurationException;
use Smartindex\Utils\Utils;

class TagAttributes
{
    private static $tagAttributes = array(
        'namespace' => array('namespace', 'string'),
        'nsFrontPage' => array('nsFrontPage', 'string'),
        'highlight' => array('highlight', 'boolean'),
        'theme' => array('theme', 'string'),
        'ajaxDepth' => array('ajaxDepth', 'numeric'),
        'openDepth' => array('openDepth', 'numeric'),
        'loadLevel' => array('loadLevel', 'numeric'),
        'showMain' => array('showMain', 'string'),
        'namespacesFirst' => array('namespacesFirst', 'boolean'),
    );


    public static function createConfigurationFromTag($match, $additional = NULL) {
        $attributes = array();

        $params = substr($match, 12, strlen($match) - 14);
        preg_match_all('/([a-zA-Z\-]+)\s*=\s*"([^"]*)"/i', $params, $res, PREG_SET_ORDER);

        foreach ($res as $val) {
            $tag_attr = $val[1];
            $value = $val[2];

            if ( ! array_key_exists($tag_attr, self::$tagAttributes)) {
                throw new ConfigurationException("unknown tag attribute $tag_attr");
            }

            $conf_attr = self::$tagAttributes[$tag_attr][0];
            $conf_type = self::$tagAttributes[$tag_attr][1];

            switch($conf_type) {
                case 'string':
                    $attributes[$conf_attr] = $value;
                    break;
                case 'boolean':
                    $attributes[$conf_attr] = Utils::parseBoolean($value);
                    break;
                case 'numeric':
                    $attributes[$conf_attr] = Utils::parseNumeric($value);
                    break;
                default:
                    throw new ConfigurationException("unknown attribute type $conf_type");
            }
        }

        if ( ! is_null($additional)) {
            $attributes = array_merge($attributes, $additional);
        }

        return new IndexConfiguration($attributes);
    }

}