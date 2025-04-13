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
        private readonly ProductEncoder $productDataEncoder,
        private ImportReporter $reporter
    )
    {

    }

    public function importFromExcel(string $file, bool $testMode): ImportResult
    {
        $data = $this->fileParser->parseCsvFile($file);

        for($i = 0; $i < count($data); $i++) {
            $validationResult = $this->productValidator->validateRow($data[$i]);

            if ($validationResult !== true) {
                $this->reporter->reportError($data[$i], $validationResult['reason']);
                continue;
            }

            try {
                $product = $this->productDataEncoder->transform($data[$i]);

                if (!$testMode) {
                    $this->productRepository->saveAction($product);
                }

                $this->reporter->reportSuccess($product);
            } catch (\Throwable $e) {
                $this->reporter->reportError($data[$i], $e->getMessage());
            }
        }

        return $this->reporter->getResults();
    }
}