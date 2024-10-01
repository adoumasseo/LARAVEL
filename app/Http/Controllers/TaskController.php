<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Return a Json of all tasks
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json([
            'tasks' => $tasks,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, int $id)
    {
        $board = Board::find($id);
        if (!$board)
        {
            return response()->json([
                "message" => "No task with that ID"
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'content' => "required|string|min:1",
            'status' => 'sometimes|required|in:todo,doing,done'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $task = Task::create([
            'content' => $request->content,
            'status' => $request->status ?? 'todo',
            'board_id' => $board->id,
        ]);
        return response()->json([
            'task' => $task,
            'message' => 'task created successfully',
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $task = Task::find($id);
        if (!$task)
        {
            return response()->json([
                'message' => 'No Task with this ID found'
            ], 404);
        }
        return response()->json([
            'task' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if (!$task)
        {
            return response()->json([
                "message" => "No task found with this ID"
            ]);
        }
        $validator = Validator::make($request->all(), [
            'content' => "sometimes|required|string|min:1",
            'status' => 'sometimes|required|in:todo,doing,done',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if ($request->has('content')) {
            $task->content = $request->content;
        }
        if ($request->has('status')) {
            $task->status = $request->status;
        }
        $task->save();
        return response()->json([
            'message' => 'task updated successfully',
            'task' => $task
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'No task with this ID found'
            ], 404);
        }
        $task->delete();
        return response()->json([
            'message' => 'task delete successfully'
        ], 204);
    }
}
