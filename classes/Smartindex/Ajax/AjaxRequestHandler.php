<?php

namespace Smartindex\Ajax;


class AjaxRequestHandler
{
    protected static function action_save_namespace_order() {
        return array("Hello" => "Inv");
    }


    public static function handle(\Doku_Event $event, $param) {

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
                    $response = self::action_save_namespace_order($INPUT);
                    break;
                default:
                    $response = array(
                        'status' => 'error',
                        'error' => "Invalid action: $action");
            }
        }

        $json = new \JSON();
        header('Content-Type: application/json');
        echo $json->encode($response);
    }

}