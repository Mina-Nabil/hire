<?php

namespace Tests\Feature;

use App\Rules\ExcelFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ExcelFileRuleTest extends TestCase
{
    public function test_accepts_legacy_xls_that_php_8_5_detects_as_ole_storage(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'xls');
        file_put_contents($path, hex2bin('d0cf11e0a1b11ae1').str_repeat("\0", 512));

        $file = new UploadedFile($path, 'October26.xls', 'application/octet-stream', null, true);

        $this->assertSame('application/x-ole-storage', $file->getMimeType());

        $legacy = Validator::make(
            ['file' => $file],
            ['file' => 'required|file|mimes:xlsx,xls']
        );

        $this->assertTrue($legacy->fails());

        $validator = Validator::make(
            ['file' => $file],
            ['file' => ['required', 'file', new ExcelFile]]
        );

        $this->assertTrue($validator->passes(), implode(' ', $validator->errors()->all()));
    }

    public function test_accepts_xlsx_workbook(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'device');
        (new Xlsx($spreadsheet))->save($path);

        $file = new UploadedFile($path, 'October26.xlsx', null, null, true);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => ['required', 'file', new ExcelFile]]
        );

        $this->assertTrue($validator->passes(), implode(' ', $validator->errors()->all()));
    }

    public function test_rejects_a_non_spreadsheet_renamed_to_xls(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($path, "%PDF-1.4\n");

        $file = new UploadedFile($path, 'October26.xls', 'application/pdf', null, true);

        $validator = Validator::make(
            ['file' => $file],
            ['file' => ['required', 'file', new ExcelFile]]
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'The file field must be a file of type: xlsx, xls.',
            $validator->errors()->first('file')
        );
    }
}
