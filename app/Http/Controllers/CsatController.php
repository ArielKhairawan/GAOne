<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Services\CsatService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CsatController extends Controller
{
    public function __construct(private CsatService $csat)
    {
    }

    public function index(Request $request): View
    {
        return view('csat.index', [
            'pending' => $this->csat->pendingFor($request->user()->id),
            'history' => Survey::query()->where('user_id', $request->user()->id)->where('status', 'completed')
                ->with('response')->latest()->limit(20)->get(),
            'moduleLabels' => config('monitoring.csat_modules'),
            'ratingLabels' => config('monitoring.csat_rating_labels'),
        ]);
    }

    public function show(Survey $survey): View
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        return view('csat.form', [
            'survey' => $survey,
            'moduleLabels' => config('monitoring.csat_modules'),
            'ratingLabels' => config('monitoring.csat_rating_labels'),
        ]);
    }

    public function store(Request $request, Survey $survey)
    {
        abort_unless($survey->user_id === auth()->id(), 403);

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
        ]);

        $this->csat->submit($survey, (int) $data['rating'], $data['comments'] ?? null);

        return redirect()->route('csat.index')->with('status', 'Terima kasih atas feedback Anda.');
    }
}
