<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends BaseController
{
    /**
     * Display a listing of the rooms.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::all();
        return $this->sendResponse($rooms, 'Rooms retrieved successfully.');
    }

    /**
     * Store a newly created room in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:rooms',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $room = Room::create($request->all());
        return $this->sendResponse($room, 'Room created successfully.');
    }

    /**
     * Display the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = Room::find($id);
        if (is_null($room)) {
            return $this->sendError('Room not found.');
        }
        return $this->sendResponse($room, 'Room retrieved successfully.');
    }

    /**
     * Update the specified room in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (is_null($room)) {
            return $this->sendError('Room not found.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:rooms,name,' . $id,
            'description' => 'nullable|string',
            'capacity' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $room->update($request->all());
        return $this->sendResponse($room, 'Room updated successfully.');
    }

    /**
     * Remove the specified room from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
