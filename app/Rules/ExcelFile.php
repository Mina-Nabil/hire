<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExcelFile implements ValidationRule
{
    /**
     * MIME types reported for .xls/.xlsx after the PHP 8.5 / libmagic 5.46 upgrade.
     * OLE workbooks that used to be application/vnd.ms-excel are now application/x-ole-storage,
     * and device exports are often HTML or SpreadsheetML saved with an .xls extension.
     *
     * @var list<string>
     */
    private const MIME_TYPES = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-office',
        'application/x-msexcel',
        'application/msexcel',
        'application/x-ole-storage',
        'application/cdfv2',
        'application/octet-stream',
        'application/zip',
        'application/x-zip-compressed',
        'text/html',
        'text/xml',
        'application/xml',
        'text/plain',
        'text/csv',
        'application/csv',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('The :attribute field must be a file of type: xlsx, xls.');

            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());

        if (! in_array($extension, ['xls', 'xlsx'], true)) {
            $fail('The :attribute field must be a file of type: xlsx, xls.');

            return;
        }

        if (in_array(strtolower((string) $value->guessExtension()), ['xls', 'xlsx'], true)) {
            return;
        }

        $mime = strtolower((string) $value->getMimeType());

        if (! in_array($mime, self::MIME_TYPES, true)) {
            $fail('The :attribute field must be a file of type: xlsx, xls.');
        }
    }
}
