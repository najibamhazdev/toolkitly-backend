<?php

namespace App\Http\Controllers;

use App\Models\ToolEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToolAnalyticsController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = config('toolkitly.analytics.dashboard_token');

        abort_if(! $token || ! hash_equals($token, (string) $request->query('token')), 404);

        $totals = ToolEvent::query()
            ->select('tool', DB::raw('count(*) as total'), DB::raw('count(distinct visitor_hash) as visitors'))
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tool')
            ->orderByDesc('total')
            ->get();

        $actions = ToolEvent::query()
            ->select('tool', 'action', DB::raw('count(*) as total'))
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tool', 'action')
            ->orderBy('tool')
            ->orderByDesc('total')
            ->get();

        $daily = ToolEvent::query()
            ->select(DB::raw('date(created_at) as day'), DB::raw('count(*) as total'))
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('analytics.tool-events', [
            'totals' => $totals,
            'actions' => $actions,
            'daily' => $daily,
        ]);
    }
}
