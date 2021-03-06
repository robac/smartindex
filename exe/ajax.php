<?php
require_once (dirname(__FILE__).'/../inc.php');
INC_constsDW();
INC_includeDWCore();
INC_constsSmartindex();

$handler = new AjaxRequestHandler;
$handler->handle($_POST['action']);


class AjaxRequestHandler {
    const ACTION_RENDER_SUBTREE = 'load_namespace';
    const ACTION_COLLECT_THEMES = 'collect';
    const ACTION_PAGE_INFO      = 'pageinfo';

    private function handle_render_subtree() {
        global $conf;
        $res = "";
        $config = new SmartIndexConf();
        $config->namespace = $_POST['namespace'];
        $config->openDepth = $_POST['depth'];
        $config->theme = $_POST['theme'];
        $config->checkHandle();
        $config->checkRender();
        if (!is_null($config->error)) {
            $res .= "<div class=\"smartindex-error\">SmartIndex error: {$config->error}</div>";
            echo $res;
        } else {
            $seeker = new PageSeeker($config);
            $data = $seeker->get($config);

            $indexBuilder = $config->getRenderer();
            $indexBuilder->setWrapper(false);
            $indexBuilder->render($data, $res);
            echo $res;
        }
  
    }

    private function handle_admin() {
    
    }
    
    private function handle_collect_themes() {
        if (!auth_isadmin()) {
            echo "Just for administrators!";
        }

        $col = new ThemesCollector();
        $col->collect();
        echo "collected ok";
    }
    
    private function handle_page_info() {
        $page = $_POST['page'];

        
        $revisions = getRevisions($_POST['page'], 0, 10);
        foreach ($revisions as $revision) {
            echo "<pre>".var_dump(getRevisionInfo($_POST['page'], $y))."</pre>";
        }
        $meta = p_get_metadata($_POST['page']);
        $toc = $meta['description']['tableofcontents'];
        
    }
    
    public function handle($action) {
        switch ($action) {
            case self::ACTION_RENDER_SUBTREE:
                $this->handle_render_subtree();
                break;
            
            case self::ACTION_COLLECT_THEMES:
                $this->handle_collect_themes();
                break;
            
            case self::ACTION_PAGE_INFO:
                $this->handle_page_info();
                break;
            
            default: echo "Unknown anction: ".$action."!";
        }
        
    }
}
