<?php

namespace App\Http\Controllers;

use App\Models\Attendance\Attendance;
use App\Models\Personel\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        /**
         * 
         * [2025-06-22 21:04:58] production.INFO: [ZKTeco Device Request] {"method":"POST","query":{"SN":"ADZV200961174","table":"OPERLOG","OpStamp":"9999"},"body":"USER PIN=1\tName=Mina Adel\tPri=0\tPasswd=\tCard=972497\tGrp=1\tTZ=0000000100000000\tVerify=-1\tViceCard=
         *        USER PIN=39\tName=Remon\tPri=14\tPasswd=\tCard=9449597\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=3\tName=Hany\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=-1\tViceCard=
         *        USER PIN=4\tName=Angeil\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=-1\tViceCard=
         *        USER PIN=5\tName=FADY\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=13\tName=Kholoud\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=55\tName=Mwalid\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=8\tName=Mariam\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=12\tName=Karim\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=2\tName=Tibian\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=6\tName=Olfat\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=16\tName=Soha\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=9\tName=Michel\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=18\tName=S\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=11\tName=Joneer\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=19\tName=Romany\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=7\tName=Adel\tPri=0\tPasswd=\tCard=4283905\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=10\tName=Shiko\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=14\tName=Amira\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        USER PIN=15\tName=Fady\tPri=0\tPasswd=\tCard=\tGrp=1\tTZ=0000000100000000\tVerify=0\tViceCard=
         *        ","all":{"SN":"ADZV200961174","table":"OPERLOG","OpStamp":"9999"}} 
         */

        return response('OK', 200);
    }
}
