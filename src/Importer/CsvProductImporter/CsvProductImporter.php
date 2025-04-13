<?php

namespace App\Importer\CsvProductImporter;

use App\Common\Exception\CsvProductImporterException;
use App\Helper\Import\ImportResult;

interface CsvProductImporter
{
    /**
     * @throws CsvProductImporterException
     */
    public function import(string $filePath, bool $testMode): ImportResult;

    public function fileIsSupported(string $filePath): bool;
}