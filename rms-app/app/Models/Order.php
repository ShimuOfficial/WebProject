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
        'cancelled_at',
        'cancellation_fee_percent',
        'refund_amount',
    ];
    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
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
            'sslcommerz' => 'SSLCommerz',
            'bkash' => 'bKash',
            'rocket' => 'Rocket',
            'card' => 'Card',
        ];

        return $labels[$this->payment_method] ?? strtoupper($this->payment_method ?? '');
    }

    /**
     * DEFENSE Q12: Index into customer tracker steps
     * Steps array: pending, approved, preparing, ready, completed (UI: Delivered)
     */
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

    /**
     * DEFENSE Q14: Customer may cancel only own online order, before stock deducted,
     * and not after ready/served/completed.
     * Board: "Cancel kobe possible?" → canBeCancelledByCustomer()
     */
    public function canBeCancelledByCustomer(): bool
    {
        if (($this->order_source ?? 'staff') !== 'customer') {
            return false;
        }

        // Once kitchen deducted ingredients, cancel is closed.
        if ($this->inventory_deducted_at) {
            return false;
        }

        $closed = config('restaurant.refund.closed_statuses', ['ready', 'served', 'completed']);
        if (in_array($this->status, $closed, true) || $this->status === 'cancelled') {
            return false;
        }

        return in_array($this->status, ['pending', 'approved', 'preparing'], true);
    }

    /**
     * DEFENSE Q14: Tiered refund math.
     * unpaid → fee 0, refund 0
     * pending paid → 0% fee (full)
     * approved paid → 20% fee
     * preparing paid → 30% fee
     * Percents live in config/restaurant.php → refund.*
     */
    public function refundBreakdown(): array
    {
        $wasPaid = in_array($this->payment_status, ['paid', 'partial'], true)
            || (!$this->is_cash_on_delivery && (float) ($this->paid_amount ?? 0) > 0);

        $paidBase = (float) ($this->paid_amount ?? 0);
        if ($paidBase <= 0 && $wasPaid) {
            $paidBase = (float) ($this->total_amount ?? 0);
        }

        if (!$wasPaid) {
            return ['fee_percent' => 0, 'refund_amount' => 0.0, 'was_paid' => false];
        }

        $fee = match ($this->status) {
            'pending' => (int) config('restaurant.refund.pending_fee_percent', 0),
            'approved' => (int) config('restaurant.refund.approved_fee_percent', 20),
            'preparing' => (int) config('restaurant.refund.preparing_fee_percent', 30),
            default => 100,
        };

        $fee = max(0, min(100, $fee));
        $refund = round($paidBase * (100 - $fee) / 100, 2);

        return [
            'fee_percent' => $fee,
            'refund_amount' => $refund,
            'was_paid' => true,
        ];
    }

    public function cancellationPolicyHint(): string
    {
        if ($this->status === 'cancelled') {
            if ((float) ($this->refund_amount ?? 0) > 0) {
                return 'Cancelled. Refund of ৳' . number_format((float) $this->refund_amount, 2)
                    . ' after a ' . (int) ($this->cancellation_fee_percent ?? 0) . '% fee.';
            }

            return 'This order is already cancelled.';
        }

        if (!$this->canBeCancelledByCustomer()) {
            return match ($this->status) {
                'preparing', 'ready', 'served' => 'This order can no longer be cancelled.',
                'completed' => 'Delivered orders are not refundable.',
                default => 'Cancellation depends on the current order status.',
            };
        }

        $breakdown = $this->refundBreakdown();
        if (!$breakdown['was_paid']) {
            return 'You can cancel now at no charge (cash on delivery / unpaid).';
        }

        return match ($this->status) {
            'pending' => 'Cancel now for a full refund (0% fee).',
            'approved' => 'Cancel before cooking starts: 20% fee, 80% refunded.',
            'preparing' => 'Late cancel: 30% fee, 70% refunded (only if kitchen has not deducted stock).',
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
