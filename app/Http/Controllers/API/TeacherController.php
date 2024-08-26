<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherController extends BaseController
{
    /**
     * Retrieve the groups of the authenticated student.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeacherGroups()
    {
        // Load only the groups along with main and assistant teachers for the authenticated teacher
        $user = Auth::user()->load(['teacherGroups.mainTeacher', 'teacherGroups.assistantTeacher', 'teacherGroups.students', 'teacherGroups.classes']);

        // You can customize the response to return only the group data if desired
        $groups = $user->groups;

        return $this->sendResponse($groups, 'Teacher groups retrieved successfully.');
    }
}
