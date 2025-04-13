<?php

namespace App\Helper\Import;

use App\Entity\Product;
use Psr\Log\LoggerInterface;

class ImportReporter
{
    private int $successCount = 0;
    private array $errors = [];
    private ?string $currentFile = null;

    public function __construct(
        private readonly ?LoggerInterface $logger = null
    ) {}

    public function reportSuccess(Product $product): void
    {
        $this->successCount++;

        $this->logger?->info('Product imported', [
            'file' => $this->currentFile,
            'product_code' => $product->getCode(),
            'product_name' => $product->getName(),
        ]);
    }

    public function reportError(array $row, string $reason, ?int $lineNumber = null): void
    {
        $error = [
            'row' => $row,
            'reason' => $reason,
            'line' => $lineNumber,
        ];

        $this->errors[] = $error;

        $this->logger?->error('Import error', [
            'file' => $this->currentFile,
            'reason' => $reason,
            'line' => $lineNumber,
            'data' => $row,
        ]);
    }

    public function getResults(): ImportResult
    {
        return new ImportResult(
            total: $this->successCount + count($this->errors),
            imported: $this->successCount,
            skipped: $this->errors
        );
    }

    public function reset(): void
    {
        $this->successCount = 0;
        $this->errors = [];
    }

    public function setCurrentFile(string $file): void
    {
        $this->currentFile = $file;
    }
}