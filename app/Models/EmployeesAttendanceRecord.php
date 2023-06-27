<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeesAttendanceRecord extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'hours', 'minutes', 'time_in_office', 'locations', 'date', 'day_status'];
}
