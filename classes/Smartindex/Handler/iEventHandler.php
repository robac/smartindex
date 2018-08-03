<?php

namespace Smartindex\Handler;

interface iEventHandler {
    public function handle(\Doku_Event &$event, $param);
}