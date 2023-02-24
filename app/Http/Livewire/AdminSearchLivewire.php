<?php

namespace App\Http\Livewire;

use App\Models\Admin;
use Livewire\Component;
use Livewire\WithPagination;

class AdminSearchLivewire extends Component
{
    use WithPagination;

    protected $admins;
    public $searchTerm;

    public function mount($admins)
    {
        $this->admins = $admins;
    }

    public function render()
    {
        // $this->admins = Admin::where('fname', 'LIKE', '%' . $this->searchTerm . '%')
        //     ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
        //     ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
        //     ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%')
        //     ->where('deleted_at', '=', null)
        //     ->paginate(10);

        $this->admins = Admin::where(function ($query) {
            $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
        })->paginate(10);
        // $searchTerm = $this->searchTerm;

        // $this->admins = $this->admins->filter(function ($admin) use ($searchTerm) {
        //     return strpos($admin->fname, $searchTerm) !== false;
        // });

        return view('livewire.admin-search-livewire', [
            'admins' => $this->admins,
        ]);
    }
}
