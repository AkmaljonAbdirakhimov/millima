<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Room;
use App\Models\Group;
use App\Models\GroupClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TimetableController extends BaseController
{
    public function getAvailableRooms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day_id' => 'required|exists:days,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $dayId = $request->day_id;
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

        $availableRooms = Room::whereHas('availableHours', function ($query) use ($dayId, $startTime, $endTime) {
            $query->where('day_id', $dayId)
                ->where('start_time', '<=', $startTime)
                ->where('end_time', '>=', $endTime);
        })->whereDoesntHave('classes', function ($query) use ($dayId, $startTime, $endTime) {
            $query->where('day_id', $dayId)
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<', $startTime)
                                ->where('end_time', '>', $endTime);
                        });
                });
        })->get();

        return $this->sendResponse($availableRooms, 'Available rooms retrieved successfully.');
    }

    public function createGroupClass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'room_id' => 'required|exists:rooms,id',
            'day_id' => 'required|exists:days,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $groupClass = GroupClass::create($request->all());

        return $this->sendResponse($groupClass, 'Group class created successfully.');
    }

    public function getGroupTimetable($groupId)
    {
        $group = Group::with(['classes.room', 'classes.day'])->find($groupId);

        if (!$group) {
            return $this->sendError('Group not found.');
        }

        $timetable = $group->classes->groupBy('day.name')->map(function ($classes) {
            return $classes->map(function ($class) {
                return [
                    'room' => $class->room->name,
                    'start_time' => $class->start_time,
                    'end_time' => $class->end_time,
                ];
            });
        });

        return $this->sendResponse($timetable, 'Group timetable retrieved successfully.');
    }
}