<?php

namespace App\Tests;

use App\Common\Validator\ProductValidator;
use App\Encoder\ProductEncoder;
use App\Helper\FileParser;
use App\Helper\Import\ImportReporter;
use App\Helper\Import\ImportResult;
use App\Importer\CsvProductImporter\MainPartnerCsvProductImporter;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ValidatorException;

class MainPartnerCsvProductImporterTest extends TestCase
{
    private $productRepository;
    private $fileParser;
    private $productValidator;
    private $productEncoder;
    private $reporter;
    private $importer;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->fileParser = $this->createMock(FileParser::class);
        $this->productValidator = $this->createMock(ProductValidator::class);
        $this->productEncoder = $this->createMock(ProductEncoder::class);
        $this->reporter = $this->createMock(ImportReporter::class);

        $this->importer = new MainPartnerCsvProductImporter(
            $this->productRepository,
            $this->fileParser,
            $this->productValidator,
            $this->productEncoder,
            $this->reporter
        );
    }

    public function testFileIsSupported(): void
    {
        $this->assertTrue($this->importer->fileIsSupported('/path/to/main_products.csv'));
        $this->assertFalse($this->importer->fileIsSupported('/path/to/other_products.csv'));
    }

    public function testImportSuccess(): void
    {
        $testData = [
            ['P001', 'Product 1', 'Description 1', '10', '100.00', ''],
            ['P002', 'Product 2', 'Description 2', '5', '50.00', 'yes']
        ];

        $this->fileParser->method('parseCsvFile')
            ->willReturn($testData);

        $this->productValidator->expects($this->exactly(2))
            ->method('validateRow');

        $this->productEncoder->expects($this->exactly(2))
            ->method('encode')
            ->willReturn(new \App\Entity\Product());

        $this->reporter->expects($this->exactly(2))
            ->method('reportSuccess');

        $this->productRepository->expects($this->exactly(2))
            ->method('save');

        $result = $this->importer->import('test.csv', false);

        $this->assertInstanceOf(ImportResult::class, $result);
    }

    public function testImportWithValidationError(): void
    {
        $testData = [
            ['P001', 'Product 1', 'Description 1', '10', '100.00', ''],
            ['P002', '', 'Description 2', '5', '50.00', 'yes'] // Invalid - missing name
        ];

        $this->fileParser->method('parseCsvFile')
            ->willReturn($testData);

        $this->productValidator->expects($this->exactly(2))
            ->method('validateRow')
            ->willReturnCallback(function ($row) {
                if (empty($row[1])) {
                    throw new ValidatorException('Missing name');
                }
            });

        $this->reporter->expects($this->once())
            ->method('reportError');

        $this->productEncoder->expects($this->once())
            ->method('encode')
            ->willReturn(new \App\Entity\Product());

        $this->productRepository->expects($this->once())
            ->method('save');

        $result = $this->importer->import('test.csv', false);

        $this->assertInstanceOf(ImportResult::class, $result);
    }

    public function testImportInTestMode(): void
    {
        $testData = [
            ['P001', 'Product 1', 'Description 1', '10', '100.00', '']
        ];

        $this->fileParser->method('parseCsvFile')
            ->willReturn($testData);

        $this->productRepository->expects($this->never())
            ->method('save');

        $result = $this->importer->import('test.csv', true);

        $this->assertInstanceOf(ImportResult::class, $result);
    }
}