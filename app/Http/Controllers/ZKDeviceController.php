<?php

namespace App\Http\Controllers;

use App\Models\Attendance\DailyPunch;
use App\Models\Personel\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZKDeviceController extends Controller
{
    public function ping(Request $request)
    {
        // Log all incoming data
        Log::info('[ZKTeco Device Request]', [
            'method' => $request->method(),
            'query'  => $request->query(),
            'body'   => $request->getContent(),
            'all'    => $request->all()
        ]);

        // Device Registration Ping (GET)
        if ($request->isMethod('get')) {
            return response('OK', 200);
        }
    }



    public function attendance(Request $request)
    {
        // Log all incoming data
        Log::info('[ZKTeco Device Request]', [
            'method' => $request->method(),
            'query'  => $request->query(),
            'body'   => $request->getContent(),
            'all'    => $request->all()
        ]);

        try {
            if ($request->query('table') !== 'ATTLOG') {
                return response('OK', 200); // Not an attendance log table
            }

            $body = $request->getContent();
            $attendance_logs = explode("\r\n", trim($body));

            $punches_to_insert = [];

            foreach ($attendance_logs as $log) {
                if (empty($log)) continue;

                $parts = explode("\t", $log);
                if (count($parts) < 2) continue;

                $device_id = $parts[0];
                $timestamp = $parts[1];
                $punch_state = $parts[2] ?? null;
                $verify_mode = $parts[3] ?? null;
                $work_code = $parts[4] ?? null;

                $employee = Employee::where('device_id', $device_id)->first();
                if (!$employee) {
                    Log::warning("[ZKTeco] Employee with device_id {$device_id} not found.");
                    continue;
                }

                $punches_to_insert[] = [
                    'employee_id' => $employee->id,
                    'punch_time' => Carbon::parse($timestamp),
                    'punch_state' => $punch_state,
                    'verify_mode' => $verify_mode,
                    'work_code' => $work_code,
                    'raw_log' => $log,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($punches_to_insert)) {
                DailyPunch::insert($punches_to_insert);
                Log::info('[ZKTeco] Successfully inserted ' . count($punches_to_insert) . ' attendance punches.');
            }

        } catch (\Exception $e) {
            Log::error('[ZKTeco] Error processing attendance logs: ' . $e->getMessage(), ['exception' => $e]);
            // Still return OK to the device, so it doesn't keep sending the same data.
        }

        return response('OK', 200);
    }
}
