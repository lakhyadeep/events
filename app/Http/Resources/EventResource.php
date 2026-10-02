<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'year' => $this->year,
            'tagline' => $this->tagline,
            'status' => $this->status?->value ?? $this->status,
            'countdown_datetime' => $this->countdown_datetime?->toIso8601String(),
            'closure_message' => $this->closure_message,
            'about_text' => $this->about_text,
            'criteria_text' => $this->criteria_text,
            'terms_and_conditions' => $this->terms_and_conditions,
            'og_image_url' => $this->og_image_url,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'external_link' => $this->external_link,
            'toggles' => [
                'registration_active' => (bool) $this->is_registration_active,
                'voting_active' => (bool) $this->is_voting_active,
                'awards_active' => (bool) $this->is_awards_active,
                'timeline_active' => (bool) $this->is_timeline_active,
            ],
            'banners' => BannerResource::collection($this->whenLoaded('banners')),
            'sponsors' => SponsorResource::collection($this->whenLoaded('sponsors')),
            'awards' => $this->whenLoaded('awards'),
            'timeline_items' => TimelineItemResource::collection($this->whenLoaded('timelineItems')),
        ];
    }
}
