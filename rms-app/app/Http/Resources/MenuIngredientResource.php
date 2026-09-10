<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuIngredientResource extends JsonResource
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
            'inventory_id' => $this->inventory_id,
            'quantity_per_dish' => (float) $this->quantity_per_dish,
            'inventory' => [
                'id' => $this->inventory?->id,
                'item_name' => $this->inventory?->item_name,
                'unit' => $this->inventory?->unit,
            ],
        ];
    }
}
