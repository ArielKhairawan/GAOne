<?php

namespace App\Http\Controllers;

use App\Models\AtkItem;
use App\Models\FacilityBooking;
use App\Models\PurchaseOrder;
use App\Models\ReportExport;
use App\Models\SurveyResponse;
use App\Models\TravelRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index', [
            'travel' => TravelRequest::selectRaw('department, count(*) as total, sum(estimated_cost) as cost')
                ->join('users', 'users.id', '=', 'travel_requests.user_id')->groupBy('department')->get(),
            'booking' => FacilityBooking::selectRaw('status, count(*) as total')->groupBy('status')->get(),
            'atk' => AtkItem::orderBy('stock')->limit(10)->get(),
            'po' => PurchaseOrder::selectRaw('status, count(*) as total, sum(total_amount) as amount')->groupBy('status')->get(),
            'csat' => round(SurveyResponse::avg('satisfaction_score') ?: 0, 2),
        ]);
    }

    public function export(Request $request, string $type)
    {
        $format = $request->validate(['format' => 'required|in:pdf,csv'])['format'];
        ReportExport::create(['user_id' => $request->user()->id, 'report_type' => $type, 'format' => $format, 'filters' => $request->query()]);

        if ($format === 'pdf') {
            return Pdf::loadView('reports.pdf', ['type' => $type, 'generatedAt' => now()])->download("ga1-$type-report.pdf");
        }

        return response("Report,Generated At\n$type,".now()->toDateTimeString()."\n", 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=ga1-$type-report.csv",
        ]);
    }
}
