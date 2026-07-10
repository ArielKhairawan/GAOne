<?php

namespace App\Http\Requests\Meeting;

use Illuminate\Foundation\Http\FormRequest;

class MeetingActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('meeting.approve');
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject,revision',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
