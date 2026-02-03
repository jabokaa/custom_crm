<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'crud_table_id',
        'column_name',
        'label',
        'description',
        'field_type',
        'options',
        'css_class',
        'icon',
        'position',
        'is_required',
        'is_visible',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function crudTable()
    {
        return $this->belongsTo(CrudTable::class);
    }
}
