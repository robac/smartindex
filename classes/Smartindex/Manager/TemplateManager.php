<?php
namespace Smartindex\Manager;

use Twig_Loader_Filesystem;
use Twig_Environment;

class TemplateManager {
    public static function getTemplate($path) {
        $templateDir = TEMPLATES_DIR . dirname($path);
        $file = basename($path);

        $loader = new Twig_Loader_Filesystem($templateDir);
        $twig = new Twig_Environment($loader, array(
            'cache' => TEMPLATESCACHE_DIR,
        ));
        $temp = $twig->load($file);

        return $twig->load($file);
    }
}