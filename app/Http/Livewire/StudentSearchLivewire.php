<?php

namespace App\Http\Livewire;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class StudentSearchLivewire extends Component
{
    use WithPagination;

    public function __construct()
    {
        $this->students = new Collection();
    }

    protected $students;
    public $searchTerm;
    public $type = 'all';

    // public function filterAdmins($type)
    // {
    //     // dd($type);
    //     if ($type === 'all') {
    //         $this->students = Student::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })->paginate(10);
    //     } else if ($type === 'only_trashed') {
    //         $this->students = Student::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->trashed()
    //             ->paginate(10);
    //     } else if ($type === 'not_trashed') {
    //         $this->students = Student::where(function ($query) {
    //             $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
    //                 ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
    //         })
    //             ->where('deleted_at', '=', null)
    //             ->paginate(10);
    //     }

    //     return $this->students;
    // }

    public function mount($students)
    {
        $this->students = $students;
    }

    public function render()
    {

        if ($this->type === 'all') {
            $this->students = Student::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })->paginate(10);
        } else if ($this->type === 'only_trashed') {
            $this->students = Student::where(function ($query) {
                    $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                        ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
                })
                ->where('deleted_at', '!=', null)
                ->paginate(10);
        }else {
            $this->students = Student::where(function ($query) {
                $query->where('fname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('sname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('tname', 'LIKE', '%' . $this->searchTerm . '%')
                    ->orWhere('lname', 'LIKE', '%' . $this->searchTerm . '%');
            })
            ->where('deleted_at', '=', null)
            ->paginate(10);
        }



        return view('livewire.student-search-livewire', [
            'students' => $this->students,
        ]);
    }
}
