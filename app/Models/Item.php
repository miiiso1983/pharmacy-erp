<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'unit',
        'price',
        'cost',
        'stock_quantity',
        'min_stock_level',
        'supplier_id',
        'barcode',
        'expiry_date',
        'batch_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'expiry_date' => 'date',
        'status' => 'string',
    ];

    // العلاقات
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnOrder::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_items')
                    ->withPivot('quantity', 'unit_cost', 'location', 'last_updated', 'notes')
                    ->withTimestamps();
    }

    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }
}
