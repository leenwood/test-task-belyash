<?php

namespace App\Command;

use App\Exception\StackOverflowException;
use App\Service\DishService\DishBuilder;
use Psr\Cache\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:execute',
    description: 'Вариант с рекурсивным перебором',
)]
class DishGenerateCombinationsCommand extends Command
{
    public function __construct(
        private DishBuilder $builder
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('pattern', InputArgument::REQUIRED, 'Паттерн выборки')
            ->addArgument('type', InputArgument::OPTIONAL, 'r - Рекурсивно, i - Интеративно')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pattern = $input->getArgument('pattern');
        $type = $input->getArgument('type');

        $type = $type ?? 'i';

        if ($type === 'r') {
            try {
                $result = $this->builder->buildDishesByRecursive($pattern);
            } catch (StackOverflowException $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            } catch (ContainerExceptionInterface $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            } catch (InvalidArgumentException $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            } catch (\Throwable $e) {
                $io->error('Internal Server Error');
                return Command::FAILURE;
            }

        } else {
            try {
                $result = $this->builder->buildDishesByIterative($pattern);
            } catch (\Throwable $e) {
                $io->error('Internal Server Error');
                return Command::FAILURE;
            }

        }

        print_r($result);


        $io->success('Application Success');

        return Command::SUCCESS;
    }
}
