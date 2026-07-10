<?php

namespace App\Http\Requests\Atk;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAtkItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('atk.edit');
    }

    public function rules(): array
    {
        return [
            'atk_category_id' => 'required|exists:atk_categories,id',
            'code' => ['required', 'string', 'max:50', Rule::unique('atk_items', 'code')->ignore($this->route('atk_item'))],
            'name' => 'required|string|max:255',
            'satuan' => 'required|string|max:20',
            'minimum_stock' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'nullable|string|max:255',
        ];
    }
}
