<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vehicle.edit');
    }

    public function rules(): array
    {
        return [
            'plat_nomor' => [
                'required', 'string', 'max:20',
                Rule::unique('vehicles', 'plat_nomor')->ignore($this->route('vehicle')),
            ],
            'jenis_kendaraan' => 'required|string|max:100',
            'merk' => 'nullable|string|max:100',
            'tahun' => 'nullable|integer|min:1980|max:'.(now()->year + 1),
            'driver' => 'nullable|string|max:150',
            'driver_id' => 'nullable|exists:users,id',
            'status' => 'required|in:aktif,servis,tidak_aktif',
            'keterangan' => 'nullable|string',
        ];
    }
}
