<?php

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Indexer\DefaultIndexer;
use Smartindex\Utils\HtmlHelper;

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


    public function handle($match, $state, $pos, &$handler){
        try {
            $config = \Smartindex\Configuration\TagAttributes::createConfigurationFromTag($match);
            $config->setAttribute('followPath', $INFO['id']);
            $config->validate();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
        return serialize($config);
    }
    
    public function removeDirs($dir, $filename) {
        return str_replace("\\", ":", substr($filename, strlen($dir)+1));
    }
    
    public function renderError(&$document, $error) {
        $document .= '<div class="smartindex-error">';
        $document .= '<strong>SmartIndex error:</strong><br/>';
        $document .= $error;
        $document .= '</div>';
    }
    
    public function render($mode, &$renderer, $data) {
        if($mode != 'xhtml') 
            return false;

        global $conf;
        global $INFO;
        
        $config = unserialize($data);
        
        if (($config->target=='desktop' && $INFO['ismobile']===true))
            return true;
        
        
        
        $config->setAttribute('followPath', $INFO['id']);
        $config->checkRender();
        if (is_null($this->error)) {
            $seeker = new DefaultIndexer($config);
            $pages = $seeker->getIndex($config);
        } else {
            $this->renderError($renderer->doc, $this->error);
            return true;
        }
        
        $indexBuilder = $config->getRenderer();
        
        $indexBuilder->setWrapper(true, $config->getAttribute('treeId'));
        $indexBuilder->render($pages, $renderer->doc);
        
        $ajaxConfig = new stdClass();
        $ajaxConfig->url  = AJAX_URL;
        $ajaxConfig->depth = $config->getAttribute('ajaxDepth');
        $ajaxConfig->theme = $config->getAttribute('theme');

        $renderer->doc .= HtmlHelper::createInlineScript(HtmlHelper::createInlineJSON($config->getAttribute('treeId')."_conf", $ajaxConfig));
        return true;
    }
}
