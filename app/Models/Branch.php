<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class Branch extends Model implements FromCollection, WithHeadings, WithStyles
{
    use HasFactory;

    // Attributes
    const STATUS = ['active', 'pending', 'inactive'];
    const POSITION = 'branch';
    protected $columns = [
        'id',
        'name',
        'region',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $branch_id;

    public function __construct($branch_id = 0)
    {
        $this->branch_id = $branch_id;
    }

    public function collection()
    {
        if (!$this->branch_id) {
            return Branch::select($this->columns)->get();
        } else {
            return Branch::select($this->columns)
                ->where('id', '=', $this->branch_id)
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
    public function getBranchStatusClassAttribute()
    {
        $class = 'label font-weight-bold label-lg  label-light-success label-inline';
        if ($this->status === 'inactive') {
            $class = 'label font-weight-bold label-lg  label-light-danger label-inline';
        } else if ($this->status === 'pending') {
            $class = 'label font-weight-bold label-lg  label-light-info label-inline';
        }
        return $class;
    }
}