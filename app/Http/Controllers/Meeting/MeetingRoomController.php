<?php

namespace App\Http\Controllers\Meeting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meeting\StoreMeetingRoomRequest;
use App\Http\Requests\Meeting\UpdateMeetingRoomRequest;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MeetingRoomController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'search']);

        return view('meeting.rooms-index', [
            'rooms' => MeetingRoom::query()->status($filters['status'] ?? null)->search($filters['search'] ?? null)
                ->latest()->paginate((int) config('monitoring.per_page'))->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('meeting.rooms-form', ['room' => new MeetingRoom]);
    }

    public function store(StoreMeetingRoomRequest $request)
    {
        $data = $request->safe()->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('meeting-rooms', 'public');
        }

        MeetingRoom::create($data);

        return redirect()->route('meeting.rooms.index')->with('status', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(MeetingRoom $meeting_room): View
    {
        return view('meeting.rooms-form', ['room' => $meeting_room]);
    }

    public function update(UpdateMeetingRoomRequest $request, MeetingRoom $meeting_room)
    {
        $data = $request->safe()->except('foto');

        if ($request->hasFile('foto')) {
            if ($meeting_room->foto) {
                Storage::disk('public')->delete($meeting_room->foto);
            }
            $data['foto'] = $request->file('foto')->store('meeting-rooms', 'public');
        }

        $meeting_room->update($data);

        return redirect()->route('meeting.rooms.index')->with('status', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(MeetingRoom $meeting_room)
    {
        if ($meeting_room->foto) {
            Storage::disk('public')->delete($meeting_room->foto);
        }
        $meeting_room->delete();

        return back()->with('status', 'Ruangan berhasil dihapus.');
    }
}
