<?php
interface iIndexRenderer {
    public function setWrapper($useWrapper, $id = NULL);
    public function render($data, &$document);
}