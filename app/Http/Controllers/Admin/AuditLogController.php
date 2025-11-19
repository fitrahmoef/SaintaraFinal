<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user:id,name,email,user_type')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        // Search by description or IP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate($request->get('per_page', 20));

        return response()->json($logs);
    }

    /**
     * Get audit log statistics.
     */
    public function stats(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $stats = [
            'total_logs' => ActivityLog::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_users' => ActivityLog::whereBetween('created_at', [$startDate, $endDate])
                ->distinct('user_id')
                ->count('user_id'),
            'by_level' => ActivityLog::whereBetween('created_at', [$startDate, $endDate])
                ->select('level', DB::raw('count(*) as count'))
                ->groupBy('level')
                ->get()
                ->pluck('count', 'level'),
            'by_module' => ActivityLog::whereBetween('created_at', [$startDate, $endDate])
                ->select('module', DB::raw('count(*) as count'))
                ->groupBy('module')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->pluck('count', 'module'),
            'by_action' => ActivityLog::whereBetween('created_at', [$startDate, $endDate])
                ->select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->pluck('count', 'action'),
            'daily_activity' => ActivityLog::whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->pluck('count', 'date'),
        ];

        return response()->json($stats);
    }

    /**
     * Get user activity timeline.
     */
    public function userActivity(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $query = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        $activities = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
            'activities' => $activities,
        ]);
    }

    /**
     * Get detailed view of a single audit log.
     */
    public function show($id)
    {
        $log = ActivityLog::with('user:id,name,email,user_type')->findOrFail($id);

        return response()->json($log);
    }

    /**
     * Get list of unique actions for filtering.
     */
    public function actions()
    {
        $actions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return response()->json($actions);
    }

    /**
     * Get list of unique modules for filtering.
     */
    public function modules()
    {
        $modules = ActivityLog::select('module')
            ->distinct()
            ->whereNotNull('module')
            ->orderBy('module')
            ->pluck('module');

        return response()->json($modules);
    }

    /**
     * Export audit logs to CSV.
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user:id,name,email')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $logs = $query->limit(10000)->get(); // Limit to prevent memory issues

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'ID',
                'User',
                'Email',
                'Action',
                'Module',
                'Description',
                'Level',
                'IP Address',
                'User Agent',
                'Created At',
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'N/A',
                    $log->user?->email ?? 'N/A',
                    $log->action,
                    $log->module,
                    $log->description,
                    $log->level,
                    $log->ip_address,
                    $log->user_agent,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
