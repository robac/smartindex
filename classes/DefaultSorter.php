<?php
/**
 * Created by PhpStorm.
 * User: poch
 * Date: 25.07.2018
 * Time: 15:45
 */

class DefaultSorter implements iPageSorter
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
        return $res;
    }
}