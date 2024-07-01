<?php

namespace App\Service;

class IterativeDishConstructor implements DishConstructorInterface
{
    /** @var array */
    private array $ingredients;

    /** @var string */
    private string $pattern;


    /** @var array */
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
        $stack = [['combination' => [], 'index' => 0, 'pattern' => $this->pattern]];
        $i = 0;

        while (!empty($stack)) {
            $currentState = array_pop($stack);
            $currentCombination = $currentState['combination'];
            $currentIndex = $currentState['index'];
            $currentPattern = $currentState['pattern'];

            if (empty($currentPattern)) {
                $this->combinations[] = $this->transformResult($currentCombination);
                continue;
            }

            $symbol = $currentPattern[0];
            $currentPattern = substr($currentPattern, 1);

            foreach ($this->ingredients[$symbol] as $ingredient) {
                if (!in_array($ingredient, $currentCombination, true)) {
                    $newCombination = array_merge($currentCombination, [$ingredient]);
                    $stack[] = ['combination' => $newCombination, 'index' => $currentIndex + 1, 'pattern' => $currentPattern];
                }
            }
        }
        return $this->combinations;
    }

    /**
     * @param $combination
     * @return array
     */
    private function transformResult($combination): array
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