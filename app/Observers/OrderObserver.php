<?php

namespace App\Observers;

use Lunar\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $captures = $order->captures;

        // Ensure this is a COD order
        if ($captures->where('driver', 'cash-on-delivery')->isEmpty()) {
            return;
        }

        // Check if status changed to delivered/completed
        if ($order->isDirty('status') && in_array($order->status, ['delivered', 'payment-received'])) {
            // Find the pending COD transaction and mark it as paid
            $codTransaction = $order->transactions()
                ->where('driver', 'cash-on-delivery')
                ->where('status', 'pending')
                ->first();

            if ($codTransaction) {
                $codTransaction->update([
                    'status' => 'paid',
                    'meta' => array_merge(
                        (array) $codTransaction->meta,
                        [
                            'paid_at' => now()->toISOString(),
                            'marked_paid_by_status_update' => true,
                        ]
                    ),
                ]);
            }
        }
    }
}
