<?php

namespace App\Http\Requests\Meeting;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('meeting.create');
    }

    public function rules(): array
    {
        return [
            'kode_ruangan' => 'required|string|max:50|unique:meeting_rooms,kode_ruangan',
            'nama_ruangan' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'string',
            'status' => ['required', 'in:tersedia,digunakan,maintenance,tidak_aktif'],
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }
}
