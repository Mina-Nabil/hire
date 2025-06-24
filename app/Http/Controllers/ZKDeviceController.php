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
            if ($request->query('table') !== 'OPERLOG' && $request->query('table') !== 'ATTLOG') {
                Log::info('[ZKTeco] Not an attendance log table: ' . $request->query('table'));
                return response('OK', 200); // Not an attendance log table
            }

            $body = $request->getContent();
            
            // Handle new format that starts with "OPLOG"
            if (str_starts_with(trim($body), 'OPLOG')) {
                $punches_to_insert = $this->processOplogFormat($body);
            } else {
                // Handle legacy format
                $punches_to_insert = $this->processLegacyFormat($body);
            }

            if (!empty($punches_to_insert)) {
                DailyPunch::insert($punches_to_insert);
                Log::info('[ZKTeco] Successfully inserted ' . count($punches_to_insert) . ' attendance punches.');
            } else {
                Log::info('[ZKTeco] No attendance punches to insert.');
            }

        } catch (\Exception $e) {
            Log::error('[ZKTeco] Error processing attendance logs: ' . $e->getMessage(), ['exception' => $e]);
            // Still return OK to the device, so it doesn't keep sending the same data.
        }

        return response('OK', 200);
    }

    /**
     * Process the new OPLOG format
     * Format: "OPLOG 3\t0\t2025-06-23 16:42:03\t53\t0\t0\t0\t"
     * Parts: [OPLOG, device_id, ?, timestamp, ?, ?, ?, ?]
     */
    private function processOplogFormat($body)
    {
        $punches_to_insert = [];
        $attendance_logs = explode("\r\n", trim($body));

        foreach ($attendance_logs as $log) {
            if (empty($log)) continue;

            $parts = explode("\t", $log);
            if (count($parts) < 4) {
                Log::warning("[ZKTeco] Invalid OPLOG format: {$log}");
                continue;
            }

            // Skip if it's not an OPLOG entry
            if ($parts[0] !== 'OPLOG') {
                continue;
            }

            $device_id = $parts[1];
            $timestamp = $parts[3]; // timestamp is at index 3
            $punch_state = $parts[4] ?? null;
            $verify_mode = $parts[5] ?? null;
            $work_code = $parts[6] ?? null;

            $employee = Employee::whereHas('info', function($query) use ($device_id) {
                $query->where('device_id', $device_id);
            })->first();
            
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

        return $punches_to_insert;
    }

    /**
     * Process the legacy format
     * Format: "device_id\ttimestamp\tpunch_state\tverify_mode\twork_code"
     */
    private function processLegacyFormat($body)
    {
        $punches_to_insert = [];
        $attendance_logs = explode("\r\n", trim($body));

        foreach ($attendance_logs as $log) {
            if (empty($log)) continue;

            $parts = explode("\t", $log);
            if (count($parts) < 2) continue;

            $device_id = $parts[0];
            $timestamp = $parts[1];
            $punch_state = $parts[2] ?? null;
            $verify_mode = $parts[3] ?? null;
            $work_code = $parts[4] ?? null;

            $employee = Employee::whereHas('info', function($query) use ($device_id) {
                $query->where('device_id', $device_id);
            })->first();
            
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

        return $punches_to_insert;
    }
}
