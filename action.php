<?php
if (!defined('DOKU_INC')) die();

require_once 'inc.php';

use Smartindex\Handler\AjaxEventHandler;
use Smartindex\Handler\LoadAssetsEventHandler;

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

        $handler = new AjaxEventHandler();
        $handler->handle($event, $param);
        $handler->outputResponse();
    }

    protected function save_namespace_order() {
        return array("Hello" => "Inv");
    }

    public function _loadassets(Doku_Event &$event, $param) {
        $handler = new LoadAssetsEventHandler($this);
        $handler->handle($event, $param);
    }
}