<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['nama','deskripsi'];

    public function donats()
    {
        return $this->hasMany(Donat::class, 'category_id');
    }
}
