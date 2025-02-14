<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable =  [
        'recipient_id',
        'code',
        'issued_date',
        'created_by'
    ];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
