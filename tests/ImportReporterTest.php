<?php

namespace App\Tests;

use App\Entity\Product;
use App\Helper\Import\ImportReporter;
use PHPUnit\Framework\TestCase;

class ImportReporterTest extends TestCase
{
    private ImportReporter $reporter;

    protected function setUp(): void
    {
        $this->reporter = new ImportReporter();
    }

    public function testReportSuccess(): void
    {
        $product = new Product();
        $product->setCode('P001');

        $this->reporter->reportSuccess($product);
        $result = $this->reporter->getResults();

        $this->assertEquals(1, $result->imported);
        $this->assertEquals(1, $result->total);
    }

    public function testReportError(): void
    {
        $row = ['P001', 'Product 1', '', '10', '100.00', ''];
        $reason = 'Missing description';

        $this->reporter->reportError($row, $reason);
        $result = $this->reporter->getResults();

        $this->assertEquals(1, $result->getSkippedCount());
        $this->assertEquals(1, $result->total);
        $this->assertEquals($reason, $result->skipped[0]['reason']);
    }

    public function testReset(): void
    {
        $product = new Product();
        $product->setCode('P001');

        $this->reporter->reportSuccess($product);
        $this->reporter->reset();
        $result = $this->reporter->getResults();

        $this->assertEquals(0, $result->total);
    }
}