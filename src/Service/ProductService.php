<?php

namespace App\Service;

use App\Common\Validator\ProductValidator;
use App\Encoder\ProductEncoder;
use App\Helper\FileParser;
use App\Helper\Import\ImportReporter;
use App\Helper\Import\ImportResult;
use App\Repository\ProductRepository;

class ProductService
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

    public function importFromCsv(string $file, bool $testMode): ImportResult
    {
        $data = $this->fileParser->parseCsvFile($file);
        $counter = count($data);

        for($i = 0; $i < $counter; $i++) {
            $validationResult = $this->productValidator->validateRow($data[$i]);

            if ($validationResult !== true) {
                $this->reporter->reportError($data[$i], $validationResult['reason']);
                continue;
            }

            try {
                $product = $this->productEncoder->transform($data[$i]);

                if (!$testMode) {
                    $this->productRepository->save($product);
                }

                $this->reporter->reportSuccess($product);
            } catch (\Throwable $e) {
                $this->reporter->reportError($data[$i], $e->getMessage());
            }
        }

        return $this->reporter->getResults();
    }
}