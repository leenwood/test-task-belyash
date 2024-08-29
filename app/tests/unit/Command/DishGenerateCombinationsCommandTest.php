<?php

namespace App\Tests\unit\Command;

use App\Command\DishGenerateCombinationsCommand;
use App\Exception\StackOverflowException;
use App\Service\DishService\DishBuilder;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class DishGenerateCombinationsCommandTest extends TestCase
{

    /**
     * @dataProvider successExecuteDataProvider
     *
     * @param string $type
     * @param string $methodName
     */
    public function testExecuteSuccess(string $type, string $methodName)
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
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    /**
     * @return array[]
     */
    private function successExecuteDataProvider(): array
    {
        return [
            ['r', 'buildDishesByRecursive'],
            ['i', 'buildDishesByIterative'],
        ];
    }


    /**
     * @dataProvider exceptionExecuteDataProvider
     *
     * @param string $type
     * @param string $methodName
     * @param Exception $exception
     * @return void
     */
    public function testExecuteWithException(string $type, string $methodName, Exception $exception, string $errorText)
    {
        $pattern = 'accii';
        $builder = $this->createMock(DishBuilder::class);
        $command = new DishGenerateCombinationsCommand($builder);


        $builder->expects($this->once())
            ->method($methodName)
            ->with($pattern)
            ->willThrowException($exception);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'pattern' => $pattern,
            'type' => $type,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString($errorText, $output);
        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }

    /**
     * @return array[]
     */
    private function exceptionExecuteDataProvider(): array
    {
        return [
            [
                'r',
                'buildDishesByRecursive',
                new StackOverflowException('Для рекурсивного метода слишком много комбинаций'),
                'Для рекурсивного метода слишком много комбинаций'
            ],
            [
                'r',
                'buildDishesByRecursive',
                new Exception('Other exception'),
                'Internal Server Error'
            ],
            [
                'i',
                'buildDishesByIterative',
                new Exception('Other exception'),
                'Internal Server Error'
            ],
        ];
    }
}
