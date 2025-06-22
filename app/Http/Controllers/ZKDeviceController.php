<?php

namespace App\Http\Controllers;

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
        // Attendance Data (POST) - Usually contains `table=ATTLOG`
        // if ($request->isMethod('post')) {
        //     $table = $request->input('table');

        //     if ($table === 'ATTLOG') {
        //         // Extract relevant fields
        //         $userId     = $request->input('UserID');
        //         $checkTime  = $request->input('CheckTime');
        //         $checkType  = $request->input('CheckType'); // 'I' = In, 'O' = Out
        //         $verifyMode = $request->input('VerifyMode');
        //         $sn         = $request->input('SN');

        //         // Save to DB - create a table if needed
        //         DB::table('zk_attendance_logs')->insert([
        //             'serial_number' => $sn,
        //             'user_id'       => $userId,
        //             'check_time'    => $checkTime,
        //             'check_type'    => $checkType,
        //             'verify_mode'   => $verifyMode,
        //             'created_at'    => now(),
        //             'updated_at'    => now(),
        //         ]);
        //     }

        //     return response('OK', 200);
        // }
    }
}
