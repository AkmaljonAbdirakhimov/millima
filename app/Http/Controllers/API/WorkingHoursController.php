<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\WorkingHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkingHoursController extends BaseController
{
    public function index()
    {
        $workingHours = WorkingHours::with('day')->get();
        return $this->sendResponse($workingHours, 'Working hours retrieved successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'working_hours' => 'required|array',
            'working_hours.*.id' => 'required|exists:working_hours,id',
            'working_hours.*.is_working_day' => 'required|boolean',
            'working_hours.*.opening_time' => 'nullable|required_if:working_hours.*.is_working_day,true|date_format:H:i',
            'working_hours.*.closing_time' => 'nullable|required_if:working_hours.*.is_working_day,true|date_format:H:i|after:working_hours.*.opening_time',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            foreach ($request->working_hours as $workingHourData) {
                $workingHour = WorkingHours::findOrFail($workingHourData['id']);

                $data = [
                    'is_working_day' => $workingHourData['is_working_day'],
                    'opening_time' => $workingHourData['is_working_day'] ? $workingHourData['opening_time'] : null,
                    'closing_time' => $workingHourData['is_working_day'] ? $workingHourData['closing_time'] : null,
                ];

                $workingHour->update($data);
            }

            DB::commit();

            $updatedWorkingHours = WorkingHours::with('day')->get();

            return $this->sendResponse($updatedWorkingHours, 'Working hours updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Update Error.', $e->getMessage());
        }
    }
}
