<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
                'user_type' => $this->user_type,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'designation' => $this->designation,
                'office' => $this->office,
                'email' => $this->email,
                'nominee' => $this->whenLoaded('nominee'),
                'created_at' => $this->created_at,
            ];
        }
}
