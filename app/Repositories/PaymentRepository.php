<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Support\Arr;

class PaymentRepository
{
    /**
     * @param array $data
     * @return Payment
     */
    public function store(array $data): Payment
    {
        return Payment::create(Arr::only($data, ['amount', 'member_id']));
    }
}
