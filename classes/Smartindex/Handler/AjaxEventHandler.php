<?php

namespace Smartindex\Handler;

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Factory\RendererFactory;
use Smartindex\Indexer\DefaultIndexer;

class AjaxEventHandler implements iEventHandler
{
    private $response;

    protected function action_save_namespace_order($input) {
        $this->response = array(
            "Hello" => "Inv"
        );
    }

    protected function action_render_subtree($input) {
        global $conf;
        try {
            $config = new IndexConfiguration(array(
                'namespace' => $input->str('namespace'),
                'openDepth' => $input->str('depth'),
                'theme'     => $input->str('theme'),
            ));
            $config->loadTheme();
            $config->validate();
        } catch (\Exception $e) {
            $this->setErrorResponse("Martindex configuration error: $config->error");
            return;
        }

        $renderer = RendererFactory::getIndexRenderer($config);
        $renderer->render($data);

        $this->response = array(
            'status' => 'success',
            'index' => $data
        );
    }

    private function setErrorResponse($message) {
        $this->response = array(
            'status' => 'error',
            'error' => $message
        );
    }


    public function handle(\Doku_Event &$event, $param) {

        global $INPUT;
        //security token check
        if ($INPUT->str('sectoken') !== getSecurityToken()) {
            $this->setErrorResponse("CSRF protection.");
        } else {
            $action = $INPUT->str('action');

            switch ($action) {
                case 'save_namespace_order':
                    $this->action_save_namespace_order($INPUT);
                    break;
                case 'render_subtree':
                    $this->action_render_subtree($INPUT);
                    break;

                default:
                    $this->setErrorResponse("Invalid action: $action.");
            }
        }
    }

    public function outputResponse() {
        $json = new \JSON();
        header('Content-Type: application/json');
        echo $json->encode($this->response);
    }

}