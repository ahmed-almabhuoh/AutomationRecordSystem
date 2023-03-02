<?php

namespace App\Http\Livewire;

use App\Models\Supervisor;
use Livewire\Component;

class AddSupervisorToSupervisionCommitteeLivewire extends Component
{
    protected $supervisors;
    public $sc;
    public $searchTerm;

    public function mount($sc)
    {
        $this->sc = $sc;
        // $this->supervisors = $supervisors;
        $this->supervisors = Supervisor::whereDoesntHave('sc', function ($query) use ($sc) {
            $query->where('id', '!=', $sc->id);
        })->paginate();
    }

    public function render()
    {
        $sc = $this->sc;
        $this->supervisors = Supervisor::where(function ($query) {
            $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
        })->whereDoesntHave('sc', function ($query) use ($sc) {
            $query->where('id', '!=', $sc->id);
        })->paginate();

        return view('livewire.add-supervisor-to-supervision-committee-livewire', [
            'supervisors' => $this->supervisors,
        ]);
    }
}
