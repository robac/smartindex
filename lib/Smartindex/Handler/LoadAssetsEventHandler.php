<?php

namespace Smartindex\Handler;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Factory\RendererFactory;
use Smartindex\Index\DefaultIndexBuilder;

class LoadAssetsEventHandler implements iEventHandler
{
    private $action;
    private $assets = array();

    public function __construct($action) {
        $this->action = $action;
    }

    private function addAsset($type, $path) {
        $this->assets[] = array (
            'type' => $type,
            'path' => $path,
        );
    }

    public function handle(\Doku_Event &$event, $param) {

        global $conf;

        $base_url   = DOKU_BASE . 'lib/plugins/smartindex/assets';

        # Load Font-Awesome (skipped for Bootstrap3 template)
        if ($this->action->getConf('loadContextMenu')) {
            $this->addAsset('css', "$base_url/context-menu/css/jquery.contextMenu.min.css");
            $this->addAsset('js', "$base_url/context-menu/jquery.contextMenu.min.js");
            $this->addAsset('js', "$base_url/context-menu/jquery.ui.position.js");
        }

        if ($this->action->getConf('loadFontAwesomeCSS')) {
            $this->addAsset('css', "$base_url/font-awesome/css/font-awesome.min.css");
        }

        if ($this->action->getConf('hookSitemap')) {
            $this->addAsset('js', "$base_url/sitemap/sitemap.js");
        }

        foreach ($this->assets as $asset) {
            if ($asset['type'] == 'css') {
                $event->data['link'][] = array (
                    'type'    => 'text/css',
                    'rel'     => 'stylesheet',
                    'href'    => $asset['path'],
                );
            } elseif ($asset['type'] == 'js') {
                $event->data["script"][]  = array (
                    'type'    => 'text/javascript',
                    'src'    => $asset['path'],
                    'defer' => 'defer',
                    "_data" => "",
                );
            } else {
                continue;
            }
        }

    }
}