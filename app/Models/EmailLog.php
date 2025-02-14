<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_id',
        'certificate_id',
        'sent_at'
    ];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
