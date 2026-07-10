<?php

namespace App\Http\Requests\Atk;

use Illuminate\Foundation\Http\FormRequest;

class StockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('atk.edit');
    }

    public function rules(): array
    {
        return [
            'atk_item_id' => 'required|exists:atk_items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
