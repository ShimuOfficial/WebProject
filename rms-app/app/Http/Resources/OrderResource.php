<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) ($this->paid_amount ?? 0),
            'paid_at' => $this->paid_at?->toISOString(),
            'inventory_deducted_at' => $this->inventory_deducted_at?->toISOString(),
            'notes' => $this->notes,
            'order_source' => $this->order_source,
            'is_customer_approved' => (bool) $this->is_customer_approved,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'table' => $this->whenLoaded('table', fn() => [
                'id' => $this->table?->id,
                'table_number' => $this->table?->table_number,
                'capacity' => $this->table?->capacity,
                'status' => $this->table?->status,
            ]),
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payment_transactions' => $this->whenLoaded('paymentTransactions', function () {
                return $this->paymentTransactions->map(fn($transaction) => [
                    'id' => $transaction->id,
                    'gateway' => $transaction->gateway,
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => (float) $transaction->amount,
                    'status' => $transaction->status,
                    'payment_method' => $transaction->payment_method,
                    'paid_at' => $transaction->paid_at?->toISOString(),
                ]);
            }),
        ];
    }
}
