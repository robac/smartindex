<?php

namespace Smartindex\Index;


class Index
{
    const TITLE = 0;
    const IS_NS = 1;
    const IS_OPEN = 2;

    public $namespace = array();

    public function addItem($namespace, $id, $itemTitle, $isNamespace, $isOpen = false) {
        $this->namespace[$namespace][$id] = array(
            self::TITLE => $itemTitle,
            self::IS_NS => $isNamespace,
            self::IS_OPEN => $isOpen,
        );
    }

}