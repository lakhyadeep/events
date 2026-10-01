<?php

namespace App\Livewire\Frontend;

use App\Models\Participant;
use Livewire\Attributes\Url;
use Livewire\Component;

class ParticipantsShowcase extends Component
{
    public int $eventId;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'zone')]
    public string $selectedZone = '';

    #[Url(as: 'shortlisted')]
    public bool $shortlistedOnly = false;

    public function setZone(string $zone): void
    {
        $this->selectedZone = $this->selectedZone === $zone ? '' : $zone;
    }

    public function toggleShortlisted(): void
    {
        $this->shortlistedOnly = ! $this->shortlistedOnly;
    }

    public function selectCandidate(int $id): void
    {
        $this->dispatch('open-candidate-modal', candidateId: $id);
    }

    public function render()
    {
        $zones = [
            'All Zones' => '',
            'North Kolkata' => 'North Kolkata',
            'South Kolkata' => 'South Kolkata',
            'Central Kolkata' => 'Central Kolkata',
            'East Kolkata' => 'East Kolkata',
            'Howrah' => 'Howrah',
        ];

        $participants = Participant::query()
            ->where('event_id', $this->eventId)
            ->where('registration_status', 'approved')
            ->when($this->selectedZone, fn ($q) => $q->where('zone', $this->selectedZone))
            ->when($this->shortlistedOnly, fn ($q) => $q->where('is_shortlisted', true))
            ->when($this->search, function ($q) {
                $term = "%{$this->search}%";
                $q->where(function ($sub) use ($term) {
                    $sub->where('display_name', 'like', $term)
                        ->orWhere('locality', 'like', $term)
                        ->orWhere('puja_theme', 'like', $term)
                        ->orWhere('idol_artist', 'like', $term);
                });
            })
            ->latest('is_shortlisted')
            ->get();

        return view('livewire.frontend.participants-showcase', [
            'participants' => $participants,
            'zones' => $zones,
        ]);
    }
}
