<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WarehouseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'item_id',
        'quantity',
        'unit_cost',
        'location',
        'last_updated',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'last_updated' => 'date',
    ];

    // العلاقات
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
