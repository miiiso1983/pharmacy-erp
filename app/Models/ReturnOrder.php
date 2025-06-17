<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnOrder extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'order_id',
        'customer_id',
        'item_id',
        'quantity',
        'unit_price',
        'total_amount',
        'reason',
        'reason_description',
        'status',
        'return_date',
        'processed_by',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'return_date' => 'date',
        'reason' => 'string',
        'status' => 'string',
    ];

    // العلاقات
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // تلقائياً إنشاء رقم المرتجع
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $return->return_number = 'RET-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }

            // حساب المبلغ الإجمالي
            $return->total_amount = $return->quantity * $return->unit_price;
        });
    }
}
