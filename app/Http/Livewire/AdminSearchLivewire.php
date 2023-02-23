<?php

namespace App\Http\Livewire;

use App\Models\Admin;
use Livewire\Component;

class AdminSearchLivewire extends Component
{
    protected $admins;
    public $searchTerm;

    public function mount($admins)
    {
        $this->admins = $admins;
    }

    public function render()
    {
        $this->admins = Admin::where('fname', 'LIKE', '%' . $this->searchTerm . '%')
            ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
            ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
            ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%')
            ->paginate();

        return view('livewire.admin-search-livewire', [
            'admins' => $this->admins,
        ]);
    }
}
