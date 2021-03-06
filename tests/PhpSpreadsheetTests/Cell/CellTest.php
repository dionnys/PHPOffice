<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    /**
     * @dataProvider providerSetValueExplicit
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testSetValueExplicit($expected, $value, string $dataType): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($value, $dataType);

        self::assertSame($expected, $cell->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function providerSetValueExplicit(): array
    {
        return require 'tests/data/Cell/SetValueExplicit.php';
    }

    /**
     * @dataProvider providerSetValueExplicitException
     *
     * @param mixed $value
     */
    public function testSetValueExplicitException($value, string $dataType): void
    {
        $this->expectException(Exception::class);

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($value, $dataType);
    }

    public function providerSetValueExplicitException(): array
    {
        return require 'tests/data/Cell/SetValueExplicitException.php';
    }

    public function testNoChangeToActiveSheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet 1');
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Sheet 3');
        $sheet1->setCellValue('C1', 123);
        $sheet1->setCellValue('D1', 124);
        $sheet3->setCellValue('A1', "='Sheet 1'!C1+'Sheet 1'!D1");
        $sheet1->setCellValue('A1', "='Sheet 3'!A1");
        $cell = 'A1';
        $spreadsheet->setActiveSheetIndex(0);
        self::assertEquals(0, $spreadsheet->getActiveSheetIndex());
        $value = $spreadsheet->getActiveSheet()->getCell($cell)->getCalculatedValue();
        self::assertEquals(0, $spreadsheet->getActiveSheetIndex());
        self::assertEquals(247, $value);
        $spreadsheet->disconnectWorksheets();
    }

    public function testDestroyWorksheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        self::assertSame($sheet, $cell->getWorksheet());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Worksheet no longer exists');
        $spreadsheet->disconnectWorksheets();
        $cell->getWorksheet();
    }

    public function testDestroyCell1(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        self::assertSame('A1', $cell->getCoordinate());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Coordinate no longer exists');
        $spreadsheet->disconnectWorksheets();
        $cell->getCoordinate();
    }

    public function testDestroyCell2(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        self::assertSame('A1', $cell->getCoordinate());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Coordinate no longer exists');
        $cell->getParent()->delete('A1');
        $cell->getCoordinate();
    }
}
