<?php

namespace App\Models\Attendance;

use App\Exceptions\AppException;
use App\Models\Personel\Employee;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Attendance extends Model
{

    protected $fillable = [
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'creator_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    ////static functions
    public static function downloadTemplate()
    {
        $employees = Employee::current()->get();
        $template_file = resource_path('sheets/AttendanceSheet.xlsx');
        $template = IOFactory::load($template_file);
        if (!$template) {
            throw new AppException('Failed to read template file');
        }
        $newFile = $template->copy();

        $employees_sheet = $newFile->getSheet(1);
        $i = 2; //start from 2 because the first row is the header
        foreach ($employees as $employee) {
            $employees_sheet->setCellValue('A' . $i, $employee->name);
            $i++;
        }
        $writer = new Xlsx($newFile);
        $file_path = "attendance_template_" . now()->format('Y-m-d') . ".xlsx";
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }

    public static function getUploadedAttendance($file)
    {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $attendance = [];
        for ($row = 2; $row <= $highestRow; $row++) {

            $employeeName = $sheet->getCell('A' . $row)->getValue();
            if (!$employeeName) continue;
            $attendanceStartDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('B' . $row)->getValue());
            $attendanceEndDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('C' . $row)->getValue());

            $hours = abs(round(Carbon::parse($attendanceEndDate)->diffInHours(Carbon::parse($attendanceStartDate)), 2));

            $employee = Employee::where('name', $employeeName)->first();

            $attendance[] = [
                'employee_id' => $employee?->id ?? "Not Found",
                'employee' => $employee,
                'uploaded_name' => $employeeName,
                'date' => $attendanceStartDate->format('Y-m-d'),
                'start_time' => $attendanceStartDate->format('H:i'),
                'end_time' => $attendanceEndDate->format('H:i'),
                'hours' => $hours,
                "error" => $employee ? false : true,
                'creator_id' => Auth::id(),
            ];
        }

        return $attendance;
    }

    public static function saveAttendance($attendance)
    {
        try {
            DB::transaction(function () use ($attendance) {
                foreach ($attendance as $attendance) {
                    if (!$attendance['error']) {
                        Attendance::create($attendance);
                    }
                }
            });
        } catch (Exception $e) {
            throw new AppException('Failed to save attendance: ' . $e->getMessage());
        }
    }
}
