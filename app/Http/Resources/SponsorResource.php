<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'display_name' => $this->display_name,
            'sponsor_type' => $this->sponsor_type?->value ?? $this->sponsor_type,
            'sponsor_tag' => $this->sponsor_tag,
            'logo_url' => $this->logo_url,
            'landing_url' => $this->landing_url,
            'slot_order' => $this->slot_order,
            'description' => $this->description,
        ];
    }
}
