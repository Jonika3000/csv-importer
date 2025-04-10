<?php

namespace App\Tests;

use App\Helper\FileParser;
use PHPUnit\Framework\TestCase;

class FileParserTest extends TestCase
{
    protected function setUp(): void
    {
        $this->csvFilePath = tempnam(sys_get_temp_dir(), 'csv_');
        $content = <<<CSV
        Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued
        P0002,Cd Player,Nice CD player,11,50.12,yes
        CSV;
        file_put_contents($this->csvFilePath, $content);
    }

    protected function tearDown(): void
    {
        unlink($this->csvFilePath);
    }

    public function test_it_reads_csv_file_correctly()
    {
        $parser = new FileParser();
        $rows = $parser->parseCsvFile($this->csvFilePath);

        $this->assertCount(2, $rows);

        $this->assertEquals([
            'Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'
        ], $rows[0]);

        $this->assertEquals([
            'P0002','Cd Player','Nice CD player','11','50.12','yes'
        ], $rows[1]);
    }
}