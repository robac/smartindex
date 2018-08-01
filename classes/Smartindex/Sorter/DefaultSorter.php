<?php

namespace Smartindex\Sorter;

class DefaultSorter implements \Smartindex\Sorter\iPageSorter
{
    private $config;

    public function __construct(\Smartindex\Configuration\IndexConfiguration $config)
    {
        $this->config = $config;
    }

    public function sort($data)
    {
        $namespace = $this->config->getAttribute('namespace');
        $isnamespace = array();

        $res[0] = array_merge($data[$namespace]["namespaces"], $data[$namespace][1]);
        $res[1] = array_merge(array_fill(0, count($data[$namespace]["namespaces"]), true), array_fill(0, count($data[$namespace][1]), false));
        $res[2] = array_merge($data[$namespace]["namespaces"], $data[$namespace][1]);
        return $res;
    }
}