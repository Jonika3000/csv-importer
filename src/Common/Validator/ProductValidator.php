<?php

namespace App\Common\Validator;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ProductValidator
{
    public function __construct(
        private ValidatorInterface $validator,
        private ProductRepository $productRepository
    )
    {

    }
    public function validateRow(array $row): void
    {
        [$code, $name, $description, $stock, $price, $discontinued] = $row;

        if ($this->productRepository->findOneBy(['code' => $code]) !== null) {
            throw new ValidatorException(sprintf('Duplicate product code: %s', $code));
        }

        if (count($row) < 6) {
            throw new ValidatorException('Row is incomplete.');
        }

        if (empty($code) || empty($name) || empty($description)) {
            throw new ValidatorException('Missing required fields (code, name or description).');
        }

        if (!is_numeric($stock) || !is_numeric($price)) {
            throw new ValidatorException('Stock and price must be numeric.');
        }

        $priceFloat = (float) $price;
        $stockInt = (int) $stock;

        if ($priceFloat < 5 && $stockInt < 10) {
            throw new ValidatorException('Items costing less than 5$ with stock less than 10 are not imported.');
        }

        if ($priceFloat > 1000) {
            throw new ValidatorException('Items costing more than 1000$ are not imported.');
        }

        $constraints = new Assert\Collection([
            'code' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 10]),
                new Assert\Regex('/^[a-zA-Z0-9]+$/'),
            ],
            'name' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 50]),
            ],
            'description' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
            'stock' => [
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
                new Assert\PositiveOrZero(),
            ],
            'price' => [
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
                new Assert\Positive(),
                new Assert\Regex('/^\d+(\.\d{1,2})?$/'),
            ],
            'discontinued' => new Assert\Optional([
                new Assert\Choice(['yes', 'no', '']),
            ]),
        ]);

        $violations = $this->validator->validate([
            'code' => $code,
            'name' => $name,
            'description' => $description,
            'stock' => $stock,
            'price' => $price,
            'discontinued' => strtolower($discontinued ?? ''),
        ], $constraints);

        if (count($violations) > 0) {
            throw new ValidatorException($this->formatViolations($violations));
        }
    }

    private function formatViolations(ConstraintViolationListInterface $violations): string
    {
        $messages = [];

        foreach ($violations as $violation) {
            $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        return implode('; ', $messages);
    }
}