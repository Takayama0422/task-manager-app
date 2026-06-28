<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 権限チェックはコントローラーで行うか、誰でも送信自体は可能とするため true
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:0,1,2',
            'is_flagged' => 'required',
            'memo' => 'nullable|string|max:1000',
        ];
    }
}
