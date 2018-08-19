<?php

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Utils\IndexTools;
use Smartindex\Renderer\AdminRenderer;

require_once (dirname(__FILE__).'/inc.php');
INC_requireDW();
INC_constsDW();
INC_constsSmartindex();

class admin_plugin_smartindex extends DokuWiki_Admin_Plugin {

  function handle() {
  }
 
  function html() {

      try {
          IndexTools::getPageFromId($_REQUEST['id'], $namespace, $page);
          $config = new IndexConfiguration(array(
              'namespace' => $namespace,
              'loadLevel' => 1,
              'theme' => 'default'
          ));
          $config->validate();
      } catch (\Exception $e) {
          echo "<div class=\"smartindex-error\"> '.$e->getMessage().' {$config->error}</div>";
          return;
      }

      $renderer = new AdminRenderer($config);
      $renderer->render($res);
      echo $res;
  }
  
  function getMenuText($language) {
      return "SmartIndex Plugin";
  }

}