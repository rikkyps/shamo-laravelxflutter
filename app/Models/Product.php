<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'productName', 'price', 'description', 'tags', 'isLove'];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class)->withPivot('productName'. 'qty', 'total');
    }
}
