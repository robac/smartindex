<?php

namespace Smartindex\Factory;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Exception\ConfigurationException;
use Smartindex\Exception\ThemeException;
use Smartindex\Manager\ThemeManager;


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

        return new $theme_info['syntaxRenderer']($config);
    }

    public static function getIndexRenderer(IndexConfiguration $config, $loadTheme = true) {
        self::checkTheme($config, $loadTheme);
        $theme_info = $config->getAttribute('theme-info');
        if (array_key_exists('indexRendererPath', $theme_info)) {
            include_once($theme_info['indexRendererPath']);
        }

        return new $theme_info['indexRenderer']($config);
    }
}