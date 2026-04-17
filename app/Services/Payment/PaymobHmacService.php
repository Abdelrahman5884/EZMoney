<?php

namespace App\Services\Payment;

class PaymobHmacService
{
    public function verify(array $data): bool
    {
        $hmacSecret = config('services.paymob.hmac');

        // 🔥 ترتيب الحقول حسب Paymob docs
        $fields = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data',
            'success'
        ];

        $concatenated = '';

        foreach ($fields as $field) {
            $value = $data[$field] ?? '';
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $concatenated .= $value;
        }

        $calculatedHmac = hash_hmac('sha512', $concatenated, $hmacSecret);

        return hash_equals($calculatedHmac, $data['hmac'] ?? '');
    }
}