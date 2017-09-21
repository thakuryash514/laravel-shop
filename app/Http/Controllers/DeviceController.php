<?php

namespace App\Http\Controllers;

use App\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    public function registerDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_uid' => 'required',
            'device_type' => 'required',
            'device_name' => 'required',
            'app_version' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Failed to register device, mandatory parameter missing or invalid.'], 422);
        }

        try {
            $device = Device::query()->where('device_uid',$request->input('device_uid'))->first();
            $message = 'Device updated successfully.';
            if (! $device) {
                $device = new Device();
                $message = 'Device registered successfully.';
            }
            $device->fill($request->only([
                'device_uid', 'device_type', 'push_token', 'device_name', 'app_version'
            ]));
            $device->save();
            return response()->json(['message' => $message], 200);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'Please try again later'], 422);
        }
    }
}
