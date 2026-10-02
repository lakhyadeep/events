<?php

namespace App\Livewire\Frontend;

use App\Models\Participant;
use App\Models\Zone;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ParticipantsShowcase extends Component
{
    use WithPagination;

    public int $eventId;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'zone')]
    public string $selectedZone = '';

    #[Url(as: 'shortlisted')]
    public bool $shortlistedOnly = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedZone(): void
    {
        $this->resetPage();
    }

    public function updatedShortlistedOnly(): void
    {
        $this->resetPage();
    }

    public function setZone(string $zone): void
    {
        $this->selectedZone = $this->selectedZone === $zone ? '' : $zone;
        $this->resetPage();
    }

    public function toggleShortlisted(): void
    {
        $this->shortlistedOnly = ! $this->shortlistedOnly;
        $this->resetPage();
    }

    public function selectCandidate(int $id): void
    {
        $this->dispatch('open-candidate-modal', candidateId: $id);
    }

    public function render()
    {
        $zones = ['All Zones' => ''] + Zone::active()->orderBy('sort_order')->pluck('name', 'name')->toArray();

        $participants = Participant::query()
            ->where('event_id', $this->eventId)
            ->approved()
            ->when($this->selectedZone, fn ($q) => $q->zone($this->selectedZone))
            ->when($this->shortlistedOnly, fn ($q) => $q->shortlisted())
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
            ->paginate(12);

        return view('livewire.frontend.participants-showcase', [
            'participants' => $participants,
            'zones' => $zones,
        ]);
    }
}
