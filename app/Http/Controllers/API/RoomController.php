<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Room;
use App\Models\RoomAvailableHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends BaseController
{
    public function index()
    {
        $rooms = Room::with('availableHours.day')->get();
        return $this->sendResponse($rooms, 'Rooms retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'available_hours' => 'array',
            'available_hours.*.day_id' => 'required|exists:days,id',
            'available_hours.*.start_time' => 'required|date_format:H:i',
            'available_hours.*.end_time' => 'required|date_format:H:i|after:available_hours.*.start_time',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $room = Room::create($request->only(['name', 'description', 'capacity']));

        if ($request->has('available_hours')) {
            foreach ($request->available_hours as $hours) {
                $room->availableHours()->create($hours);
            }
        }

        $room->load('availableHours.day');
        return $this->sendResponse($room, 'Room created successfully.');
    }

    public function show($id)
    {
        $room = Room::with('availableHours.day')->find($id);

        if (is_null($room)) {
            return $this->sendError('Room not found.');
        }

        return $this->sendResponse($room, 'Room retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'sometimes|required|integer|min:1',
            'available_hours' => 'array',
            'available_hours.*.id' => 'sometimes|exists:room_available_hours,id',
            'available_hours.*.day_id' => 'required|exists:days,id',
            'available_hours.*.start_time' => 'required|date_format:H:i',
            'available_hours.*.end_time' => 'required|date_format:H:i|after:available_hours.*.start_time',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $room = Room::find($id);

        if (is_null($room)) {
            return $this->sendError('Room not found.');
        }

        $room->update($request->only(['name', 'description', 'capacity']));

        if ($request->has('available_hours')) {
            $room->availableHours()->delete();
            foreach ($request->available_hours as $hours) {
                $room->availableHours()->create($hours);
            }
        }

        $room->load('availableHours.day');
        return $this->sendResponse($room, 'Room updated successfully.');
    }

    public function destroy($id)
    {
        $room = Room::find($id);

        if (is_null($room)) {
            return $this->sendError('Room not found.');
        }

        $room->delete();

        return $this->sendResponse([], 'Room deleted successfully.');
    }
}