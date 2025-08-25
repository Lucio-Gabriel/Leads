<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'full_name'         => $this->full_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'status'            => $this->status,
            'registration_date' => $this->registration_date,
        ];
    }
}
