<?php

namespace Smartindex\Factory;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Exception\ConfigurationException;
use Smartindex\Exception\ThemeException;
use Smartindex\Manager\ThemeManager;
use Smartindex\Renderer\iIndexRenderer;
use Smartindex\Renderer\iRenderer;


class RendererFactory {

    private static function checkTheme(IndexConfiguration $config, $loadTheme) {
        if (is_null($config->getAttribute('theme-info'))) {
            if ($loadTheme) {
                $manager = new ThemeManager($config);
                $config->setAttribute('theme-info', $manager->getThemeInfo($config->getAttribute('theme')));
            } else {
                throw new ThemeException('Missing theme info.');
            }
        }
    }

    public static function getSyntaxRenderer(IndexConfiguration $config, $loadTheme = true) {
        self::checkTheme($config, $loadTheme);
        $theme_info = $config->getAttribute('theme-info');
        if (array_key_exists('syntaxRendererPath', $theme_info)) {
            include_once($theme_info['syntaxRendererPath']);
        }

        $class = $theme_info['syntaxRenderer'];
        $renderer = new $class($config);
        if ($renderer instanceof iRenderer) {
            return $renderer;
        } else {
            throw new ThemeException("Class $class doesn\'t implements interface iRenderer.");
        }
    }

    public static function getIndexRenderer(IndexConfiguration $config, $loadTheme = true) {
        self::checkTheme($config, $loadTheme);
        $theme_info = $config->getAttribute('theme-info');
        if (array_key_exists('indexRendererPath', $theme_info)) {
            include_once($theme_info['indexRendererPath']);
        }

        $class = $theme_info['indexRenderer'];
        $renderer = new $class($config);
        if ($renderer instanceof iIndexRenderer) {
            return $renderer;
        } else {
            throw new ThemeException("Class $class doesn\'t implements interface iIndexRenderer.");
        }
    }
}