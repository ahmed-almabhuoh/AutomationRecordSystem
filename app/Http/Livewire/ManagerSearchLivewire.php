<?php

namespace App\Http\Livewire;

use App\Models\Manager;
use Livewire\Component;

class ManagerSearchLivewire extends Component
{
    protected $managers;
    public $searchTerm;

    public function mount($managers)
    {
        $this->managers = $managers;
    }

    public function render()
    {
        $this->managers = Manager::where('fname', 'LIKE', '%' . $this->searchTerm . '%')
            ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
            ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
            ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%')
            ->paginate();
            
        return view('livewire.manager-search-livewire', [
            'managers' => $this->managers,
        ]);
    }
}
