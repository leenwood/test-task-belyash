<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "ingredient")]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: IngredientType::class)]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id", nullable: false)]
    private IngredientType $type;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "decimal", precision: 19, scale: 2)]
    private float $price;

    // Getters and Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): IngredientType
    {
        return $this->type;
    }

    public function setType(IngredientType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'type' => $this->type->getTitle(),
            'price' => $this->price
        ];
    }
}