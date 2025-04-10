<?php

namespace App\Common\Exception;

class FileNotFoundException extends \Exception
{
    protected $message = 'File not found';
}