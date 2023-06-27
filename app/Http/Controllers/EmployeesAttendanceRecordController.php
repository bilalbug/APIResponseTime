<?php

namespace App\Http\Controllers;

use App\Models\EmployeesAttendanceRecord;
use Illuminate\Http\Request;

class EmployeesAttendanceRecordController extends Controller
{
    public function index()
    {
        $records = EmployeesAttendanceRecord::all();

        return response()->json($records);
    }
}
