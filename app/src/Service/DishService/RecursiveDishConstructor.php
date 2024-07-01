<?php

namespace App\Service\DishService;

class RecursiveDishConstructor implements DishConstructorInterface
{
    /** @var array  */
    private array $ingredients;

    /** @var string  */
    private string $pattern;

    /** @var array  */
    private array $combinations;

    /**
     * @param array $ingredients
     * @param string $pattern
     */
    public function __construct(array $ingredients, string $pattern)
    {
        $this->ingredients = $ingredients;
        $this->pattern = $pattern;
        $this->combinations = [];
    }

    /**
     * @return array
     */
    public function generateCombinations(): array
    {
        $this->combinations = [];
        $this->createCombinationsByRecursive([], $this->pattern);
        return $this->combinations;
    }

    /**
     * @param $currentCombination
     * @param $currentPattern
     * @return void
     */
    private function createCombinationsByRecursive($currentCombination, $currentPattern): void
    {
        if (empty($currentPattern)) {
            $this->combinations[] = $this->buildDish($currentCombination);
            return;
        }

        $symbol = $currentPattern[0];

        foreach ($this->ingredients[$symbol] as $ingredient) {

            if (!in_array($ingredient, $currentCombination, true)) {
                $this->createCombinationsByRecursive(array_merge($currentCombination, [$ingredient]), substr($currentPattern, 1));
            }
        }
    }

    /**
     * @param $combination
     * @return array
     */
    private function buildDish($combination): array
    {
        $products = [];
        $price = 0;

        foreach ($combination as $ingredient) {
            $products[] = [
                'type' => $ingredient['type'],
                'value' => $ingredient['title']
            ];
            $price += $ingredient['price'];
        }

        return [
            'products' => $products,
            'price' => $price
        ];
    }
}