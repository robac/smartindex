<?php
namespace Smartindex\Manager;

use Smartindex\Exception\ThemeException;

class ThemeManager {
    private $builinThemes = array(
        'default' => array(
            'syntaxRenderer' => '\Smartindex\Renderer\SyntaxRenderer',
            'indexRenderer' => '\Smartindex\Renderer\DefaultIndexRenderer',
            'css-class' => 'smartindex-default-theme'
        ),
        'folder' => array(
            'syntaxRenderer' => '\Smartindex\Renderer\SyntaxRenderer',
            'indexRenderer' => '\Smartindex\Renderer\DefaultIndexRenderer',
            'css-class' => 'smartindex-folder-theme'
        ),
        'simple' => array(
            'syntaxRenderer' => '\Smartindex\Renderer\SyntaxRenderer',
            'indexRenderer' => '\Smartindex\Renderer\DefaultIndexRenderer',
            'css-class' => 'smartindex-simple-theme'
        ),
        /*'tree' => array(
            'syntaxRenderer' => '\Smartindex\Renderer\SyntaxRenderer',
            'indexRenderer' => '\Smartindex\Renderer\DefaultIndexRenderer',
            'css-class' => 'smartindex-tree-theme'
        )*/
    );

    public function getThemeInfo($theme) {
        if (array_key_exists($theme, $this->builinThemes)) {
            return $this->builinThemes[$theme];
        } else {
            $data['theme'] = $theme;
            trigger_event('PLUGIN_SMARTINDEX_GET_THEME', $data);
            if (array_key_exists('theme-info', $data)) {
                return $data['theme-info'];
            } else {
                throw new ThemeException('Invalid theme $theme.');
            }
        }
    }
}