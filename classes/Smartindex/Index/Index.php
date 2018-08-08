<?php

namespace Smartindex\Index;


class Index
{
    const TITLE = 0;
    const IS_NS = 1;

    public $namespace = array();

    public function addItem($namespace, $id, $itemTitle, $isNamespace) {
        $this->namespace[$namespace][$id] = array(
            self::TITLE => $itemTitle,
            self::IS_NS => $isNamespace,
        );
    }

}