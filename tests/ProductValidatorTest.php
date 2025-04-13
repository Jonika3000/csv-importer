<?php

namespace App\Tests;

use App\Common\Validator\ProductValidator;
use App\Repository\ProductRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductValidatorTest extends TestCase
{
    private $validator;
    private $productRepository;
    private $productValidator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->productRepository = $this->createMock(ProductRepository::class);

        $this->productValidator = new ProductValidator(
            $this->validator,
            $this->productRepository
        );
    }

    public function testValidateRowSuccess(): void
    {
        $row = ['P001', 'Product 1', 'Description 1', '10', '100.00', ''];

        $this->productRepository->method('findOneBy')
            ->willReturn(null);

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->productValidator->validateRow($row);

        $this->assertTrue(true);
    }

    public function testValidateRowDuplicateCode(): void
    {
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Duplicate product code: P001');

        $row = ['P001', 'Product 1', 'Description 1', '10', '100.00', ''];

        $this->productRepository->method('findOneBy')
            ->willReturn(new \App\Entity\Product());

        $this->productValidator->validateRow($row);
    }

    public function testValidateRowIncomplete(): void
    {
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Row is incomplete.');

        $row = ['P001', 'Product 1', 'Description 1'];
        $this->productValidator->validateRow($row);
    }

    public function testValidateRowMissingRequiredFields(): void
    {
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Missing required fields (code, name or description).');

        $row = ['', '', '', '10', '100.00', ''];
        $this->productValidator->validateRow($row);
    }

    public function testValidateRowNumericValidation(): void
    {
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Stock and price must be numeric.');

        $row = ['P001', 'Product 1', 'Description 1', 'invalid', 'invalid', ''];
        $this->productValidator->validateRow($row);
    }

    public function testValidateRowBusinessRules(): void
    {
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('Items costing less than 5$ with stock less than 10 are not imported.');

        $row = ['P001', 'Product 1', 'Description 1', '5', '4.99', ''];
        $this->productValidator->validateRow($row);
    }
}