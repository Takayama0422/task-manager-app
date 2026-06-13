<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlaggedTaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'major_id'   => $this->major_id,
            'mid_sort'   => $this->mid_sort,
            'chapter_no' => $this->chapter_no,
            'status'     => $this->progressLog->status,
            'memo'       => $this->progressLog->memo,
            'flagged_at' => $this->progressLog->updated_at->toIso8601String(),
        ];
    }
}