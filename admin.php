<?php

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Indexer\DefaultIndexer;
use Smartindex\Utils\PageTools;

require_once (dirname(__FILE__).'/inc.php');
INC_requireDW();
INC_constsDW();
INC_constsSmartindex();

class admin_plugin_smartindex extends DokuWiki_Admin_Plugin {

  function handle() {
  }
 
  function html() {
      $config = new IndexConfiguration();
      PageTools::excludePageNamespaces($_REQUEST['id'], $namespace, $page);
      $config->namespace = $namespace;
      $config->openDepth = "5";
      $config->theme = "default";
      $config->checkHandle();
      $config->checkRender();
      if ( ! is_null($config->error)) {
          $res .= "<div class=\"smartindex-error\">SmartIndex error: {$config->error}</div>";
          echo $res;
      } else {
          $seeker = new DefaultIndexer($config);
          $data = $seeker->getIndex($config);

          $renderer = new Smartindex\Renderer\AdminRenderer($config);
          $renderer->setWrapper(false);
          $renderer->render($data, $res);
          echo $res;
      }
  }
  
  function getMenuText($language) {
      return "SmartIndex Plugin";
  }

}