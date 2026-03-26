<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'created_by', 'color'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenses() {
        return $this->hasMany(Expense::class);
    }
}
