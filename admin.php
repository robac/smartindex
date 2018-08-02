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

      try {
          PageTools::getPageFromId($_REQUEST['id'], $namespace, $page);
          $config = new IndexConfiguration(array(
              'namespace' => $namespace,
              'openDepth' => 1,
              'theme' => 'default'
          ));
          $config->validate();
          $config->checkRender();
      } catch (\Exception $e) {
          echo "<div class=\"smartindex-error\"> '.$e->getMessage().' {$config->error}</div>";
          return;
      }

      $indexer = new DefaultIndexer($config);
      $index = $indexer->getIndex($config);

      $renderer = new Smartindex\Renderer\AdminRenderer($config);
      $renderer->setWrapper(false);
      $renderer->render($index, $res);
      echo $res;
  }
  
  function getMenuText($language) {
      return "SmartIndex Plugin";
  }

}