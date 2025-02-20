<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CertificateVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'ip_address',
        'user_agent',
        'verified_at',
    ];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
