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
     * Retrieve the groups of the authenticated teacher.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeacherGroups()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Load the groups where the user is the main teacher
        $groups = $user->teacherGroups()->with(['mainTeacher', 'assistantTeacher', 'students', 'classes.day', 'classes.room'])->get();

        return $this->sendResponse($groups, 'Teacher groups retrieved successfully.');
    }
}
