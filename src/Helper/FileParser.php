<?php

namespace App\Helper;

use PhpOffice\PhpSpreadsheet\IOFactory;

class FileParser
{
    public function parseCsvFile(string $filePath): array
    {
        $reader = IOFactory::createReader('Csv');
        $reader->setDelimiter(',');
        $reader->setEnclosure('"');
        $reader->setSheetIndex(0);

        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $data = [];

        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = trim($cell->getValue());
            }

            $data[] = $rowData;
        }

        return $data;
    }
}