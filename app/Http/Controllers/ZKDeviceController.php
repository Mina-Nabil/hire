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
            if ($request->query('table') === 'OPERLOG') {
                return response("GET ATTLOG FROM 0\n", 200); // force full resend
            }
        } else {
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

            // Log total employees for debugging
            $totalEmployees = Employee::count();
            $employeesWithDeviceId = Employee::whereHas('info', function ($query) {
                $query->whereNotNull('device_id');
            })->count();
            Log::info('[ZKTeco] Employee count for debugging', [
                'total_employees' => $totalEmployees,
                'employees_with_device_id' => $employeesWithDeviceId
            ]);

            $body = $request->getContent();

            // Add detailed logging for debugging
            Log::info('[ZKTeco] Processing attendance request', [
                'body_length' => strlen($body),
                'body_trimmed' => trim($body),
                'body_starts_with_OPLOG' => str_starts_with(trim($body), 'OPLOG'),
                'body_lines' => explode("\r\n", trim($body))
            ]);

            // Handle new format that starts with "OPLOG"
            if (str_starts_with(trim($body), 'OPLOG')) {
                $punches_to_insert = $this->processOplogFormat($body);
            } else {
                // Handle legacy format
                $punches_to_insert = $this->processLegacyFormat($body);
            }

            if (!empty($punches_to_insert)) {
                DailyPunch::updateOrCreate($punches_to_insert);
                Log::info('[ZKTeco] Successfully inserted ' . count($punches_to_insert) . ' attendance punches.');
            } else {
                Log::info('[ZKTeco] No attendance punches to insert.');
            }
        } catch (\Exception $e) {
            Log::error('[ZKTeco] Error processing attendance logs: ' . $e->getMessage(), ['exception' => $e]);
            // Still return OK to the device, so it doesn't keep sending the same data.
        }
        return response("GET ATTLOG FROM 0\n", 200); // force full resend
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

            Log::info("[ZKTeco] Processing OPLOG entry", [
                'device_id' => $device_id,
                'timestamp' => $timestamp,
                'punch_state' => $punch_state,
                'verify_mode' => $verify_mode,
                'work_code' => $work_code,
                'raw_log' => $log
            ]);

            // First try to find employee by device_id in employee_info
            $employee = Employee::whereHas('info', function ($query) use ($device_id) {
                $query->where('device_id', $device_id);
            })->first();

            // If not found, try to find by employee_code as fallback
            if (!$employee) {
                Log::info("[ZKTeco] Employee not found by device_id {$device_id}, trying employee_code");
                $employee = Employee::whereHas('info', function ($query) use ($device_id) {
                    $query->where('employee_code', $device_id);
                })->first();
            }

            // If still not found, try to find by employee ID as fallback (device might be sending user IDs)
            if (!$employee) {
                Log::info("[ZKTeco] Employee not found by employee_code {$device_id}, trying employee ID");
                $employee = Employee::find($device_id);
            }

            // If still not found, log all available device_ids for debugging
            if (!$employee) {
                Log::warning("[ZKTeco] Employee with device_id {$device_id} not found. Available device_ids:");
                $allEmployees = Employee::with('info')->get();
                foreach ($allEmployees as $emp) {
                    if ($emp->info && $emp->info->device_id) {
                        Log::warning("[ZKTeco] Employee {$emp->name} has device_id: {$emp->info->device_id}");
                    }
                }
                continue;
            }

            Log::info("[ZKTeco] Found employee", [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'device_id' => $employee->info->device_id ?? 'null'
            ]);

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

            Log::info("[ZKTeco] Processing legacy format entry", [
                'device_id' => $device_id,
                'timestamp' => $timestamp,
                'punch_state' => $punch_state,
                'verify_mode' => $verify_mode,
                'work_code' => $work_code,
                'raw_log' => $log
            ]);

            // First try to find employee by device_id in employee_info
            $employee = Employee::whereHas('info', function ($query) use ($device_id) {
                $query->where('device_id', $device_id);
            })->first();


            // If still not found, log all available device_ids for debugging
            if (!$employee) {
                Log::warning("[ZKTeco] Employee with device_id {$device_id} not found. Available device_ids:");
                $allEmployees = Employee::with('info')->get();
                foreach ($allEmployees as $emp) {
                    if ($emp->info && $emp->info->device_id) {
                        Log::warning("[ZKTeco] Employee {$emp->name} has device_id: {$emp->info->device_id}");
                    }
                }
                continue;
            }

            Log::info("[ZKTeco] Found employee", [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'device_id' => $employee->info->device_id ?? 'null'
            ]);

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
