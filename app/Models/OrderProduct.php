<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'plan_id',
        'quantity',
        'price',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    /**
     * Get the order that owns the order product.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function currency(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order->currency
        );
    }

    /**
     * Get the description for the next invoice item.
     */
    public function description(): Attribute
    {
        $endDate = $this->expires_at->addDays($this->plan->billing_duration);

        return Attribute::make(
            get: fn () => $this->product->name . ' (' . $this->expires_at->format('M d, Y') . ' - ' . $endDate->format('M d, Y') . ')'
        );
    }

    /**
     * Get the product corresponding to the order product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the plan corresponding to the order product.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the order product's configurations.
     */
    public function configs()
    {
        return $this->hasMany(OrderProductConfig::class);
    }

    /**
     * Get invoiceItems
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get invoices
     */
    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, InvoiceItem::class, 'order_product_id', 'id', 'order_product_id', 'invoice_id');
    }
}