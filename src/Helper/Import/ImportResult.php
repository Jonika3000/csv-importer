<?php

namespace App\Helper\Import;

readonly class ImportResult
{
    /**
     * @param int $total Total rows processed
     * @param int $imported Successfully imported count
     * @param array $skipped Array of skipped rows with reasons
     */
    public function __construct(
        public int   $total,
        public int   $imported,
        public array $skipped
    ) {}

    public function getSkippedCount(): int
    {
        return count($this->skipped);
    }

    public function getSuccessRate(): float
    {
        return $this->total > 0 ? ($this->imported / $this->total) * 100 : 0;
    }
}