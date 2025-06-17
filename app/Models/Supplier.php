<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // العلاقات
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
