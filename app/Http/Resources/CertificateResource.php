<?php

namespace App\Http\Resources;

use App\Models\Recipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'recipient' => Recipient::find($this->recipient_id)->name,
            'code' => $this->code,
            'issued_date' => $this->issued_date,
            'created_by' => User::find($this->created_by)->name
        ];
    }
}
