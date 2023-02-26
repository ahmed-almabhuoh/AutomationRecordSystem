<?php

namespace App\Http\Livewire;

use App\Models\Keeper;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class KeeperSearchLivewire extends Component
{
    // use WithPagination;

    public function __construct()
    {
        $this->keepers = new Collection();
    }

    protected $keepers;
    public $searchTerm;
    public $type = 'all';

    // public function filterAdmins($type)
    // {
    //     // dd($type);
    //     if ($type === 'all') {
    //         $this->keepers = Keeper::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })->paginate(10);
    //     } else if ($type === 'only_trashed') {
    //         $this->keepers = Keeper::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->trashed()
    //             ->paginate(10);
    //     } else if ($type === 'not_trashed') {
    //         $this->keepers = Keeper::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->where('deleted_at', '=', null)
    //             ->paginate(10);
    //     }

    //     return $this->keepers;
    // }

    public function mount($keepers)
    {
        $this->keepers = $keepers;
    }

    public function render()
    {

        if ($this->type === 'all') {
            $this->keepers = Keeper::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })->paginate(10);
        } else if ($this->type === 'only_trashed') {
            $this->keepers = Keeper::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })
                ->where('deleted_at', '!=', null)
                ->paginate(10);
        } else {
            $this->keepers = Keeper::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })
                ->where('deleted_at', '=', null)
                ->paginate(10);
        }



        return view('livewire.keeper-search-livewire', [
            'keepers' => $this->keepers,
        ]);
    }
}
