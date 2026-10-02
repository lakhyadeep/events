<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'short_introduction' => $this->short_introduction,
            'zone' => $this->zone,
            'locality' => $this->locality,
            'landmark' => $this->landmark,
            'address' => $this->address,
            'year' => $this->year,
            'registration_status' => $this->registration_status?->value ?? $this->registration_status,
            'is_shortlisted' => (bool) $this->is_shortlisted,
            'primary_image_url' => $this->primary_image_url,
            'gallery_images' => $this->gallery_image_urls,
            'cultural' => [
                'is_puja_contest' => (bool) $this->is_puja_contest,
                'first_year_of_puja' => $this->first_year_of_puja,
                'puja_theme' => $this->puja_theme,
                'idol_artist' => $this->idol_artist,
                'theme_artist' => $this->theme_artist,
                'light_designer' => $this->light_designer,
                'sound_designer' => $this->sound_designer,
                'concept_note_image_url' => $this->concept_note_image_url,
            ],
        ];
    }
}
