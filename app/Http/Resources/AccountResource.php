<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Name' => $this->Name,
            'Phone' => $this->Phone,
            'CompanyName' => $this->CompanyName,
            'CompanyAddress' => $this->CompanyAddress,
            'Email' => $this->Email,
            'CustomerId' => $this->CustomerId,
            'WalletAmount' => $this->WalletAmount,
            'Image' => $this->Image,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
