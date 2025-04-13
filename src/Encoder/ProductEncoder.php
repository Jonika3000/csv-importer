<?php

namespace App\Encoder;

use App\Entity\Product;
use DateTimeImmutable;

class ProductEncoder
{
    public function transform(array $row): Product
    {
        [$code, $name, $desc, $stock, $price, $discontinued] = array_map('trim', $row);

        $product = new Product();
        $product
            ->setCode($code)
            ->setName($name)
            ->setDescription($desc)
            ->setStockLevel((int)$stock)
            ->setPrice((float)$price)
            ->setAddedAt(new DateTimeImmutable());

        if (strtolower($discontinued ?? '') === 'yes') {
            $product->setDiscontinuedAt(new DateTimeImmutable());
        }

        return $product;
    }
}