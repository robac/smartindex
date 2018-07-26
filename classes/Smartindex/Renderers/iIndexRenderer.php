<?php

namespace Smartindex\Renderers;

interface iIndexRenderer {
    const CLASS_OPEN = "open";
    const CLASS_CLOSED = "closed";
    const CLASS_NAMESPACE = "namespace";
    const CLASS_PAGE = "page";

    public function setWrapper($useWrapper, $id = NULL);
    public function render($data, &$document);
}