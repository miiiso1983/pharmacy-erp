<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_number',
        'invoice_id',
        'customer_id',
        'amount',
        'payment_method',
        'reference_number',
        'collection_date',
        'collected_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collection_date' => 'date',
        'payment_method' => 'string',
    ];

    // العلاقات
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // تلقائياً إنشاء رقم التحصيل
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($collection) {
            if (empty($collection->collection_number)) {
                $collection->collection_number = 'COL-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($collection) {
            // تحديث المبلغ المدفوع في الفاتورة
            $invoice = $collection->invoice;
            $invoice->paid_amount += $collection->amount;
            $invoice->save();
        });
    }
}
