<?php

namespace App\Tests\unit\Command;

use App\Command\DishGenerateCombinationsCommand;
use App\Service\DishService\DishBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DishGenerateCombinationsCommandTest extends TestCase
{

    /**
     * @dataProvider typeProvider
     */
    public function testExecuteRecursiveSuccess(string $type, string $methodName)
    {
        $pattern = 'accii';
        $result = [];
        $builder = $this->createMock(DishBuilder::class);
        $command = new DishGenerateCombinationsCommand($builder);


        $builder->expects($this->once())
            ->method($methodName)
            ->with($pattern)
            ->willReturn($result);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'pattern' => $pattern,
            'type' => $type,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Application Success', $output);
        $this->assertSame(0, $commandTester->getStatusCode());
    }

    protected function typeProvider()
    {
        return [
            ['r', 'buildDishesByRecursive'], // Первый набор данных: type = 'r'
            ['i', 'buildDishesByIterative'], // Второй набор данных: type = 'i'
        ];
    }
}
