<?php

namespace App\Http\Requests\Meeting;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('meeting.create');
    }

    public function rules(): array
    {
        return [
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'departemen' => 'nullable|string|max:255',
            'nama_kegiatan' => 'required|string|max:255',
            'jumlah_peserta' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
            'butuh_konsumsi' => 'nullable|boolean',
            'jenis_konsumsi' => 'required_if:butuh_konsumsi,1|array',
            'jenis_konsumsi.*' => 'string',
            'detail_konsumsi' => 'nullable|string',
        ];
    }
}
