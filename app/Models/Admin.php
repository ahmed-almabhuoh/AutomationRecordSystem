<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements FromCollection, WithHeadings, WithStyles
{
    use HasFactory, SoftDeletes;

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
    const POSITION = 'admin';
    protected $admin_id;
    const GENDER = ['male', 'female'];
    const STATUS = ['active', 'draft', 'blocked'];


    public function __construct($admin_id = 0)
    {
        $this->admin_id = $admin_id;
    }

    public function collection()
    {
        if (!$this->admin_id) {
            return Admin::select($this->columns)->get();
        } else {
            return Admin::select($this->columns)
                ->where('id', '=', $this->admin_id)
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

    public function getAdminStatusClassAttribute()
    {
        $class = 'label font-weight-bold label-lg  label-light-success label-inline';
        if ($this->status === 'blocked') {
            $class = 'label font-weight-bold label-lg  label-light-danger label-inline';
        } else if ($this->status === 'draft') {
            $class = 'label font-weight-bold label-lg  label-light-info label-inline';
        }
        return $class;
    }

    public function getAdminGenderClassAttribute()
    {
        return $this->status === 'male' ? 'font-weight-bold text-primary' : 'font-weight-bold text-primary';
    }

    public function getLastBlockAttribute()
    {
        return $this->blocks->first();
    }

    public function getAdminDeletionAttribute()
    {
        return $this->deleted_at == null ? 'F' : 'T';
    }

    public function getAdminDeletionClassAttribute()
    {
        return $this->deleted_at == null ? 'success' : 'danger';
    }

    // Relations
    public function blocks()
    {
        return $this->hasMany(Block::class, 'blocked_id', 'id')->orderBy('created_at', 'DESC');
    }
}
