<?php
 
require_once (dirname(__FILE__).'/inc.php');
INC_requireDW();
INC_constsDW();
INC_constsSmartindex();

class admin_plugin_smartindex extends DokuWiki_Admin_Plugin {

  function handle() {
  }
 
  function html() {
    echo file_get_contents(HTML_DIR.'admin.html');
    echo HtmlHelper::createInlineScript('var collectorURL = "'.AJAX_URL.'";');
  }
  
  function getMenuText($language) {
      return "SmartIndex Plugin";
  }

}