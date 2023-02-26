<?php

namespace App\Http\Livewire;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class AdminSearchLivewire extends Component
{
    // use WithPagination;

    public function __construct()
    {
        $this->admins = new Collection();
    }

    protected $admins;
    public $searchTerm;
    public $type = 'all';

    // public function filterAdmins($type)
    // {
    //     // dd($type);
    //     if ($type === 'all') {
    //         $this->admins = Admin::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })->paginate(10);
    //     } else if ($type === 'only_trashed') {
    //         $this->admins = Admin::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->trashed()
    //             ->paginate(10);
    //     } else if ($type === 'not_trashed') {
    //         $this->admins = Admin::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->where('deleted_at', '=', null)
    //             ->paginate(10);
    //     }

    //     return $this->admins;
    // }

    public function mount($admins)
    {
        $this->admins = $admins;
    }

    public function render()
    {

        if ($this->type === 'all') {
            $this->admins = Admin::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })->paginate(10);
        } else if ($this->type === 'only_trashed') {
            $this->admins = Admin::where(function ($query) {
                    $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
                })
                ->where('deleted_at', '!=', null)
                ->paginate(10);
        }else {
            $this->admins = Admin::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })
            ->where('deleted_at', '=', null)
            ->paginate(10);
        }



        return view('livewire.admin-search-livewire', [
            'admins' => $this->admins,
        ]);
    }
}
