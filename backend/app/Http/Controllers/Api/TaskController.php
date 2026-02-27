<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['user', 'assignee'])
            ->where(function ($q) use ($request) {
                $q->where('user_id', $request->user()->id)
                  ->orWhere('assigned_to', $request->user()->id);
            });

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed',
            'priority' => 'in:low,medium,high',
            'deadline' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending',
            'priority' => $request->priority ?? 'medium',
            'deadline' => $request->deadline,
            'assigned_to' => $request->assigned_to,
        ]);

        $task->load(['user', 'assignee']);

        return response()->json($task, 201);
    }

    public function show(Task $task, Request $request)
    {
        if ($task->user_id !== $request->user()->id && $task->assigned_to !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->load(['user', 'assignee']);

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id && $task->assigned_to !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed',
            'priority' => 'in:low,medium,high',
            'deadline' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($request->only([
            'title', 'description', 'status', 'priority', 'deadline', 'assigned_to',
        ]));

        $task->load(['user', 'assignee']);

        return response()->json($task);
    }

    public function destroy(Task $task, Request $request)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function dashboard(Request $request)
    {
        $userId = $request->user()->id;

        $stats = [
            'total' => Task::where('user_id', $userId)->orWhere('assigned_to', $userId)->count(),
            'pending' => Task::where(fn($q) => $q->where('user_id', $userId)->orWhere('assigned_to', $userId))->where('status', 'pending')->count(),
            'in_progress' => Task::where(fn($q) => $q->where('user_id', $userId)->orWhere('assigned_to', $userId))->where('status', 'in_progress')->count(),
            'completed' => Task::where(fn($q) => $q->where('user_id', $userId)->orWhere('assigned_to', $userId))->where('status', 'completed')->count(),
        ];

        $recentTasks = Task::with(['user', 'assignee'])
            ->where(fn($q) => $q->where('user_id', $userId)->orWhere('assigned_to', $userId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_tasks' => $recentTasks,
        ]);
    }

    public function users(Request $request)
    {
        $users = \App\Models\User::where('id', '!=', $request->user()->id)
            ->select('id', 'name', 'email')
            ->get();

        return response()->json($users);
    }
}
