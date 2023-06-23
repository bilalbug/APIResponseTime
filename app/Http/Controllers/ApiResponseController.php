<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
                            'ips' => [],
                            'dates' => []
                        ];
                    }

                    if ($totalTime !== null) {
                        $timeInMinutes = $this->convertTimeToMinutes($totalTime);
                        $users[$email]['hours'] += floor($timeInMinutes / 60);
                        $users[$email]['minutes'] += $timeInMinutes % 60;
                    }

                    if (!empty($ipAddress)) {
                        if (!isset($users[$email]['ips'][$ipAddress])) {
                            $users[$email]['ips'][$ipAddress] = 0;
                        }
                        $users[$email]['ips'][$ipAddress] += $timeInMinutes;
                    }

                    if (!empty($date)) {
                        if (!in_array($date, $users[$email]['dates'])) {
                            $users[$email]['dates'][] = $date;
                        }
                    }
                }

                // Convert minutes to hours if necessary
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
