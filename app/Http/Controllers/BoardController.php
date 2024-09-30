<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = auth()->user();
        $boards = Board::where('user_id', $currentUser->id)->get();

        return response()->json([
            'boards' => $boards,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $currentUser = auth()->user();
        $validator = Validator::make($request->all(), [
            'board_name' => "required|string|max:255",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $board = Board::create([
            "board_name" => $request->board_name,
            "status" => "active",
            "user_id" => $currentUser->id,
        ]);
        
        return response()->json([
            'board' => $board,
            'message' => 'board create succesfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Board $board)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Board $board)
    {
        if (!$board) {
            return response()->json([
                'message' => 'No board with this ID found'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'board_name' => "sometimes|required|string|max:255",
            'status' => 'sometimes|required|in:active,archived',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('board_name')) {
            $board->board_name = $request->board_name;
        }
        if ($request->has('status')) {
            $board->status = $request->status;
        }
        $board->save();
        return response()->json([
            'message' => 'board updated successfully',
            'board' => $board
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board)
    {
        if (!$board) {
            return response()->json([
                'message' => 'No board with this ID found'
            ], 404);
        }
        $board->delete();
        return response()->json([
            'message' => 'board delete successfully'
        ], 204);
    }
}
