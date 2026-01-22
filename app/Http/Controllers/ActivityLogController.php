<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['subject', 'causer'])->latest();

        // Filter by log name
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(15);

        // Get statistics
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => ActivityLog::whereMonth('created_at', now()->month)->count(),
        ];

        // Get distinct log names for filter
        $logNames = ActivityLog::select('log_name')->distinct()->pluck('log_name');
        $events = ActivityLog::select('event')->distinct()->whereNotNull('event')->pluck('event');

        return view('admin.activity-log.index', compact('logs', 'stats', 'logNames', 'events'));
    }
}
