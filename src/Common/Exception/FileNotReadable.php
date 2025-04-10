<?php

namespace App\Common\Exception;

class FileNotReadable extends \Exception
{
    protected $message = 'File not readable';
}