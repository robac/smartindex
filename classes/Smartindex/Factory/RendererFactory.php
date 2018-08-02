<?php

namespace Smartindex\Factory;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Manager\ThemeManager;


class RendererFactory
{
    public static function getSyntaxRenderer(IndexConfiguration $config) {
        $manager = new ThemeManager();
        $theme_info = $manager->getThemeInfo($config->getAttribute('theme'));
        return new $theme_info['syntaxRenderer']($config);
    }

    public static function getIndexRenderer(IndexConfiguration $config) {
        $manager = new ThemeManager();
        $theme_info = $manager->getThemeInfo($config->getAttribute('theme'));
        return new $theme_info['indexRenderer']($config);
    }
}