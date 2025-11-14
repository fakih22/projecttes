<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'donat_id',
        'qty',
        'total_harga',
        'status',
        'kode_transaksi',
        'snap_token',
        'nama_penerima',
        'no_hp',
        'alamat',
    ];

    protected $casts = [
        'qty' => 'integer',
        'total_harga' => 'decimal:2',
    ];

    // relasi yang benar
    public function donat()
    {
        return $this->belongsTo(\App\Models\Donat::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\TransactionItem::class);
    }
}
