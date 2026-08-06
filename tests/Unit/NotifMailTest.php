<?php

namespace Tests\Unit;

use App\Mail\NotifMail;
use App\Models\Customer;
use App\Models\Order;
use Tests\TestCase;

class NotifMailTest extends TestCase
{
    public function test_order_notification_mailable_renders_correctly(): void
    {
        $order = new Order([
            'id' => 123,
            'order_type' => 'sell',
            'delivery_method' => 'warehouse',
            'status' => 'pending',
            'pickup_address' => 'Jl. Contoh 1',
            'pickup_address_note' => 'Tinggal dekat toko',
        ]);

        $customer = new Customer([
            'name' => 'Erich',
            'phone_number' => '08123456789',
            'address' => 'Jl. Contoh 1',
        ]);

        $mail = new NotifMail($order, $customer, 150000.00);

        $html = $mail->render();

        $this->assertStringContainsString('Pesanan Baru Diterima', $html);
        $this->assertStringContainsString('#123', $html);
        $this->assertStringContainsString('Erich', $html);
    }
}
