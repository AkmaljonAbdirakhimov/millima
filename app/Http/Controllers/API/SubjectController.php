<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends BaseController
{
    /**
     * Display a listing of the subjects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $subjects = Subject::all();
        return $this->sendResponse($subjects, 'Subjects retrieved successfully.');
    }

    /**
     * Store a newly created subject in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $subject = Subject::create($request->only('name'));

        return $this->sendResponse($subject, 'Subject created successfully.');
    }

    /**
     * Display the specified subject.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $subject = Subject::with('groups')->find($id);

        if (!$subject) {
            return $this->sendError('Subject not found.');
        }

        return $this->sendResponse($subject, 'Subject retrieved successfully.');
    }

    /**
     * Update the specified subject in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:subjects,name,' . $id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $subject = Subject::find($id);

        if (!$subject) {
            return $this->sendError('Subject not found.');
        }

        $subject->update($request->only('name'));

        return $this->sendResponse($subject, 'Subject updated successfully.');
    }

    /**
     * Remove the specified subject from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return $this->sendError('Subject not found.');
        }

        $subject->delete();

        return $this->sendResponse([], 'Subject deleted successfully.');
    }
}
