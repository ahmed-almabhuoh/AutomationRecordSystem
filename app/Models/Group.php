<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Group extends Model implements FromCollection, WithHeadings, WithStyles
{
    use HasFactory, SoftDeletes;

    // Attributes
    const STATUS = ['active', 'pending', 'inactive'];
    const POSITION = 'group';
    protected $columns = [
        'id',
        'name',
        'region',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $group_id;

    public function __construct($group_id = 0)
    {
        $this->group_id = $group_id;
    }

    public function collection()
    {
        if (!$this->group_id) {
            return Group::select($this->columns)->get();
        } else {
            return Group::select($this->columns)
                ->where('id', '=', $this->group_id)
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

    // Get Attributes
    public function getGroupStatusClassAttribute()
    {
        $class = 'label font-weight-bold label-lg  label-light-success label-inline';
        if ($this->status === 'inactive') {
            $class = 'label font-weight-bold label-lg  label-light-danger label-inline';
        } else if ($this->status === 'pending') {
            $class = 'label font-weight-bold label-lg  label-light-info label-inline';
        }
        return $class;
    }

    public function getGroupDeletionAttribute()
    {
        return $this->deleted_at == null ? 'F' : 'T';
    }

    public function getGroupDeletionClassAttribute()
    {
        return $this->deleted_at == null ? 'success' : 'danger';
    }

    // Relations
    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id', 'id');
    }

    public function keeper()
    {
        return $this->belongsTo(Keeper::class, 'keeper_id', 'id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'group_student');
    }
}
