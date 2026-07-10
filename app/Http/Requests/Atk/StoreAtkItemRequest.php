<?php

namespace App\Http\Requests\Atk;

use Illuminate\Foundation\Http\FormRequest;

class StoreAtkItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('atk.create');
    }

    public function rules(): array
    {
        return [
            'atk_category_id' => 'required|exists:atk_categories,id',
            'code' => 'required|string|max:50|unique:atk_items,code',
            'name' => 'required|string|max:255',
            'satuan' => 'required|string|max:20',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
        ];
    }
}
