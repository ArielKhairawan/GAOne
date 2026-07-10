<?php

namespace App\Http\Controllers\Meeting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meeting\MeetingActionRequest;
use App\Http\Requests\Meeting\StoreMeetingBookingRequest;
use App\Models\MeetingBooking;
use App\Models\MeetingRoom;
use App\Services\Meeting\MeetingBookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingBookingController extends Controller
{
    public function __construct(private MeetingBookingService $bookings)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'date_from', 'date_to', 'meeting_room_id']);

        if ($request->user()->hasRole('Karyawan') && ! $request->user()->hasAnyRole(['Admin', 'Manager', 'GA Staff'])) {
            $filters['user_id'] = $request->user()->id;
        }

        return view('meeting.bookings-index', [
            'bookings' => $this->bookings->list($filters, (int) config('monitoring.per_page')),
            'filters' => $filters,
            'rooms' => MeetingRoom::orderBy('nama_ruangan')->get(),
            'statusLabels' => config('monitoring.workflow_status_labels'),
        ]);
    }

    public function create(): View
    {
        return view('meeting.bookings-form', [
            'rooms' => MeetingRoom::where('status', 'tersedia')->orderBy('nama_ruangan')->get(),
        ]);
    }

    public function store(StoreMeetingBookingRequest $request)
    {
        $data = $request->safe()->except(['jenis_konsumsi', 'detail_konsumsi']);
        $consumption = null;

        if ($request->boolean('butuh_konsumsi')) {
            $consumption = [
                'departemen' => $data['departemen'] ?? null,
                'nama_acara' => $data['nama_kegiatan'],
                'jumlah_peserta' => $data['jumlah_peserta'],
                'jenis_konsumsi' => $request->validated('jenis_konsumsi', []),
                'detail_konsumsi' => $request->validated('detail_konsumsi'),
            ];
        }

        $this->bookings->submit($data, $consumption, $request->user()->id);

        return redirect()->route('meeting.bookings.index')->with('status', 'Booking ruang meeting berhasil diajukan.');
    }

    public function show(MeetingBooking $meeting_booking): View
    {
        $this->authorize('view', $meeting_booking);

        return view('meeting.bookings-show', [
            'booking' => $this->bookings->find($meeting_booking->id),
            'statusLabels' => config('monitoring.workflow_status_labels'),
        ]);
    }

    public function act(MeetingActionRequest $request, MeetingBooking $meeting_booking)
    {
        $this->bookings->act($meeting_booking, $request->validated('action'), $request->validated('notes'));

        return back()->with('status', 'Booking berhasil diproses.');
    }

    public function complete(MeetingBooking $meeting_booking)
    {
        $this->bookings->markCompleted($meeting_booking);

        return back()->with('status', 'Booking ditandai selesai.');
    }

    /**
     * Endpoint AJAX ringan untuk cek bentrok jadwal sebelum submit.
     */
    public function checkAvailability(Request $request)
    {
        $data = $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $available = $this->bookings->checkAvailability($data['meeting_room_id'], $data['tanggal'], $data['jam_mulai'], $data['jam_selesai']);

        return response()->json(['available' => $available]);
    }
}
