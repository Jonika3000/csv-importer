<?php

namespace App\Common\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ProductValidator
{
    public function __construct(
        private ValidatorInterface $validator
    )
    {

    }
    public function validateRow(array $row): array|true
    {
        if (count($row) < 6) {
            return [
                'row' => $row,
                'reason' => 'Incomplete row data',
            ];
        }

        [$code, $name, $description, $stock, $price, $discontinued] = $row;

        if (empty($code) || empty($name) || empty($description)) {
            return [
                'row' => $row,
                'reason' => 'Missing required fields (code, name or description)',
            ];
        }

        if (!is_numeric($stock) || !is_numeric($price)) {
            return [
                'row' => $row,
                'reason' => 'Stock and price must be numeric',
            ];
        }

        $priceFloat = (float) $price;
        $stockInt = (int) $stock;

        if ($priceFloat < 5 && $stockInt < 10) {
            return [
                'row' => $row,
                'reason' => 'Items costing less than 5$ with stock less than 10$ are not imported',
            ];
        }

        if ($priceFloat > 1000) {
            return [
                'row' => $row,
                'reason' => 'Items costing more than 1000$ are not imported',
            ];
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
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }

            return [
                'row' => $row,
                'reason' => implode('; ', $messages),
            ];
        }

        return true;
    }
}