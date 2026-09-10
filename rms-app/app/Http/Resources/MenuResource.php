<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MenuResource extends JsonResource
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
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'price' => (float) $this->price,
            'image' => $this->image,
            'is_available' => (bool) $this->is_available,
            'ingredients_count' => $this->whenCounted('menuIngredients'),
            'ingredients' => \App\Http\Resources\MenuIngredientResource::collection($this->whenLoaded('menuIngredients')),
        ];
    }
}
