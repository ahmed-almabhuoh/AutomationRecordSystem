<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class Keeper extends Authenticatable implements FromCollection, WithHeadings, WithStyles
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
    const POSITION = 'keeper';
    protected $keeper_id;
    const GENDER = ['male', 'female'];
    const STATUS = ['active', 'draft', 'blocked'];
    protected $guarded = [];


    public function __construct($keeper_id = 0)
    {
        $this->keeper_id = $keeper_id;
    }

    public function collection()
    {
        if (!$this->keeper_id) {
            return Keeper::select($this->columns)->get();
        } else {
            return Keeper::select($this->columns)
                ->where('id', '=', $this->keeper_id)
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

    public function getKeeperStatusClassAttribute()
    {
        $class = 'label font-weight-bold label-lg  label-light-success label-inline';
        if ($this->status === 'blocked') {
            $class = 'label font-weight-bold label-lg  label-light-danger label-inline';
        } else if ($this->status === 'draft') {
            $class = 'label font-weight-bold label-lg  label-light-info label-inline';
        }
        return $class;
    }

    public function getKeeperGenderClassAttribute()
    {
        return $this->status === 'male' ? 'font-weight-bold text-primary' : 'font-weight-bold text-primary';
    }

    public function getLastBlockAttribute()
    {
        return $this->blocks->first();
    }

    public function getKeeperDeletionAttribute()
    {
        return $this->deleted_at == null ? 'F' : 'T';
    }

    public function getKeeperDeletionClassAttribute()
    {
        return $this->deleted_at == null ? 'success' : 'danger';
    }

    // Relations
    public function blocks()
    {
        return $this->hasMany(Block::class, 'blocked_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'keeper_id', 'id');
    }
}
