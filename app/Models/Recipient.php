<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipient extends Model
{
    use HasFactory;

    protected $fillable =  [
        'name',
        'email'
    ];

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'recipient_id');
    }
}
