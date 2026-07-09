<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('user')
            ->orderByRaw("
            FIELD(status,'open','resolved'),
            FIELD(priority,'urgent','high','medium','low')
        ")
            ->latest()
            ->get();

        return view(
            'report.index',
            compact('reports')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'title' => 'required|max:255',

            'message' => 'required',

            'priority' => 'required|in:low,medium,high,urgent',

        ]);

        Report::create([

            'user_id' => Auth::id(),

            'title' => $validated['title'],

            'message' => $validated['message'],

            'priority' => $validated['priority'],

        ]);

        return back()->with(
            'success',
            'Report submitted successfully.'
        );
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return back()->with(
            'success',
            'Report resolved and removed successfully.'
        );
    }
}
