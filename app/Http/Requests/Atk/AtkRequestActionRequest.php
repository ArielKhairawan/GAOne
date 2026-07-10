<?php

namespace App\Http\Requests\Atk;

use Illuminate\Foundation\Http\FormRequest;

class AtkRequestActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('atk.approve');
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject,revision',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
