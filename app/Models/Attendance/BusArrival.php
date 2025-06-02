<?php

namespace App\Models\Attendance;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BusArrival extends Model
{

    const MORPH_NAME = 'bus_arrival';

    const BUS_ARRIVAL_TIME_OFFSET = 15;
    
    protected $fillable = ['bus_id', 'date', 'time'];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }


    ////static functions
    public static function downloadTemplate()
    {
        $buses = Bus::all();
        $template_file = resource_path('sheets/BusSheet.xlsx');
        $template = IOFactory::load($template_file);
        if (!$template) {
            throw new AppException('Failed to read template file');
        }
        $newFile = $template->copy();

        $employees_sheet = $newFile->getSheet(1);
        $i = 2; //start from 2 because the first row is the header
        foreach ($buses as $bus) {
            $employees_sheet->setCellValue('A' . $i, $bus->name);
            $i++;
        }
        $writer = new Xlsx($newFile);
        $file_path = "bus_arrival_template_" . now()->format('Y-m-d') . ".xlsx";
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        AppLog::info('Bus Arrival Template Downloaded', ['file_path' => $file_path]);
        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }

    public static function getUploadedBusArrival($file)
    {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $busArrivals = [];
        for ($row = 2; $row <= $highestRow; $row++) {

            $busName = trim($sheet->getCell('A' . $row)->getValueString());
            if (!$busName) continue;

            if(!$sheet->getCell('B' . $row)->getValue()) continue; //if the start time is empty, skip the row
            $arrivalTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet->getCell('B' . $row)->getValue());
          

            $bus = Bus::where('name', $busName)->first();
            if(!$bus) {
                $busArrivals[] = [
                    'bus_id' => "Not Found",
                    'bus' => null,
                    'uploaded_name' => $busName,
                    'date' => $arrivalTime->format('Y-m-d'),
                    'time' => $arrivalTime->format('H:i'),
                    'creator_id' => Auth::id(),
                ];
                continue;
            }


            $busArrivals[] = [
                'bus_id' => $bus->id,
                'bus' => $bus,
                'uploaded_name' => $busName,
                'date' => $arrivalTime->format('Y-m-d'),
                'time' => $arrivalTime->format('H:i'),
                'creator_id' => Auth::id(),
            ];
        }

        AppLog::info('Uploaded Bus Arrival');
        return $busArrivals;
    }

    public static function saveBusArrival($busArrivals)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if(!$loggedInUser->can('create', BusArrival::class)) {
            throw new AppException('You are not authorized to create bus arrivals');
        }


        if(count($busArrivals) == 0) {
            throw new AppException('No bus arrivals to save');
        }

        try {
            DB::transaction(function () use ($busArrivals) {
                foreach ($busArrivals as $busArrival) {
                    self::createBusArrival($busArrival['bus_id'], $busArrival['date'], $busArrival['time']);
                }
            });
            AppLog::info('Saved Bus Arrival');
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to save bus arrival', $e->getMessage());
            throw new AppException('Failed to save bus arrival: ' . $e->getMessage());
        }
    }


    /**
     * Create a bus arrival
     *
     * @param string $busName
     * @param string $date
     * @param string $time
     * @return bool
     */
    public static function createBusArrival($busID, $date, $time)
    {

        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if(!$loggedInUser->can('create', BusArrival::class)) {
            throw new AppException('You are not authorized to create bus arrivals');
        }

        $bus = Bus::where('id', $busID)->first();
        if (!$bus) {
            throw new AppException('Bus not found');
        }

        try {
            DB::transaction(function () use ($bus, $date, $time) {
                AppLog::info("Creating bus {$bus->name} arrival for {$date} at {$time}");

                self::create([
                    'bus_id' => $bus->id,
                    'date' => $date,
                    'time' => $time,
                ]);
            });

            return true;
        } catch (Exception $e) {
            AppLog::error('Failed to create bus arrival', $e->getMessage());
            throw new AppException('Failed to create bus arrival');
        }
    }
}
