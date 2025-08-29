<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class TodoApiController extends Controller
{
    /**
     * Display a listing of the todos.
     */
    public function index(): JsonResponse
    {
        $todos = Todo::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $todos,
            'message' => 'Todos retrieved successfully'
        ]);
    }

    /**
     * Store a newly created todo in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $todo = Todo::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'user_id' => Auth::id(),
        ]);

        $todo->load('user');

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo created successfully'
        ], 201);
    }

    /**
     * Display the specified todo.
     */
    public function show($id): JsonResponse
    {
        $todo = Todo::with('user')->find($id);
        
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Todo not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo retrieved successfully'
        ]);
    }

    /**
     * Update the specified todo in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $todo = Todo::find($id);
        
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Todo not found'
            ], 404);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
        ]);

        $todo->load('user');

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo updated successfully'
        ]);
    }

    /**
     * Remove the specified todo from storage.
     */
    public function destroy($id): JsonResponse
    {
        $todo = Todo::find($id);
        
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Todo not found'
            ], 404);
        }

        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully'
        ]);
    }

    /**
     * Update todo status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $todo = Todo::find($id);
        
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Todo not found'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $todo->update(['status' => $request->status]);
        $todo->load('user');

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo status updated successfully'
        ]);
    }
}
