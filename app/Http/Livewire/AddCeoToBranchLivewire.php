<?php

namespace App\Http\Livewire;

use App\Models\Supervisor;
use Livewire\Component;

class AddCeoToBranchLivewire extends Component
{
    protected $supervisors;
    public $branch;
    public $searchTerm;

    public function mount($branch)
    {
        $this->branch = $branch;
        // $this->supervisors = $supervisors;
        $this->supervisors = Supervisor::whereDoesntHave('sc')
            ->whereDoesntHave('branch', function ($query) use ($branch) {
                $query->where('id', '!=', $branch->id);
            })
            ->paginate();
    }
    public function render()
    {
        $branch = $this->branch;
        $this->supervisors = Supervisor::where(function ($query) {
            $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
        })->whereDoesntHave('branch', function ($query) use ($branch) {
            $query->where('id', '!=', $branch->id);
        })->whereDoesntHave('sc')
            ->paginate();

        return view('livewire.add-ceo-to-branch-livewire', [
            'supervisors' => $this->supervisors,
        ]);
    }
}
