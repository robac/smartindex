<?php
 
require_once (dirname(__FILE__).'/inc.php');
INC_requireDW();
INC_constsDW();
INC_constsSmartindex();

class admin_plugin_smartindex extends DokuWiki_Admin_Plugin {

  function handle() {
  }
 
  function html() {
    $template = new Monotek\MiniTPL\Template(TEMPLATES_DIR);
    $template->load("admin.tpl");
    $template->render();

    //echo HtmlHelper::createInlineScript('var collectorURL = "'.AJAX_URL.'";');
      /*      $config = new SmartIndexConf();
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
      }*/
  }
  
  function getMenuText($language) {
      return "SmartIndex Plugin";
  }

}