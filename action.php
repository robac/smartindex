<?php
if (!defined('DOKU_INC')) die();

class action_plugin_smartindex extends DokuWiki_Action_Plugin
{


    function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, '_ajax_call');
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, '_loadassets');
    }

    function _ajax_call(Doku_Event $event, $param)
    {
        if ($event->data !== 'plugin_smartindex') {
            return;
        }

        $event->stopPropagation();
        $event->preventDefault();


        global $INPUT;
        if ($INPUT->str('sectoken') !== getSecurityToken()) {
            $response = array(
                'status' => 'error',
                'error' => "CSRF protection!"
            );
        } else {
           $action = $INPUT->str('action');

            switch ($action) {
                case 'save_namespace_order':
                    $response = $this->save_namespace_order($INPUT);
                    break;
                default:
                    $response = array(
                        'status' => 'error',
                        'error' => "Invalid action: $action");
            }
        }

        $json = new JSON();
        header('Content-Type: application/json');
        echo $json->encode($response);
    }

    protected function save_namespace_order() {
        return array("Hello" => "Inv");
    }

    public function _loadassets(Doku_Event &$event, $param) {

        global $conf;

        $base_url   = DOKU_BASE . 'lib/plugins/smartindex/assets';
        $font_icons = array();

        # Load Font-Awesome (skipped for Bootstrap3 template)
        if ($this->getConf('loadContextMenuCSS')) {
            $font_icons[] = "$base_url/context-menu/css/jquery.contextMenu.min.css";
        }

        if ($this->getConf('loadFontAwesomeCSS')) {
            $font_icons[] = "$base_url/font-awesome/css/font-awesome.min.css";
        }

        foreach ($font_icons as $font_icon) {
            $event->data['link'][] = array(
                'type'    => 'text/css',
                'rel'     => 'stylesheet',
                'href'    => $font_icon);
        }

    }
}