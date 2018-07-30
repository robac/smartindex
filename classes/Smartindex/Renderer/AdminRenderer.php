<?php
namespace Smartindex\Renderer;

class AdminRenderer implements \Smartindex\Renderer\iIndexRenderer {

    private $useWrapper = true;
    private $wrapperClasses = array();
    private $wrapperId;

    private $config;
    private $basicData;


    public function setWrapper($useWrapper, $id = NULL) {
        $this->useWrapper = $useWrapper;

        $this->wrapperClasses[] = \Smartindex\Configuration\IndexConfiguration::TREE_CLASS;
        $this->wrapperClasses[] = $this->config->cssClass;
        if ($this->config->highlite) {
            $this->wrapperClasses[] = \Smartindex\Configuration\IndexConfiguration::HIGHLITE_CLASS;
        }

        $this->wrapperId = $id;
    }

    public function __construct(\Smartindex\Configuration\IndexConfiguration $config) {
        $this->config = $config;
    }

    public function render($data, &$document) {

        /*        if ($this->useWrapper) {
                    $document .= "<div".HtmlHelper::createIdClassesPart($this->wrapperId, $this->wrapperClasses).">";
                }

                $this->basicData = $data;
                //if ($this->config->showMain) $document .= "<ul><li class=\"namespace open\"><div><a href=\"#\">root</a></div>";
                $this->buildList($data, $this->config->namespace, $document, 1);
                //if ($this->config->showMain) $document .= "</li></ul>";

                if ($this->useWrapper) {
                    $document .= "</div>";
                }*/

        $sorter = new \Smartindex\Sorter\DefaultSorter($this->config);

        $template = new \Monotek\MiniTPL\Template(TEMPLATES_DIR);
        $template->load("admin.tpl");
        $pages = $sorter->sort($data);
        $template->assign("namespace", $this->config->namespace);
        $template->assign("page_titles", $pages[0]);
        $template->assign("isnamespace", $pages[1]);
        $template->assign("page_ids", $pages[2  ]);
        $template->assign("sectoken", getSecurityToken());
        $template->render();
    }


}

