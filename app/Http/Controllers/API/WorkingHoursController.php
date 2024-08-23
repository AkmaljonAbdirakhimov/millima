<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\WorkingHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkingHoursController extends BaseController
{
    public function index()
    {
        $workingHours = WorkingHours::with('day')->get();
        return $this->sendResponse($workingHours, 'Working hours retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'is_working_day' => 'required|boolean',
            'opening_time' => 'nullable|required_if:is_working_day,true|date_format:H:i',
            'closing_time' => 'nullable|required_if:is_working_day,true|date_format:H:i|after:opening_time',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $workingHours = WorkingHours::findOrFail($id);

        $data = $request->only(['is_working_day', 'opening_time', 'closing_time']);

        // If it's not a working day, set times to null
        if (!$data['is_working_day']) {
            $data['opening_time'] = null;
            $data['closing_time'] = null;
        }

        $workingHours->update($data);

        // Load the related day
        $workingHours->load('day');

        return $this->sendResponse($workingHours, 'Working hours updated successfully.');
    }
}