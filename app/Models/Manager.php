<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Manager extends Authenticatable implements FromCollection, WithHeadings, WithStyles
{
    use HasFactory;

    protected $columns = [
        'id',
        'fname',
        'sname',
        'tname',
        'lname',
        'email',
        'phone',
        'identity_no',
        'gender',
        'status',
        'local_region',
        'description',
        'updated_at',
        'created_at',
    ];
    const POSITION = 'manager';
    protected $manager_id;

    public function __construct($manager_id = 0)
    {
        $this->manager_id = $manager_id;
    }

    public function collection()
    {
        if (!$this->manager_id) {
            return Manager::select($this->columns)->get();
        } else {
            return Manager::select($this->columns)
                ->where('id', '=', $this->manager_id)
                ->get();
        }
    }

    public function headings(): array
    {
        return $this->columns;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:N1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFC0C0C0');
    }

    // Attributes
    const GENDER = ['male', 'female'];
    const STATUS = ['active', 'draft', 'blocked'];

    // Get - Attributes
    public function getFullNameAttribute()
    {
        return $this->fname . ' ' . $this->sname . ' ' . $this->tname . ' ' . $this->lname;
    }

    public function getManagerStatusClassAttribute()
    {
        $class = 'label font-weight-bold label-lg  label-light-success label-inline';
        if ($this->status === 'blocked') {
            $class = 'label font-weight-bold label-lg  label-light-danger label-inline';
        } else if ($this->status === 'draft') {
            $class = 'label font-weight-bold label-lg  label-light-info label-inline';
        }
        return $class;
    }

    public function getManagerGenderClassAttribute()
    {
        return $this->status === 'male' ? 'font-weight-bold text-primary' : 'font-weight-bold text-primary';
    }

    // Relations
    public function blocks () {
        return $this->hasMany(Block::class, 'blocked_id', 'id')->orderBy('created_at', 'DESC');
    }
}
