<?php

namespace App\Common\Validator;

use App\Common\Exception\FileNotFoundException;
use App\Common\Exception\FileNotReadableException;
use InvalidArgumentException;

class FileValidator
{
    /**
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws InvalidArgumentException
     */
    public function validateCsvFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException();
        }

        if (!is_readable($filePath)) {
            throw new FileNotReadableException();
        }

        if (strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) !== 'csv') {
            throw new InvalidArgumentException("The file must be in CSV format.");
        }
    }
}