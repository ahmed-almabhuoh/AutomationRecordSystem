<?php

namespace App\Http\Livewire;

use App\Models\Branch;
use Livewire\Component;

class BranchSearchLivewire extends Component
{
    protected $branches;
    public $searchTerm;

    public function mount($branches)
    {
        $this->branches = $branches;
    }

    public function render()
    {
        $this->branches = Branch::where('name', 'LIKE', '%' . $this->searchTerm . '%')
            ->orWhere('region', 'LIKE', '%' . $this->searchTerm . '%')
            ->paginate();
            
        return view('livewire.branch-search-livewire', [
            'branches' => $this->branches,
        ]);
    }
}
