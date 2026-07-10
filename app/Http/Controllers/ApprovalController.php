<?php

namespace App\Http\Controllers;

use App\Models\ApprovalInstance;
use App\Models\AtkRequest;
use App\Models\ConsumptionRequest;
use App\Models\MeetingBooking;
use App\Services\ApprovalEngine;
use App\Services\Inventory\AtkRequestService;
use App\Services\Meeting\ConsumptionRequestService;
use App\Services\Meeting\MeetingBookingService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        return view('approvals.index', [
            'pending' => ApprovalInstance::with('approvable')->where('status', 'pending')->latest()->paginate(15),
            'history' => ApprovalInstance::whereIn('status', ['approved', 'rejected', 'revision'])->latest()->limit(25)->get(),
        ]);
    }

    /**
     * Modul ATK, Meeting Booking, dan Consumption Request memiliki efek
     * tambahan saat approve/reject (potong stok, sinkronisasi konsumsi,
     * notifikasi khusus) yang ditangani oleh Service masing-masing, bukan
     * langsung oleh ApprovalEngine. Supaya approval dari halaman generik ini
     * tetap konsisten dengan halaman modul masing-masing, request didelegasikan
     * ke Service yang sesuai berdasarkan tipe approvable-nya. Modul lama
     * (travel, facility, po) yang belum memiliki Service khusus tetap
     * memakai ApprovalEngine langsung seperti sebelumnya.
     */
    public function act(Request $request, ApprovalInstance $approval, ApprovalEngine $engine, AtkRequestService $atkRequests, MeetingBookingService $meetingBookings, ConsumptionRequestService $consumptionRequests)
    {
        $data = $request->validate(['action' => 'required|in:approve,reject,revision', 'notes' => 'nullable|string']);

        $module = $approval->approvalWorkflow->module ?? null;
        abort_unless($module && $request->user()->can("{$module}.approve"), 403, 'Anda tidak memiliki izin approve untuk modul ini.');

        $record = $approval->approvable;

        match (true) {
            $record instanceof AtkRequest => $atkRequests->act($record, $data['action'], $data['notes'] ?? null),
            $record instanceof MeetingBooking => $meetingBookings->act($record, $data['action'], $data['notes'] ?? null),
            $record instanceof ConsumptionRequest => $consumptionRequests->act($record, $data['action'], $data['notes'] ?? null),
            default => $engine->act($approval, $data['action'], $data['notes'] ?? null),
        };

        return back()->with('status', 'Approval diproses.');
    }
}
