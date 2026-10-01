<?php

namespace App\Livewire\Frontend;

use App\Models\Participant;
use Livewire\Attributes\On;
use Livewire\Component;

class CandidateDetailModal extends Component
{
    public bool $isOpen = false;
    public ?Participant $candidate = null;
    public string $activeImage = '';

    #[On('open-candidate-modal')]
    public function openModal(int $candidateId): void
    {
        $this->candidate = Participant::find($candidateId);
        if ($this->candidate) {
            $this->activeImage = $this->candidate->primary_display_image;
            $this->isOpen = true;
        }
    }

    public function setActiveImage(string $url): void
    {
        $this->activeImage = $url;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->candidate = null;
    }

    public function render()
    {
        return view('livewire.frontend.candidate-detail-modal');
    }
}
