<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\OfficeIpAddress;
use App\Models\EmployeesAttendanceRecord;

class ApiResponseController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function APIResponseUserTimeLocation()
    {
        try {
            $response = $this->client->get('https://champagne-bandicoot-hem.cyclic.app/api/data');

            $jsonData = $response->getBody()->getContents();

            $decodedResponse = json_decode($jsonData, true);

            if ($decodedResponse['error'] === false) {
                $data = $decodedResponse['data'];

                $users = $this->processUserTimeLocationData($data);

                $this->calculateDayStatus($users);

                $this->convertMinutesToHours($users);

                $this->storeAttendanceRecords($users);

                return response()->json($users);
            } else {
                return response()->json(['error' => 'API request error'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function processUserTimeLocationData($data)
    {
        $users = [];

        foreach ($data as $entry) {
            $email = $entry['email'];
            $totalTime = $entry['total_time'];
            $ipAddress = $entry['ip_address'];
            $date = $entry['date'];

            if (!isset($users[$email])) {
                $users[$email] = [
                    'hours' => 0,
                    'minutes' => 0,
                    'timeInOffice' => 0,
                    'locations' => [],
                    'day_status' => '',
                    'date' => $date,
                ];
            }

            if ($totalTime !== null) {
                list($hours, $minutes) = explode(':', $totalTime);
                $users[$email]['hours'] += (int) $hours;
                $users[$email]['minutes'] += (int) $minutes;

                if (!empty($ipAddress)) {
                    $location = OfficeIpAddress::where('router_address', $ipAddress)->value('location');
                    if ($location !== null) {
                        $users[$email]['locations'][$location] = isset($users[$email]['locations'][$location]) ? $users[$email]['locations'][$location] + $this->convertTimeToMinutes($totalTime) : $this->convertTimeToMinutes($totalTime);
                        $users[$email]['timeInOffice'] += $this->convertTimeToMinutes($totalTime);
                    } else {
                        $users[$email]['locations']['Remote'] = isset($users[$email]['locations']['Remote']) ? $users[$email]['locations']['Remote'] + $this->convertTimeToMinutes($totalTime) : $this->convertTimeToMinutes($totalTime);
                    }
                }
            }
        }

        return $users;
    }

    private function calculateDayStatus(&$users)
    {
        foreach ($users as &$user) {
            $totalMinutes = $user['timeInOffice'];

            if ($totalMinutes >= 300) {
                $user['day_status'] = 'Present';
            } elseif ($totalMinutes > 180 && $totalMinutes < 300) {
                $user['day_status'] = 'Half Day';
            } else {
                $user['day_status'] = 'Absent';
            }
        }
    }

    private function convertMinutesToHours(&$users)
    {
        foreach ($users as &$user) {
            if ($user['minutes'] >= 60) {
                $extraHours = floor($user['minutes'] / 60);
                $user['hours'] += $extraHours;
                $user['minutes'] %= 60;
            }
        }
    }

    private function storeAttendanceRecords($users)
    {
        foreach ($users as $email => $data) {
            $existingRecord = EmployeesAttendanceRecord::where('email', $email)->where('date', $data['date'])->first();

            if (!$existingRecord) {
                $attendanceRecord = new EmployeesAttendanceRecord();
                $attendanceRecord->email = $email;
                $attendanceRecord->hours = $data['hours'];
                $attendanceRecord->minutes = $data['minutes'];
                $attendanceRecord->time_in_office = $data['timeInOffice'];
                $attendanceRecord->locations = json_encode($data['locations']);
                $attendanceRecord->date = $data['date'];
                $attendanceRecord->day_status = $data['day_status'];
                $attendanceRecord->save();
            }
        }
    }

    private function convertTimeToMinutes($time)
    {
        if ($time === null) {
            return 0;
        }

        list($hours, $minutes) = explode(':', $time);
        return ($hours * 60) + $minutes;
    }
}
