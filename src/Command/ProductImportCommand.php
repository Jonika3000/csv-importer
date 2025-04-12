<?php

namespace App\Command;

use App\Service\ProductService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ProductImportCommand',
    description: 'Import products from a CSV file',
)]
class ProductImportCommand extends Command
{

    public function __construct(
        private readonly ProductService $productService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('product:import')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to CSV file')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Run in test mode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $testMode = $input->getOption('test');

        $this->productService->importFromExcel($file, $testMode);

        return Command::SUCCESS;
    }

}
