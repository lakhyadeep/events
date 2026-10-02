<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimelineItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'milestone_date' => $this->milestone_date,
            'description' => $this->description,
            'status' => $this->status?->value ?? $this->status,
            'sort_order' => $this->sort_order,
        ];
    }
}
