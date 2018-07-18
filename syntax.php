<?php
require_once (dirname(__FILE__).'/inc.php');
INC_requireDW();
INC_constsDW();
INC_constsSmartindex();

class syntax_plugin_smartindex extends DokuWiki_Syntax_Plugin {
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
        $config = new SmartIndexConf();
        $config->readFromTag($match);
        $config->followPath = $INFO['id']; 
        $config->checkHandle();
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
        
        
        
        $config->followPath = $INFO['id'];
        $config->checkRender();
        if (is_null($config->error)) {
            $seeker = new PageSeeker($config);
            $pages = $seeker->get($config);
        } else {
            $this->renderError($renderer->doc, $config->error);
            return true;
        }
        
        $indexBuilder = $config->getRenderer();
        
        $indexBuilder->setWrapper(true, $config->treeId);
        $indexBuilder->render($pages, $renderer->doc);
        
        $ajaxConfig = new stdClass();
        $ajaxConfig->url  = AJAX_URL;
        $ajaxConfig->handleSubTreeLoad = 'si_dtree_handleSubTreeLoad';
        $ajaxConfig->depth = $config->ajaxDepth;
        
        $ajaxConfig->rawEvents = array();
        $ajaxConfig->rawEvents[0]['selector'] = 'li.namespace.closed > div';
        $ajaxConfig->rawEvents[0]['fn'] = 'si_default_openFolder';
        $ajaxConfig->rawEvents[0]['event'] = 'click';
        
        $ajaxConfig->rawEvents[1]['selector'] = 'li.namespace.open > div';
        $ajaxConfig->rawEvents[1]['fn'] = 'si_default_closeFolder';
        $ajaxConfig->rawEvents[1]['event'] = 'click';
        
        $ajaxConfig->rawEvents[3]['selector'] = 'li.page > div';
        $ajaxConfig->rawEvents[3]['fn'] = 'si_default_openPage';
        $ajaxConfig->rawEvents[3]['event'] = 'click';

        $ajaxConfig->theme = $config->theme;


        
        $renderer->doc .= HtmlHelper::createInlineScript(HtmlHelper::createInlineJSON($config->treeId."_conf", $ajaxConfig));
        return true;
    }
}
