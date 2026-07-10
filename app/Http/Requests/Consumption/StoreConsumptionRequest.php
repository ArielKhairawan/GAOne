<?php

namespace App\Http\Requests\Consumption;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsumptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('consumption.create');
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'departemen' => 'nullable|string|max:255',
            'nama_acara' => 'required|string|max:255',
            'jumlah_peserta' => 'required|integer|min:1',
            'jenis_konsumsi' => 'required|array|min:1',
            'jenis_konsumsi.*' => 'string',
            'detail_konsumsi' => 'nullable|string',
        ];
    }
}
