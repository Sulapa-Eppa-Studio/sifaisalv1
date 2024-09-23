<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TermintSppPpk;
use App\Enums\FileType;

class ViewFilesModal extends Component
{
    public $record;
    public $files = [];

    protected $listeners = ['openFilesModal'];

    public function openFilesModal($recordId)
    {
        $this->record = TermintSppPpk::with('files')->findOrFail($recordId);
        $this->files = $this->record->files;
        $this->dispatchBrowserEvent('openModal', ['id' => 'view-files-modal']);
    }

    public function render()
    {
        return view('livewire.view-files-modal');
    }
}
