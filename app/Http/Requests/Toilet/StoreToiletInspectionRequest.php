<?php

namespace App\Http\Requests\Toilet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreToiletInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('toilet.create');
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'lokasi' => ['required', Rule::in(config('monitoring.toilet_locations'))],
            'lokasi_detail' => 'required_if:lokasi,Lokasi Lainnya|nullable|string|max:150',
            'petugas_id' => 'required|exists:users,id',
            'status' => ['required', Rule::in(array_keys(config('monitoring.toilet_statuses')))],
            'catatan' => 'nullable|string',
            // Foto wajib (tidak boleh opsional) sesuai instruksi terbaru.
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'tanda_tangan' => 'nullable|string',
            'items' => 'required|array',
            'items.*' => ['required', Rule::in(array_keys(config('monitoring.toilet_checklist_item_statuses')))],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.required' => 'Foto wajib diunggah untuk setiap inspeksi.',
        ];
    }
}
