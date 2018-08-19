<?php

namespace Smartindex\Renderer;

interface iIndexRenderer extends iRenderer {
    const CLASS_OPEN = "open";
    const CLASS_CLOSED = "closed";
    const CLASS_NAMESPACE = "namespace";
    const CLASS_PAGE = "page";
}