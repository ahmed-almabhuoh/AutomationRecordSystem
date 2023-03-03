<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Supervisor extends Authenticatable implements FromCollection, WithHeadings, WithStyles
{
    use HasFactory, SoftDeletes, HasApiTokens;


    // Attributes
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
    const POSITION = 'supervisor';
    protected $supervisor_id;
    const GENDER = ['male', 'female'];
    const STATUS = ['active', 'draft', 'blocked'];
    protected $guarded = [];


    public function __construct($supervisor_id = 0)
    {
        $this->supervisor_id = $supervisor_id;
    }

    public function collection()
    {
        if (!$this->supervisor_id) {
            return Supervisor::select($this->columns)->get();
        } else {
            return Supervisor::select($this->columns)
                ->where('id', '=', $this->supervisor_id)
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



    // Get - Attributes
    public function getFullNameAttribute()
    {
        return $this->fname . ' ' . $this->sname . ' ' . $this->tname . ' ' . $this->lname;
    }

    public function getSupervisorStatusClassAttribute()
    {
        $class = 'label font-weight-bold label-lg  label-light-success label-inline';
        if ($this->status === 'blocked') {
            $class = 'label font-weight-bold label-lg  label-light-danger label-inline';
        } else if ($this->status === 'draft') {
            $class = 'label font-weight-bold label-lg  label-light-info label-inline';
        }
        return $class;
    }

    public function getSupervisorGenderClassAttribute()
    {
        return $this->status === 'male' ? 'font-weight-bold text-primary' : 'font-weight-bold text-primary';
    }

    public function getLastBlockAttribute()
    {
        return $this->blocks->first();
    }

    public function getSupervisorDeletionAttribute()
    {
        return $this->deleted_at == null ? 'F' : 'T';
    }

    public function getSupervisorDeletionClassAttribute()
    {
        return $this->deleted_at == null ? 'success' : 'danger';
    }

    // Relations
    public function blocks()
    {
        return $this->hasMany(Block::class, 'blocked_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function sc()
    {
        return $this->belongsTo(SupervisionCommittee::class, 'sc_id', 'id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'supervisor_id', 'id');
    }
}
