<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(
    name: "tblProductData",
    indexes: [new ORM\Index(name: "strProductCode_idx", columns: ["code"])],
    options: [
        "charset" => "latin1",
        "collation" => "latin1_swedish_ci"
    ]
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", options: ["unsigned" => true])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $code = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeImmutable $discontinuedAt = null;

    #[ORM\Column(type: "datetime")]
    #[ORM\Version]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(type: "integer", options: ["unsigned" => true])]
    private ?int $stockLevel = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?string $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(?\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getDiscontinuedAt(): ?\DateTimeImmutable
    {
        return $this->discontinuedAt;
    }

    public function setDiscontinuedAt(?\DateTimeImmutable $discontinuedAt): static
    {
        $this->discontinuedAt = $discontinuedAt;

        return $this;
    }

    public function getStockLevel(): ?int
    {
        return $this->stockLevel;
    }

    public function setStockLevel(?int $stockLevel): static
    {
        $this->stockLevel = $stockLevel;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }
}