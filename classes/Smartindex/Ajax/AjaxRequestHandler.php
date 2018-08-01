<?php

namespace Smartindex\Ajax;


class AjaxRequestHandler
{
    private $response;

    protected function action_save_namespace_order($input) {
        $this->response = array(
            "Hello" => "Inv"
        );
    }

    protected function action_render_subtree($input) {
        global $conf;
        $index = "";
        $config = new \Smartindex\Configuration\IndexConfiguration();
        $config->namespace = $input->str('namespace');
        $config->openDepth = $input->str('depth');
        $config->theme = $input->str('theme');;
        $config->checkHandle();
        $config->checkRender();
        /*if (!is_null($config->error)) {
            $this->setErrorResponse("Martindex configuration error: $config->error");
        } else {*/
            $indexer = new \Smartindex\Indexer\DefaultIndexer($config);
            $data = $indexer->getIndex($config);

            $renderer = $config->getRenderer();
            $renderer->setWrapper(false);
            $renderer->render($data, $index);

            $this->response = array(
                'status' => 'success',
                'index' => $index
            );
       // }

    }

    private function setErrorResponse($message) {
        $this->response = array(
            'status' => 'error',
            'error' => $message
        );
    }


    public function handle(\Doku_Event $event, $param) {

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