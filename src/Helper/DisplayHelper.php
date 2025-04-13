<?php

namespace App\Helper;

use App\Helper\Import\ImportResult;
use Symfony\Component\Console\Output\OutputInterface;

class DisplayHelper
{
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