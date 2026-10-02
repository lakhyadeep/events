<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WinnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'rank_order' => $this->rank_order?->value ?? $this->rank_order,
            'status' => $this->status,
            'award' => [
                'id' => $this->award?->id,
                'name' => $this->award?->name,
                'display_name' => $this->award?->display_name,
                'category' => $this->award?->category,
                'prize_money_or_award' => $this->award?->prize_money_or_award,
            ],
            'participant' => [
                'id' => $this->participant?->id,
                'name' => $this->participant?->name,
                'display_name' => $this->participant?->display_name,
                'zone' => $this->participant?->zone,
                'locality' => $this->participant?->locality,
                'primary_image_url' => $this->participant?->primary_image_url,
                'puja_theme' => $this->participant?->puja_theme,
            ],
        ];
    }
}
