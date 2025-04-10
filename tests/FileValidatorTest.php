<?php

namespace App\Tests;

use App\Common\Exception\FileNotFoundException;
use App\Common\Validator\FileValidator;
use PHPUnit\Framework\TestCase;

class FileValidatorTest extends TestCase
{
    public function test_it_throws_exception_when_file_does_not_exist()
    {
        $this->expectException(FileNotFoundException::class);

        $validator = new FileValidator();
        $validator->validateCsvFile('/fake/path/file.csv');
    }

    public function test_it_throws_exception_for_non_csv_file()
    {
        $this->expectException(\InvalidArgumentException::class);

        $file = tempnam(sys_get_temp_dir(), 'test');
        rename($file, $file . '.txt');

        $validator = new FileValidator();
        $validator->validateCsvFile($file . '.txt');
    }
}