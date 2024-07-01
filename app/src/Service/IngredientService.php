<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Repository\IngredientTypeRepository;

class IngredientService
{
    public function __construct(
        private IngredientRepository $ingredientRepository,
        private IngredientTypeRepository $ingredientTypeRepository
    )
    {
    }

    /**
     * @param String $code
     * @return Ingredient[]
     */
    public function getIngredientsByCode(String $code): array
    {
        $type = $this->ingredientTypeRepository->findOneBy(['code' => $code]);
        return $this->ingredientRepository->findBy(['type' => $type]);
    }

}