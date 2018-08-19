<?php
namespace Smartindex\Manager;

use Twig_Loader_Filesystem;
use Twig_Environment;

class TemplateManager {
    public static function getTemplate($path, array $functions = NULL) {
        $templateDir = TEMPLATES_DIR . dirname($path);
        $file = basename($path);

        $loader = new Twig_Loader_Filesystem($templateDir);
        $twig = new Twig_Environment($loader, array(
            'cache' => TEMPLATESCACHE_DIR,
        ));

        if ( ! is_null($functions)) {
            foreach ($functions as $name => $fnReference) {
                $function = new \Twig_Function($name, $fnReference);
                $twig->addFunction($function);
            }
        }

        return $twig->load($file);
    }
}