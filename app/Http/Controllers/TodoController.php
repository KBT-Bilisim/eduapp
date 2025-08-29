<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    /**
     * Display a listing of the todos.
     */
    public function index()
    {
        // Sadece istatistik için verileri al
        $todoStats = [
            'total' => Todo::count(),
            'pending' => Todo::where('status', 'pending')->count(),
            'in_progress' => Todo::where('status', 'in_progress')->count(),
            'completed' => Todo::where('status', 'completed')->count()
        ];
        
        return view('todos.index', compact('todoStats'));
    }

    /**
     * Display simple todos list without AJAX (page refresh version)
     */
    public function simpleIndex()
    {
        $todos = Todo::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('todos.simple-index', compact('todos'));
    }

    /**
     * Store a newly created todo in storage.
     */
    public function store(Request $request)
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

        // AJAX isteği kontrolü
        if ($request->ajax()) {
            $todo->load('user');
            return response()->json([
                'success' => true,
                'message' => 'Todo başarıyla eklendi.',
                'data' => $todo
            ]);
        }

        // Simple index'den geliyorsa oraya redirect et
        if ($request->hasHeader('Referer') && str_contains($request->header('Referer'), 'todos-simple')) {
            return redirect()->route('todos.simple-index')->with('success', 'Todo başarıyla eklendi.');
        }

        return redirect()->route('todos.index')->with('success', 'Todo başarıyla eklendi.');
    }

    /**
     * Display the specified todo.
     */
    public function show($id)
    {
        $todo = Todo::with('user')->findOrFail($id);
        return response()->json($todo);
    }

    /**
     * Update the specified todo in storage.
     */
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        
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

        // AJAX isteği kontrolü
        if ($request->ajax()) {
            $todo->load('user');
            return response()->json([
                'success' => true,
                'message' => 'Todo başarıyla güncellendi.',
                'data' => $todo
            ]);
        }

        // Simple index'den geliyorsa oraya redirect et
        if ($request->hasHeader('Referer') && str_contains($request->header('Referer'), 'todos-simple')) {
            return redirect()->route('todos.simple-index')->with('success', 'Todo başarıyla güncellendi.');
        }

        return redirect()->route('todos.index')->with('success', 'Todo başarıyla güncellendi.');
    }

    /**
     * Remove the specified todo from storage.
     */
    public function destroy(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        // AJAX isteği ise JSON döndür
        if ($request->ajax()) {
            return response()->json(['success' => 'Todo başarıyla silindi.']);
        }

        // Simple index'den geliyorsa oraya redirect et
        if ($request->hasHeader('Referer') && str_contains($request->header('Referer'), 'todos-simple')) {
            return redirect()->route('todos.simple-index')->with('success', 'Todo başarıyla silindi.');
        }

        return redirect()->route('todos.index')->with('success', 'Todo başarıyla silindi.');
    }

    /**
     * Update todo status
     */
    public function updateStatus(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $todo->update(['status' => $request->status]);

        // AJAX isteği kontrolü
        if ($request->ajax()) {
            $todo->load('user');
            return response()->json([
                'success' => true,
                'message' => 'Durum başarıyla güncellendi.',
                'data' => $todo
            ]);
        }

        // Simple index'den geliyorsa oraya redirect et
        if ($request->hasHeader('Referer') && str_contains($request->header('Referer'), 'todos-simple')) {
            return redirect()->route('todos.simple-index')->with('success', 'Todo durumu başarıyla güncellendi.');
        }

        return redirect()->route('todos.index')->with('success', 'Todo durumu başarıyla güncellendi.');
    }

    /**
     * Get todo statistics
     */
    public function statistics()
    {
        $todos = Todo::all();
        
        $statistics = [
            'total' => $todos->count(),
            'pending' => $todos->where('status', 'pending')->count(),
            'in_progress' => $todos->where('status', 'in_progress')->count(),
            'completed' => $todos->where('status', 'completed')->count(),
        ];

        return response()->json($statistics);
    }

    /**
     * Get todo data for DataTable with server-side processing
     */
    public function datatable(Request $request)
    {
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumn = $request->get('order')[0]['column'] ?? 1;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';

        // Column mapping for ordering
        $columns = ['id', 'title', 'priority', 'status', 'user_id', 'due_date', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';

        // Base query with relationships
        $query = Todo::with('user');

        // Global search
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                  ->orWhere('description', 'like', "%{$searchValue}%")
                  ->orWhereHas('user', function($userQuery) use ($searchValue) {
                      $userQuery->where('name', 'like', "%{$searchValue}%")
                               ->orWhere('email', 'like', "%{$searchValue}%");
                  });
            });
        }

        // Total records before filtering
        $totalRecords = Todo::count();
        
        // Total records after filtering
        $filteredRecords = $query->count();

        // Get paginated results
        $todos = $query->orderBy($orderBy, $orderDir)
                      ->skip($start)
                      ->take($length)
                      ->get();

        // Format data for DataTable
        $data = $todos->map(function($todo) {
            $userInitials = collect(explode(' ', $todo->user->name))
                ->map(fn($name) => strtoupper(substr($name, 0, 1)))
                ->take(2)
                ->implode('');

            return [
                'id' => $todo->id,
                'empty' => '',
                'title_description' => view('todos.partials.title-description', compact('todo'))->render(),
                'priority' => view('todos.partials.priority-badge', compact('todo'))->render(),
                'status' => view('todos.partials.status-select', compact('todo'))->render(),
                'user' => view('todos.partials.user-info', compact('todo', 'userInitials'))->render(),
                'due_date' => $todo->due_date ? 
                    '<span class="text-' . ($todo->due_date->isPast() ? 'danger' : 'body') . '">' . 
                    $todo->due_date->format('d.m.Y') . '</span>' : 
                    '<span class="text-muted">-</span>',
                'actions' => view('todos.partials.actions', compact('todo'))->render()
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
}
