<?php

namespace App\Http\Livewire;

use App\Models\Supervisor;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class SupervisorSearchLivewire extends Component
{
    use WithPagination;

    public function __construct()
    {
        $this->supervisors = new Collection();
    }

    protected $supervisors;
    public $searchTerm;
    public $type = 'all';

    // public function filterAdmins($type)
    // {
    //     // dd($type);
    //     if ($type === 'all') {
    //         $this->supervisors = Supervisor::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })->paginate(10);
    //     } else if ($type === 'only_trashed') {
    //         $this->supervisors = Supervisor::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->trashed()
    //             ->paginate(10);
    //     } else if ($type === 'not_trashed') {
    //         $this->supervisors = Supervisor::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->where('deleted_at', '=', null)
    //             ->paginate(10);
    //     }

    //     return $this->supervisors;
    // }

    public function mount($supervisors)
    {
        $this->supervisors = $supervisors;
    }

    public function render()
    {

        if ($this->type === 'all') {
            $this->supervisors = Supervisor::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })->paginate(10);
        } else if ($this->type === 'only_trashed') {
            $this->supervisors = Supervisor::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })
                ->where('deleted_at', '!=', null)
                ->paginate(10);
        } else {
            $this->supervisors = Supervisor::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })
                ->where('deleted_at', '=', null)
                ->paginate(10);
        }



        return view('livewire.supervisor-search-livewire', [
            'supervisors' => $this->supervisors,
        ]);
    }
}
