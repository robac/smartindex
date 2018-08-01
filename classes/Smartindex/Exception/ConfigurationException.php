<?php

namespace Smartindex\Exception;


class ConfigurationException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return "Smartindex configuration exception: [{$this->code}]: {$this->message}\n";
    }
}