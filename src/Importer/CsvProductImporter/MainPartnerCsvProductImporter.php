<?php

namespace App\Importer\CsvProductImporter;

use App\Common\Validator\ProductValidator;
use App\Encoder\ProductEncoder;
use App\Helper\FileParser;
use App\Helper\Import\ImportReporter;
use App\Helper\Import\ImportResult;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Exception\ValidatorException;

class MainPartnerCsvProductImporter implements CsvProductImporter
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly FileParser        $fileParser,
        private readonly ProductValidator  $productValidator,
        private readonly ProductEncoder $productEncoder,
        private readonly ImportReporter $reporter
    )
    {

    }

    public function import(string $filePath, bool $testMode): ImportResult
    {
        $data = $this->fileParser->parseCsvFile($filePath);
        $counter = count($data);

        for($i = 0; $i < $counter; $i++) {
            try {
                $this->productValidator->validateRow($data[$i]);
            } catch (ValidatorException $e) {
                $this->reporter->reportError($data[$i], $e->getMessage());
                continue;
            }

            $product = $this->productEncoder->encode($data[$i]);

            if (!$testMode) {
                $this->productRepository->save($product);
            }

            $this->reporter->reportSuccess($product);
        }

        return $this->reporter->getResults();
    }

    public function fileIsSupported(string $filePath): bool
    {
        return str_contains(basename($filePath), 'main');
    }
}