<?php

namespace App\Http\Controllers;

use App\Models\Attendance\DailyPunch;
use App\Models\Personel\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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


        return response('OK', 200);
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
                // Process each punch individually to avoid parameter conflicts
                $insertedCount = 0;
                foreach ($punches_to_insert as $punch) {
                    try {
                        DailyPunch::updateOrCreate(
                            [
                                'employee_id' => $punch['employee_id'],
                                'punch_time' => $punch['punch_time'],
                            ],
                            $punch
                        );
                        $insertedCount++;
                    } catch (\Exception $e) {
                        Log::error('[ZKTeco] Error inserting individual punch: ' . $e->getMessage(), [
                            'punch_data' => $punch,
                            'exception' => $e
                        ]);
                    }
                }
                Log::info('[ZKTeco] Successfully processed ' . $insertedCount . ' attendance punches.');
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

            Log::info("[ZKTeco] Processing OPLOG entry", [
                'device_id' => $device_id,
                'timestamp' => $timestamp,
                'punch_state' => $punch_state,
                'verify_mode' => $verify_mode,
                'work_code' => $work_code,
                'raw_log' => $log
            ]);

            // First try to find employee by device_id in employee_info with better debugging
            $employee = Employee::whereHas('info', function ($query) use ($device_id) {
                $query->where('device_id', $device_id);
            })->with('info')->first();

            // If not found, try a direct database lookup to debug the relationship issue
            if (!$employee) {
                Log::info("[ZKTeco] Employee not found by device_id {$device_id} using relationship, trying direct lookup");

                // Direct lookup in employee_info table
                $employeeInfo = DB::table('employee_info')->where('device_id', $device_id)->first();
                if ($employeeInfo) {
                    Log::info("[ZKTeco] Found employee_info record directly", [
                        'employee_id' => $employeeInfo->employee_id,
                        'device_id' => $employeeInfo->device_id
                    ]);
                    $employee = Employee::find($employeeInfo->employee_id);
                    if ($employee) {
                        Log::info("[ZKTeco] Found employee via direct lookup: {$employee->name}");
                    }
                }
            }

            // If still not found, try to find by employee_code as fallback
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
            $punch_time = Carbon::parse($timestamp);
            if(env('BASMA_TIMEZONE')){
                $punch_time = $punch_time->setTimezone(env('BASMA_TIMEZONE'));
                $punch_time->shiftTimezone('Africa/Cairo');
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

    public function getRequest(Request $request)
    {
        $serialNumber = $request->query('SN');
        $requestBody = $request->getContent();
        
        // Log the polling request for debugging
        Log::info('[ZKTeco Device Polling]', [
            'method' => $request->method(),
            'query'  => $request->query(),
            'body'   => $requestBody,
            'SN' => $serialNumber,
            'user_agent' => $request->userAgent()
        ]);
        return response('NO', 200);
        try {
            // Check if this is a command response from the device (has body with ID= and Return=)
            if (!empty($requestBody) && (strpos($requestBody, 'ID=') !== false && strpos($requestBody, 'Return=') !== false)) {
                $this->handleCommandResponse($serialNumber, $requestBody);
                
                // For command responses, just acknowledge with OK
                Log::info('[ZKTeco] Acknowledging command response via getrequest', ['SN' => $serialNumber]);
                return response('OK', 200);
            }

            // This is a command polling request - check if we have commands to send
            $commands = $this->getDeviceCommands($serialNumber);
            
            if (count($commands) > 0) {
                $response = implode("\r\n", $commands);
                Log::info('[ZKTeco] Sending commands to device via getrequest', [
                    'SN' => $serialNumber,
                    'commands' => $commands,
                    'response' => $response
                ]);
                return response($response, 200);
            }
        
        // Return 'NONE' to tell the device there are no commands
        // This should reduce or stop the polling frequency
            return response('NONE', 200);
            
        } catch (\Exception $e) {
            Log::error('[ZKTeco] Error processing getrequest: ' . $e->getMessage(), [
                'SN' => $serialNumber,
                'exception' => $e
            ]);
            return response('NONE', 200); // Return NONE even on error to prevent device loops
        }
    }

    public function deviceCmd(Request $request)
    {
        $serialNumber = $request->query('SN');
        $requestBody = $request->getContent();
        
        // Log all incoming device command requests for debugging
        Log::info('[ZKTeco Device Command Request]', [
            'method' => $request->method(),
            'query'  => $request->query(),
            'body'   => $requestBody,
            'SN' => $serialNumber,
            'user_agent' => $request->userAgent(),
            'all' => $request->all()
        ]);
        return response('OK', 200);
        try {
            // Check if device is registered/authorized (optional validation)
            if (empty($serialNumber)) {
                Log::warning('[ZKTeco] Device command request without serial number');
                return response('ERROR: Missing serial number', 400);
            }

            // Check if this is a command response from the device (has body with ID= and Return=)
            if (!empty($requestBody) && (strpos($requestBody, 'ID=') !== false && strpos($requestBody, 'Return=') !== false)) {
                $this->handleCommandResponse($serialNumber, $requestBody);
                
                // For command responses, just acknowledge with OK
                Log::info('[ZKTeco] Acknowledging command response', ['SN' => $serialNumber]);
                return response('OK', 200);
            }

            // This is a command request - get pending commands for the device
            $commands = $this->getDeviceCommands($serialNumber);
            
            if (count($commands) > 0) {
                $response = implode("\r\n", $commands);
                Log::info('[ZKTeco] Sending commands to device', [
                    'SN' => $serialNumber,
                    'commands' => $commands,
                    'response' => $response
                ]);
                return response($response, 200);
            }

            // If no commands are pending, return NONE
            Log::info('[ZKTeco] No commands pending for device', ['SN' => $serialNumber]);
            return response('NONE', 200);

        } catch (\Exception $e) {
            Log::error('[ZKTeco] Error processing device command request: ' . $e->getMessage(), [
                'SN' => $serialNumber,
                'exception' => $e
            ]);
            return response('ERROR', 500);
        }
    }

    /**
     * Handle command response from device
     */
    private function handleCommandResponse($serialNumber, $responseBody)
    {
        // Parse the response body to understand what command was executed
        // Format appears to be: ID=COMMANDNAME&Return=RETURNCODE&CMD=
        parse_str($responseBody, $parsed);
        
        if (isset($parsed['ID']) && isset($parsed['Return'])) {
            $commandId = $parsed['ID'];
            $returnCode = $parsed['Return'];
            
            // Interpret return codes
            $status = 'unknown';
            $message = '';
            
            switch ($returnCode) {
                case '1':
                    $status = 'success';
                    $message = 'Command executed successfully';
                    break;
                case '-1002':
                    $status = 'error';
                    $message = 'Command format error or invalid parameter';
                    break;
                case '-1003':
                    $status = 'error';
                    $message = 'Command not supported';
                    break;
                case '-1004':
                    $status = 'error';
                    $message = 'Access denied';
                    break;
                default:
                    $status = 'unknown';
                    $message = "Unknown return code: {$returnCode}";
            }
            
            Log::info('[ZKTeco] Device command response received', [
                'SN' => $serialNumber,
                'command_id' => $commandId,
                'return_code' => $returnCode,
                'status' => $status,
                'message' => $message,
                'response_body' => $responseBody
            ]);
            
            // If SETTIME failed with -1002, we might need to adjust the format
            if ($commandId === 'SETTIME' && $returnCode === '-1002') {
                Log::warning('[ZKTeco] SETTIME command failed - trying next format', [
                    'SN' => $serialNumber,
                    'return_code' => $returnCode,
                    'message' => 'Command format error - will try different format on next attempt'
                ]);
                
                // Mark this format as failed and increment the counter
                $failedFormatKey = "time_format_failed_{$serialNumber}";
                $currentFailedCount = cache()->get($failedFormatKey, 0);
                cache()->put($failedFormatKey, $currentFailedCount + 1, 3600);
                
                // Clear the time sync cache so it will try again with new format
                $timeSyncKey = "time_sync_sent_{$serialNumber}";
                cache()->forget($timeSyncKey);
                
                Log::info('[ZKTeco] Will try format #' . (($currentFailedCount + 1) % 6) . ' on next request', [
                    'SN' => $serialNumber,
                    'failed_attempts' => $currentFailedCount + 1
                ]);
            }
            
            // If SETTIME succeeded, save the working format
            if ($commandId === 'SETTIME' && $returnCode === '1') {
                $formatUsedKey = "time_format_used_{$serialNumber}";
                $workingFormat = cache()->get($formatUsedKey, 0);
                
                Log::info('[ZKTeco] SETTIME command succeeded - format works!', [
                    'SN' => $serialNumber,
                    'working_format_index' => $workingFormat
                ]);
                
                // Save the working format permanently
                cache()->put("time_format_working_{$serialNumber}", $workingFormat, 86400); // 24 hours
                cache()->forget("time_format_failed_{$serialNumber}"); // Clear failed counter
            }
            
            // Store command execution result (you can save this to database if needed)
            $cacheKey = "device_cmd_executed_{$serialNumber}_{$commandId}";
            cache()->put($cacheKey, [
                'executed_at' => now(),
                'return_code' => $returnCode,
                'status' => $status,
                'message' => $message,
                'response' => $responseBody
            ], 3600); // Cache for 1 hour
        }
    }

    /**
     * Get pending commands for a specific device
     * You can customize this method based on your requirements
     */
    private function getDeviceCommands($serialNumber)
    {
        $commands = [];

        // Check if we've already sent OPLOG command recently
        $oplogSentKey = "oplog_sent_{$serialNumber}";
        $oplogSent = cache()->get($oplogSentKey, false);

        // Only send OPLOG command if we haven't sent it in the last 5 minutes
        if (!$oplogSent) {
            Log::info('[ZKTeco] Sending OPLOG request to device', ['SN' => $serialNumber]);
            $commands[] = "DATA QUERY OPLOG";

            // Mark OPLOG as sent for 5 minutes
            cache()->put($oplogSentKey, true, 300);
        }

        // Check if we need to send time sync (only if not sent recently or if time is significantly different)
        $timeSyncKey = "time_sync_sent_{$serialNumber}";
        $lastTimeSync = cache()->get($timeSyncKey, null);

        $currentTime = Carbon::now();
        $shouldSendTimeSync = false;

        if (!$lastTimeSync) {
            $shouldSendTimeSync = true;
        } else {
            // Send time sync if last one was more than 1 hour ago
            $lastSyncTime = Carbon::parse($lastTimeSync);
            if ($currentTime->diffInMinutes($lastSyncTime) > 60) {
                $shouldSendTimeSync = true;
            }
        }

        if ($shouldSendTimeSync) {
            // Check if we have a known working format for this device
            $workingFormatKey = "time_format_working_{$serialNumber}";
            $workingFormat = cache()->get($workingFormatKey, null);
            
            // Check if there's a manually forced format
            $forcedFormatKey = "force_format_{$serialNumber}";
            $forcedFormat = cache()->get($forcedFormatKey, null);
            
            // Get the last failed format for this device to try a different one
            $failedFormatKey = "time_format_failed_{$serialNumber}";
            $lastFailedFormat = cache()->get($failedFormatKey, 0);
            
            // Different time formats that ZKTeco devices might accept
            $timeFormats = [
                // Format 0: Standard format with colons and dashes
                [
                    'format' => 'Y-m-d H:i:s',
                    'command' => "C:{$serialNumber}:SETTIME {time}",
                    'description' => 'Standard format: C:SN:SETTIME YYYY-MM-DD HH:MM:SS'
                ],
                // Format 1: With slashes instead of dashes
                [
                    'format' => 'Y/m/d H:i:s', 
                    'command' => "C:{$serialNumber}:SETTIME {time}",
                    'description' => 'Slash format: C:SN:SETTIME YYYY/MM/DD HH:MM:SS'
                ],
                // Format 2: US format
                [
                    'format' => 'm/d/Y H:i:s',
                    'command' => "C:{$serialNumber}:SETTIME {time}",
                    'description' => 'US format: C:SN:SETTIME MM/DD/YYYY HH:MM:SS'
                ],
                // Format 3: Without seconds
                [
                    'format' => 'Y-m-d H:i',
                    'command' => "C:{$serialNumber}:SETTIME {time}",
                    'description' => 'No seconds: C:SN:SETTIME YYYY-MM-DD HH:MM'
                ],
                // Format 4: Simple SETTIME without C:SN prefix
                [
                    'format' => 'Y-m-d H:i:s',
                    'command' => "SETTIME {time}",
                    'description' => 'Simple format: SETTIME YYYY-MM-DD HH:MM:SS'
                ],
                // Format 5: Unix timestamp
                [
                    'format' => 'timestamp',
                    'command' => "C:{$serialNumber}:SETTIME {time}",
                    'description' => 'Unix timestamp: C:SN:SETTIME 1735545612'
                ],
                // Format 6: MB10-VL specific format (newer devices)
                [
                    'format' => 'Y-m-d H:i:s',
                    'command' => "CMD=SETTIME&SN={$serialNumber}&TIME={time}",
                    'description' => 'MB10-VL format: CMD=SETTIME&SN=xxx&TIME=YYYY-MM-DD HH:MM:SS'
                ],
                // Format 7: Alternative newer format
                [
                    'format' => 'YmdHis',
                    'command' => "C:{$serialNumber}:SETTIME {time}",
                    'description' => 'Compact format: C:SN:SETTIME YYYYMMDDHHMMSS'
                ],
                // Format 8: ISO 8601 format (T separator)
                [
                    'format' => 'Y-m-d\TH:i:s',
                    'command' => "C:{$serialNumber}:SETTIME {time}",
                    'description' => 'ISO format: C:SN:SETTIME YYYY-MM-DDTHH:MM:SS'
                ]
            ];
            
            // Use working format if we have one, otherwise try the next format after failures
            if ($workingFormat !== null) {
                $formatIndex = $workingFormat;
                Log::info('[ZKTeco] Using known working format', ['SN' => $serialNumber, 'format_index' => $formatIndex]);
            } elseif ($forcedFormat !== null) {
                $formatIndex = $forcedFormat;
                Log::info('[ZKTeco] Using manually forced format', ['SN' => $serialNumber, 'format_index' => $formatIndex]);
                // Clear the forced format after using it once
                cache()->forget($forcedFormatKey);
            } else {
                $formatIndex = $lastFailedFormat % count($timeFormats);
                Log::info('[ZKTeco] Trying format (no working format known)', ['SN' => $serialNumber, 'format_index' => $formatIndex, 'failed_attempts' => $lastFailedFormat]);
                
                // For MB10-VL devices, start with format 6 (newer format) instead of 0
                if ($lastFailedFormat == 0 && strpos($serialNumber, 'ADZV') === 0) {
                    $formatIndex = 6; // Try MB10-VL specific format first
                    Log::info('[ZKTeco] MB10-VL device detected - trying newer format first', ['SN' => $serialNumber, 'format_index' => $formatIndex]);
                }
            }
            
            $selectedFormat = $timeFormats[$formatIndex];
            
            // Generate time string based on format
            if ($selectedFormat['format'] === 'timestamp') {
                $timeString = $currentTime->timestamp;
            } else {
                $timeString = $currentTime->format($selectedFormat['format']);
            }
            
            $timeCommand = str_replace('{time}', $timeString, $selectedFormat['command']);
            $commands[] = $timeCommand;
            
            // Mark time sync as sent and store which format we tried
            cache()->put($timeSyncKey, $currentTime->toDateTimeString(), 3600);
            cache()->put("time_format_used_{$serialNumber}", $formatIndex, 3600);
            
            Log::info('[ZKTeco] Sending time sync to device', [
                'SN' => $serialNumber,
                'time' => $timeString,
                'format_index' => $formatIndex,
                'format_description' => $selectedFormat['description'],
                'command' => $timeCommand,
                'using_working_format' => ($workingFormat !== null),
                'previous_failed_attempts' => $lastFailedFormat
            ]);
        }

        // You can add more conditional commands based on your needs:
        // $commands[] = "C:{$serialNumber}:RESTART";  // Restart device
        // $commands[] = "C:{$serialNumber}:CLEAR DATA"; // Clear attendance data
        // $commands[] = "C:{$serialNumber}:ENABLE DEVICE"; // Enable device
        // $commands[] = "C:{$serialNumber}:DISABLE DEVICE"; // Disable device

        // Alternative OPLOG request formats (uncomment if needed):
        // $commands[] = "DATA QUERY OPLOG 0";  // Request starting from record 0
        // $commands[] = "GET OPLOG";  // Simple get command

        // You could also check a database table for pending commands
        // Example: Check a 'device_commands' table for pending commands for this device
        /*
        $pendingCommands = DB::table('device_commands')
            ->where('device_serial', $serialNumber)
            ->where('executed', false)
            ->get();
            
        foreach ($pendingCommands as $cmd) {
            $commands[] = $cmd->command;
            // Mark as executed
            DB::table('device_commands')
                ->where('id', $cmd->id)
                ->update(['executed' => true, 'executed_at' => now()]);
        }
        */

        return $commands;
    }

    /**
     * Helper method to manually force a specific time format for testing
     * Usage: Call this method to test a specific format for your device
     */
    public function forceTimeFormat($serialNumber, $formatIndex = 6)
    {
        // Clear any existing format restrictions
        cache()->forget("time_format_working_{$serialNumber}");
        cache()->forget("time_format_failed_{$serialNumber}");
        cache()->forget("time_sync_sent_{$serialNumber}");
        
        // Force the specific format
        cache()->put("force_format_{$serialNumber}", $formatIndex, 300); // 5 minutes
        
        Log::info('[ZKTeco] Manually forcing time format', [
            'SN' => $serialNumber,
            'forced_format_index' => $formatIndex,
            'duration' => '5 minutes'
        ]);
        
        return "Format {$formatIndex} will be tried on next device request";
    }
}
