<?php

namespace App\Service\DishService;

use App\Exception\StackOverflowException;
use App\Service\IngredientService;
use App\Service\RedisService;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DishBuilder
{

    /**
     * @param IngredientService $ingredientService
     * @param RedisService $redisService
     */
    public function __construct(
        private readonly IngredientService $ingredientService,
        private readonly RedisService      $redisService
    )
    {
    }

    /**
     * @param string $pattern
     *
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     * @throws StackOverflowException
     */
    public function buildDishesByRecursive(string $pattern): array
    {
        $ingredientCounts = $this->countIngrediens($pattern);

        $ingredientMap = $this->createIngredientsMaps($ingredientCounts);

        if ($this->countCombinations($ingredientMap, $ingredientCounts, $pattern) > 100) {
            throw new StackOverflowException("Для рекурсивного метода слишком много комбинаций");
        }

        $constructor = new RecursiveDishConstructor($ingredientMap, $pattern);
        return $constructor->generateCombinations();
    }

    /**
     * @param string $pattern
     *
     * @return array
     */
    public function buildDishesByIterative(string $pattern): array
    {
        $ingredientCounts = $this->countIngrediens($pattern);

        $ingredientMap = $this->createIngredientsMaps($ingredientCounts);

        $constructor = new IterativeDishConstructor($ingredientMap, $pattern);
        return $constructor->generateCombinations();
    }

    /**
     * @param array $ingredientsMap
     * @param array $ingredientCounts
     * @param string $inputLine
     *
     * @return int
     *
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     */
    private function countCombinations(
        array  $ingredientsMap,
        array  $ingredientCounts,
        string $inputLine
    ): int {

        if (empty($ingredientCounts)) {
            return 0;
        }

        $count = $this->redisService->getRedisAdapter()->get(
            sprintf('combinations-%s', $inputLine),
            function (ItemInterface $item) use ($ingredientsMap, $ingredientCounts) {
                $item->expiresAfter(600);
                return $this->calculateCombinations($ingredientsMap, $ingredientCounts);
            }
        );

        return $count;
    }

    /**
     * @param array $ingredientsMap
     * @param array $ingredientCounts
     *
     * @return int
     */
    private function calculateCombinations(array $ingredientsMap, array $ingredientCounts): int
    {
        $combinationCount = 1;
        foreach ($ingredientCounts as $ingredientCode => $ingredientCount) {
            $combinationCount *= count($ingredientsMap[$ingredientCode]) ** $ingredientCount;
        }
        return $combinationCount;
    }

    /**
     * @param array $ingredientCounts
     *
     * @return array
     */
    public function createIngredientsMaps(array $ingredientCounts): array
    {
        $ingredientMap = [];
        foreach ($ingredientCounts as $ingredientCode => $ingredientCount) {
            $ingredients = $this->ingredientService->getIngredientsByCode($ingredientCode);
            array_walk($ingredients, function ($ingredient) use (&$ingredientMap, $ingredientCode) {
                $ingredientMap[$ingredientCode][] = $ingredient->toArray();
            });
        }
        return $ingredientMap;
    }

    /**
     * @param string $pattern
     * @return array
     */
    public function countIngrediens(string $pattern): array
    {
        $ingredientCounts = array_count_values(str_split($pattern));
        return $ingredientCounts;
    }
}