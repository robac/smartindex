<?php

namespace Smartindex\Sorters;

class DefaultSorter implements \Smartindex\Sorters\iPageSorter
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function sort($data)
    {
        $isnamespace = array();
        $res[0] = array_merge($data[$this->config->namespace][0], $data[$this->config->namespace][1]);
        $res[1] = array_merge(array_fill(0, count($data[$this->config->namespace][0]), true), array_fill(0, count($data[$this->config->namespace][1]), false));
        $res[2] = array_merge($data[$this->config->namespace][0], $data[$this->config->namespace][1]);
        return $res;
    }
}