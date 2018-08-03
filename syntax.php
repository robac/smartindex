<?php

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Indexer\DefaultIndexer;
use Smartindex\Utils\HtmlHelper;
use Smartindex\Configuration\TagAttributes;
use Monotek\MiniTPL\Template;
use Smartindex\Factory\RendererFactory;
use Smartindex\Renderer\SyntaxRenderer;

require_once (dirname(__FILE__).'/inc.php');
INC_requireDW();
INC_constsDW();
INC_constsSmartindex();

class syntax_plugin_smartindex extends DokuWiki_Syntax_Plugin {
    private $error = NULL;

    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'block';
    }

    public function getSort() {
        return 100;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<smartindex(?i:\s+[a-zA-Z\-]+\s*=\s*"[^"]*")*\s*/>', $mode, 'plugin_smartindex');
    }


    public function handle($match, $state, $pos, Doku_Handler $handler){
        global $INFO;

        try {
            $config = TagAttributes::createConfigurationFromTag($match, array(
                'followPath' => $INFO['id'],
            ));
            $config->loadTheme();
            $config->validate();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return serialize($config);
    }
    
    public function render($mode, Doku_Renderer $doku_renderer, $data) {
        global $conf;
        global $INFO;

        if($mode != 'xhtml')
            return false;

        $config = unserialize($data, array(
            'IndexConfiguration'
        ));
        

        if ( ! is_null($this->error)) {
            $this->renderError($doku_renderer->doc, $this->error);
            return true;
        }

        $renderer = RendererFactory::getSyntaxRenderer($config);
        $renderer->render($doku_renderer->doc);

        return true;
    }

    private function renderError(&$document, $error) {
        $template = new Template(TEMPLATES_DIR);
        $template->load("error.tpl");
        $template->assign("error", $error);
        $document .= $template->get();
    }
}
