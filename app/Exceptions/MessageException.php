<?php

namespace App\Exceptions;

use Exception;

class MessageException extends Exception
{
    public function __construct($message, $page = null, $param = null)
    {
        $this->page = null;
        $this->param = null;
        if ($page !== null) {
            $this->page = $page;
            $this->param = $param;
        }
        parent::__construct($message);
    }
    public function hasPage() {
        return $this->page !== null;
    }
    public function getPage() {
        return $this->page;
    }
    public function hasParam() {
        return $this->param !== null;
    }
    public function getParam() {
        return $this->param;
    }
}
