<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class GroupController extends BaseController
{

    /**
     * Retrieve all groups with filtering.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Group::with([
            'mainTeacher',
            'assistantTeacher',
            'students',
            'classes.day',
            'classes.room',
            'subject'
        ]);

        // Dynamically apply filters
        foreach ($request->all() as $field => $value) {
            // Apply filter if field exists in groups table or related models
            if (Schema::hasColumn('groups', $field)) {
                $query->where($field, 'like', "%{$value}%");
            } elseif ($field == 'main_teacher') {
                $query->whereHas('mainTeacher', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            } elseif ($field == 'assistant_teacher') {
                $query->whereHas('assistantTeacher', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            } elseif ($field == 'student_name') {
                $query->whereHas('students', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            }
        }

        // Pagination or return all groups if no pagination is specified
        $groups = $request->has('per_page') ? $query->paginate($request->get('per_page')) : $query->get();

        return $this->sendResponse($groups, 'Groups retrieved successfully.');
    }

    // Admin creates a new group
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'main_teacher_id' => 'nullable|exists:users,id',
            'assistant_teacher_id' => 'nullable|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $group = Group::create($request->only(['name', 'main_teacher_id', 'assistant_teacher_id', 'subject_id']));

        return $this->sendResponse($group, 'Group created successfully.');
    }

    // Admin adds students to a group
    public function addStudents(Request $request, $groupId)
    {
        $validator = Validator::make($request->all(), [
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $group = Group::find($groupId);

        if (!$group) {
            return $this->sendError('Group not found.');
        }


        // Sync students with the group, if students list is provided
        if ($request->has('students')) {
            $group->students()->sync($request->students);
        } else {
            // Clear all students from the group if an empty list is provided
            $group->students()->sync([]);
        }

        return $this->sendResponse($group->students, 'Students added to group successfully.');
    }

    // Admin updates a group (e.g., assigning teachers)
    public function update(Request $request, $groupId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'main_teacher_id' => 'nullable|exists:users,id',
            'assistant_teacher_id' => 'nullable|exists:users,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $group = Group::find($groupId);

        if (!$group) {
            return $this->sendError('Group not found.');
        }

        $group->update($request->only(['name', 'main_teacher_id', 'assistant_teacher_id', 'subject_id']));

        return $this->sendResponse($group, 'Group updated successfully.');
    }

    /**
     * Delete a specific group by its ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $group = Group::find($id);

        if (!$group) {
            return $this->sendError('Group not found.');
        }

        $group->delete();

        return $this->sendResponse([], 'Group deleted successfully.');
    }
}
