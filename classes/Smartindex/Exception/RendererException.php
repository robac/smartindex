<?php

namespace Smartindex\Exception;


class RenderernException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return "Smartindex renderer exception: [{$this->code}]: {$this->message}\n";
    }
}