<?php

namespace App\Common\Exception;

class FileNotReadableException extends \Exception
{
    protected $message = 'File not readable';
}