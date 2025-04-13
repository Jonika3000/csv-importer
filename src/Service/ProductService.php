<?php

namespace App\Service;

use App\Common\Validator\ProductValidator;
use App\Encoder\ProductEncoder;
use App\Helper\FileParser;
use App\Helper\Import\ImportReporter;
use App\Helper\Import\ImportResult;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Exception\ValidatorException;

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
}