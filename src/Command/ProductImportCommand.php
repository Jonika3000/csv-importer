<?php

namespace App\Command;

use App\Common\Validator\FileValidator;
use App\Helper\DisplayHelper;
use App\Helper\Import\ImportResult;
use App\Service\ProductService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'product:import',
    description: 'Import products from a CSV file',
)]
class ProductImportCommand extends Command
{

    public function __construct(
        private readonly ProductService $productService,
        private readonly FileValidator  $fileValidator
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('product-import')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to CSV file')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Run in test mode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $testMode = $input->getOption('test');

        try {
            $this->fileValidator->validateCsvFile($file);
        } catch (\Exception $exception) {
            $this->displayError($output, $exception->getMessage());
            return Command::FAILURE;
        }

        $result = $this->productService->importFromCsv($file, $testMode);
        $this->displayResults($output, $result);

        return $result->getSkippedCount() > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    public function displayResults(OutputInterface $output, ImportResult $result): void
    {
        $output->writeln("=== Import Results ===");
        $output->writeln("Total rows: " . $result->total);
        $output->writeln("Imported: " . $result->imported);
        $output->writeln("Skipped: " . $result->getSkippedCount());
        $output->writeln("Success rate: " . round($result->getSuccessRate(), 2) . "%");

        if ($result->getSkippedCount() > 0) {
            $output->writeln("\n=== Skipped Rows ===");
            foreach ($result->skipped as $error) {
                $output->writeln(sprintf(
                    "Line %s: %s | Data: %s",
                    $error['line'] ?? 'N/A',
                    $error['reason'],
                    implode(', ', array_map('trim', $error['row']))
                ));
            }
        }
    }

    public function displayError(OutputInterface $output, string $message): void
    {
        $output->writeln("<error>Error: $message</error>");
    }
}
