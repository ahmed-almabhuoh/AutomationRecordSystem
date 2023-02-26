<?php

namespace App\Http\Livewire;

use App\Models\StudentParent;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class StudentParentSearchLivewire extends Component
{
    use WithPagination;

    public function __construct()
    {
        $this->student_parents = new Collection();
    }

    protected $student_parents;
    public $searchTerm;
    public $type = 'all';

    // public function filterAdmins($type)
    // {
    //     // dd($type);
    //     if ($type === 'all') {
    //         $this->student_parents = StudentParent::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })->paginate(10);
    //     } else if ($type === 'only_trashed') {
    //         $this->student_parents = StudentParent::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->trashed()
    //             ->paginate(10);
    //     } else if ($type === 'not_trashed') {
    //         $this->student_parents = StudentParent::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->where('deleted_at', '=', null)
    //             ->paginate(10);
    //     }

    //     return $this->student_parents;
    // }

    public function mount($student_parents)
    {
        $this->student_parents = $student_parents;
    }

    public function render()
    {

        if ($this->type === 'all') {
            $this->student_parents = StudentParent::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })->paginate(10);
        } else if ($this->type === 'only_trashed') {
            $this->student_parents = StudentParent::where(function ($query) {
                    $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
                })
                ->where('deleted_at', '!=', null)
                ->paginate(10);
        }else {
            $this->student_parents = StudentParent::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })
            ->where('deleted_at', '=', null)
            ->paginate(10);
        }



        return view('livewire.student-parent-search-livewire', [
            'student_parents' => $this->student_parents,
        ]);
    }
}
