<?php

namespace App\Importer\CsvProductImporter;

use App\Common\Exception\CsvProductImporterException;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class CsvProductImporterRegistry
{
    /**
     * @var CsvProductImporter[]
     */
    private array $importers;

    public function __construct(#[TaggedIterator('app.csv_product_importer')] iterable $importers)
    {
        $this->importers = iterator_to_array($importers);
    }

    /**
     * @throws CsvProductImporterException
     */
    public function findByCsvFile(string $filePath): CsvProductImporter
    {
        foreach ($this->importers as $importer) {
            if ($importer->fileIsSupported($filePath)) {
                return $importer;
            }
        }

        throw new CsvProductImporterException("No suitable CSV importer found for file: $filePath");
    }
}