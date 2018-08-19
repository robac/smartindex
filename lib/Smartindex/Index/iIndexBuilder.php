<?php

namespace Smartindex\Index;

use Smartindex\Configuration\IndexConfiguration;

interface iIndexBuilder
{
//    const KEY_DIRS = 0;
    const KEY_DIRS = "namespaces";
    const KEY_PAGES = 1;
    const KEY_PAGES_TITLE = 2;
    const KEY_FOLLOW = 3;
    const KEY_FRONT = 4;

    const INFO_NS = 0;
    const INFO_DIR = 1;
    const INFO_FOLLOW = 2;

    public function __construct(IndexConfiguration $config);
    public function getIndex();
}