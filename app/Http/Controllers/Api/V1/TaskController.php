<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function store(TaskStoreRequest $request)
    {
        $createdTask = Task::create($request->validated());
        return new TaskResource($createdTask);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function update(TaskUpdateRequest $request, Task $task)
    {
        $task->update($request->validated());
        return new TaskResource($task);
    }


    public function destroy(Task $task)
    {
        $task->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
