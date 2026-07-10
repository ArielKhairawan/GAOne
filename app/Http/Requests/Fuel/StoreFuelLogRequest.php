<?php

namespace App\Http\Requests\Fuel;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuelLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('fuel.create');
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'tanggal_pengisian' => 'required|date',
            'driver' => 'nullable|string|max:150',
            'driver_id' => 'nullable|exists:users,id',
            'jenis_bahan_bakar' => 'required|string|max:50',
            'harga_per_liter' => 'required|numeric|min:0',
            'jumlah_liter' => 'required|numeric|min:0.01',
            'kilometer_awal' => 'required|integer|min:0',
            'kilometer_akhir' => 'required|integer|gte:kilometer_awal',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'kilometer_akhir.gte' => 'Kilometer akhir harus lebih besar atau sama dengan kilometer awal.',
        ];
    }
}
