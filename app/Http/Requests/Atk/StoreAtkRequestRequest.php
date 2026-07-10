<?php

namespace App\Http\Requests\Atk;

use Illuminate\Foundation\Http\FormRequest;

class StoreAtkRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('atk.create');
    }

    public function rules(): array
    {
        return [
            'department' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.atk_item_id' => 'required|exists:atk_items,id|distinct',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
