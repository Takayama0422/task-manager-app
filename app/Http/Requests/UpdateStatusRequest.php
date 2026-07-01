<?php

namespace App\Http\Requests;

use App\Models\ProgressLog;
use App\Models\Textbook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $textbookId = (int) $this->route('id');

        return Textbook::where('user_id', auth()->id())
            ->where('id', $textbookId)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'integer',
                Rule::in([
                    ProgressLog::STATUS_NOT_STARTED,
                    ProgressLog::STATUS_IN_PROGRESS,
                    ProgressLog::STATUS_COMPLETED,
                ]),
            ],
            'is_flagged' => 'required|boolean',
            'memo' => 'nullable|string|max:1000',
        ];
    }
}
