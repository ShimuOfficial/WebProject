<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DEFENSE: §5.9 orders — status, payment, customer source, cancel policy
 * Board: "Customer kobe cancel korte pare?" → canBeCancelledByCustomer()
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'table_id',
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'total_amount',
        'paid_amount',
        'paid_at',
        'inventory_deducted_at',
        'reserved_requirements',
        'reserved_at',
        'notes',
        'order_source',
        'is_customer_approved',
    ];
    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'inventory_deducted_at' => 'datetime',
        'reserved_at' => 'datetime',
        'reserved_requirements' => 'array',
        'is_customer_approved' => 'boolean',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function calculateTotal()
    {
        $this->total_amount = $this->items->sum('subtotal');
        $this->save();
        return $this->total_amount;
    }

    public static function generateOrderNumber()
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $lastOrder = static::where('order_number', 'like', $prefix . '%')->orderBy('order_number', 'desc')->first();
        $num = $lastOrder ? (int) substr($lastOrder->order_number, -4) + 1 : 1;
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getIsOnlineOrderAttribute(): bool
    {
        return ($this->order_source ?? 'staff') === 'customer';
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'completed' && $this->isOnlineOrder) {
            return 'Delivered';
        }

        // Keep a simple, predictable display label derived from status.
        return ucfirst((string) $this->status);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        $labels = [
            'cash' => 'COD',
            'bkash' => 'bKash',
            'rocket' => 'Rocket',
            'card' => 'Card',
        ];

        return $labels[$this->payment_method] ?? strtoupper($this->payment_method ?? '');
    }

    public function getCustomerTrackIndexAttribute(): int
    {
        $status = $this->status === 'served' ? 'ready' : $this->status;
        $steps = ['pending', 'approved', 'preparing', 'ready', 'completed'];
        $currentIndex = array_search($status, $steps, true);

        if ($currentIndex !== false) {
            return $currentIndex;
        }

        return $status === 'cancelled' ? -1 : 0;
    }

    public function canBeCancelledByCustomer(): bool
    {
        if (($this->order_source ?? 'staff') !== 'customer') {
            return false;
        }

        if ($this->inventory_deducted_at) {
            return false;
        }

        return in_array($this->status, ['pending', 'approved'], true);
    }

    public function cancellationPolicyHint(): string
    {
        return match ($this->status) {
            'pending' => 'You can cancel now at no charge.',
            'approved' => 'You can still cancel until the kitchen starts cooking. Paid orders are fully refunded.',
            'preparing', 'ready', 'served' => 'This order can no longer be cancelled.',
            'completed' => 'Delivered orders are not refundable.',
            'cancelled' => 'This order is already cancelled.',
            default => 'Cancellation depends on the current order status.',
        };
    }

    public function getIsCashOnDeliveryAttribute(): bool
    {
        return ($this->payment_method ?? 'cash') === 'cash';
    }

    public function getIsPayableOnlineAttribute(): bool
    {
        return ! $this->is_cash_on_delivery && ($this->payment_status ?? 'unpaid') !== 'paid';
    }

    public function getDueAmountAttribute(): float
    {
        return max((float) $this->total_amount - (float) ($this->paid_amount ?? 0), 0);
    }

    public function getChangeAmountAttribute(): float
    {
        return max((float) ($this->paid_amount ?? 0) - (float) ($this->total_amount ?? 0), 0);
    }
}
