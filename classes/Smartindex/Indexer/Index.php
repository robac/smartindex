<?php

namespace Smartindex\Indexer;


class Index
{
    const ID = 0;
    const TITLE = 1;
    const IS_NS = 2;

    public $namespace = array();

    public function addItem($namespace, $id, $itemTitle, $isNamespace) {
        $this->namespace[$namespace][$id] = array(
            self::TITLE => $itemTitle,
            self::IS_NS => $isNamespace,
        );
    }

}