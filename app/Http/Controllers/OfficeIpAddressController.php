<?php

namespace App\Http\Controllers;

use App\Models\OfficeIpAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OfficeIpAddressController extends Controller
{
    public function index()
    {
        $ipAddresses = OfficeIpAddress::all();
        return response()->json($ipAddresses);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ip_address' => 'required|ip',
                'location' => 'required'
            ]);

            $officeIpAddress = OfficeIpAddress::create([
                'router_address' => $request->ip_address,
                'location' => $request->location
            ]);

            return response()->json($officeIpAddress, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(OfficeIpAddress $ipAddress)
    {
        return response()->json($ipAddress);
    }

    public function update(Request $request, OfficeIpAddress $ipAddress): JsonResponse
    {
        try {
            $request->validate([
                'ip_address' => 'required|ip',
                'location' => 'required'
            ]);

            $ipAddress->update([
                'router_address' => $request->ip_address,
                'location' => $request->location
            ]);

            return response()->json($ipAddress);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(OfficeIpAddress $ipAddress)
    {
        $ipAddress->delete();
        return response()->json(null, 204);
    }
}
