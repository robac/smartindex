<?php
/**
 * Created by PhpStorm.
 * User: poch
 * Date: 02.08.2018
 * Time: 16:27
 */

namespace Smartindex\Exception;


use splitbrain\phpcli\Exception;

class ThemeException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return "Smartindex theme exception: [{$this->code}]: {$this->message}\n";
    }

}