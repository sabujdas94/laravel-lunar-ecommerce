<?php

namespace App\PaymentTypes;


use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Base\DataTransferObjects\PaymentRefund;
use Lunar\Events\PaymentAttemptEvent;
use Lunar\Models\Transaction;
use Lunar\PaymentTypes\AbstractPayment;
use Lunar\Models\Contracts\Transaction as TransactionContract;

class CODPayment extends AbstractPayment
{
    /**
     * Authorize the payment
     */
    public function authorize(): ?PaymentAuthorize
    {
        if (! $this->order) {
            if (! $this->order = $this->cart->draftOrder()->first()) {
                $this->order = $this->cart->createOrder();
            }
        }
        $orderMeta = array_merge(
            (array) $this->order->meta,
            $this->data['meta'] ?? []
        );

        $status = $this->data['authorized'] ?? null;

        $this->order->update([
            'status' => $status ?? ($this->config['authorized'] ?? null),
            'meta' => $orderMeta,
            'placed_at' => now(),
        ]);

        // Create capture transaction immediately (COD is instant)
        $this->order->transactions()->create([
            'success' => true,
            'type' => 'capture',
            'driver' => 'cash-on-delivery',
            'amount' => $this->order->total->value,
            'reference' => 'COD-' . $this->order->id,
            'status' => 'pending',
            'card_type' => 'cod',
            'notes' => 'Customer will pay on delivery',
            'meta' => [
                'method' => 'Cash on Delivery'
            ],
        ]);

        $response = new PaymentAuthorize(
            success: true,
            orderId: $this->order->id,
            paymentType: 'offline',
        );

        PaymentAttemptEvent::dispatch($response);

        return $response;
    }

    /**
     * Capture is not needed for COD, but must exist.
     */
    public function capture(TransactionContract $transaction, $amount = 0): PaymentCapture
    {
        return new PaymentCapture(true);
    }

    /**
     * Refund handler (optional)
     */
    public function refund(TransactionContract $transaction, int $amount = 0, $notes = null): PaymentRefund
    {
        return new PaymentRefund(true);
    }
}
