<?php

namespace App\Http\Requests\Consumption;

use Illuminate\Foundation\Http\FormRequest;

class ConsumptionActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('consumption.approve');
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject,revision',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
