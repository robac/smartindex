<?php

namespace Smartindex\Renderer;

use Smartindex\Configuration\IndexConfiguration;

interface iRenderer {
    public function __construct(IndexConfiguration $config);
    public function render(&$document);
}