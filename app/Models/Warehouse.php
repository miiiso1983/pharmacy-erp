<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'city',
        'area',
        'address',
        'phone',
        'manager',
        'type',
        'status',
        'total_value',
        'total_items',
        'notes',
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'total_items' => 'integer',
    ];

    // العلاقات
    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'warehouse_items')
                    ->withPivot('quantity', 'unit_cost', 'location', 'last_updated', 'notes')
                    ->withTimestamps();
    }

    // الدوال المساعدة
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'success',
            'inactive' => 'secondary',
            'maintenance' => 'warning'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getTypeLabelAttribute()
    {
        $types = [
            'main' => 'مخزن رئيسي',
            'branch' => 'مخزن فرعي',
            'pharmacy' => 'صيدلية',
            'distribution' => 'مركز توزيع'
        ];

        return $types[$this->type] ?? 'غير محدد';
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'maintenance' => 'صيانة'
        ];

        return $statuses[$this->status] ?? 'غير محدد';
    }
}
