<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'listing_id', 'buyer_id', 'offered_price', 'payment_method',
        'status', 'message', 'seller_note', 'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    const PAYMENT_METHODS = [
        'cod'            => ['label' => 'Cash on Delivery',   'icon' => '🚚', 'desc' => 'Pay when item is delivered'],
        'cash_on_pickup' => ['label' => 'Cash on Pickup',     'icon' => '🤝', 'desc' => 'Pay when you collect the item'],
        'bank_transfer'  => ['label' => 'Bank Transfer',      'icon' => '🏦', 'desc' => 'Transfer to seller\'s bank account'],
        'installment'    => ['label' => 'Installment',        'icon' => '📅', 'desc' => 'Pay in agreed instalments'],
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method]['label'] ?? ucfirst($this->payment_method);
    }

    public function getPaymentIconAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method]['icon'] ?? '💳';
    }
}
