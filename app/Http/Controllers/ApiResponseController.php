<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\OfficeIpAddress;
use App\Models\EmployeesAttendanceRecord;

class ApiResponseController extends Controller
{
    public function APIResponseUserTimeLocation()
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->get('https://champagne-bandicoot-hem.cyclic.app/api/data');

            $jsonData = $response->getBody()->getContents();

            $decodedResponse = json_decode($jsonData, true);

            if ($decodedResponse['error'] === false) {
                $data = $decodedResponse['data'];

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
                            'day_status' => ''
                        ];
                    }

                    if ($totalTime !== null) {
                        $timeInMinutes = $this->convertTimeToMinutes($totalTime);
                        $users[$email]['hours'] += floor($timeInMinutes / 60);
                        $users[$email]['minutes'] += $timeInMinutes % 60;

                        if (!empty($ipAddress)) {
                            $location = OfficeIpAddress::where('router_address', $ipAddress)->value('location');
                            if ($location !== null) {
                                $users[$email]['locations'][$location] = isset($users[$email]['locations'][$location]) ? $users[$email]['locations'][$location] + $timeInMinutes : $timeInMinutes;
                                $users[$email]['timeInOffice'] += $timeInMinutes;
                            } else {
                                $users[$email]['locations']['Remote'] = isset($users[$email]['locations']['Remote']) ? $users[$email]['locations']['Remote'] + $timeInMinutes : $timeInMinutes;
                            }
                        }
                    }

                    if (!empty($date)) {
                        $users[$email]['date'] = $date;
                    }
                }

                //day status
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

                // Convert minutes to hours for Office hours
                foreach ($users as &$user) {
                    if ($user['minutes'] >= 60) {
                        $extraHours = floor($user['minutes'] / 60);
                        $user['hours'] += $extraHours;
                        $user['minutes'] %= 60;
                    }
                }

                return response()->json($users);
            } else {
                return response()->json(['error' => 'API request error'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
