<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentGateways = [
            [
                'nama_gateway' => 'Midtrans - All Payment',
                'kode_gateway' => 'midtrans_all',
                'logo_url' => '/images/payment/midtrans.png',
                'is_active' => true,
                'config' => json_encode([
                    'server_key' => env('MIDTRANS_SERVER_KEY', 'YOUR_SERVER_KEY'),
                    'client_key' => env('MIDTRANS_CLIENT_KEY', 'YOUR_CLIENT_KEY'),
                    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
                    'is_sanitized' => true,
                    'is_3ds' => true,
                    'enabled_payments' => [
                        'credit_card', 'gopay', 'shopeepay', 'qris',
                        'bca_va', 'bni_va', 'bri_va', 'mandiri_va', 'permata_va',
                        'echannel', 'other_va', 'alfamart', 'indomaret'
                    ]
                ])
            ],
            [
                'nama_gateway' => 'GoPay',
                'kode_gateway' => 'gopay',
                'logo_url' => '/images/payment/gopay.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'gopay',
                    'fee_percentage' => 2,
                    'description' => 'Bayar dengan GoPay, mudah dan cepat'
                ])
            ],
            [
                'nama_gateway' => 'ShopeePay',
                'kode_gateway' => 'shopeepay',
                'logo_url' => '/images/payment/shopeepay.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'shopeepay',
                    'fee_percentage' => 2,
                    'description' => 'Bayar dengan ShopeePay'
                ])
            ],
            [
                'nama_gateway' => 'OVO',
                'kode_gateway' => 'ovo',
                'logo_url' => '/images/payment/ovo.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'xendit',
                    'payment_type' => 'ewallet',
                    'ewallet_type' => 'OVO',
                    'fee_percentage' => 2,
                    'description' => 'Bayar dengan OVO'
                ])
            ],
            [
                'nama_gateway' => 'DANA',
                'kode_gateway' => 'dana',
                'logo_url' => '/images/payment/dana.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'xendit',
                    'payment_type' => 'ewallet',
                    'ewallet_type' => 'DANA',
                    'fee_percentage' => 2,
                    'description' => 'Bayar dengan DANA'
                ])
            ],
            [
                'nama_gateway' => 'QRIS',
                'kode_gateway' => 'qris',
                'logo_url' => '/images/payment/qris.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'qris',
                    'fee_percentage' => 0.7,
                    'description' => 'Bayar dengan QRIS - Semua e-wallet'
                ])
            ],
            [
                'nama_gateway' => 'Bank Transfer - BCA',
                'kode_gateway' => 'bca_va',
                'logo_url' => '/images/payment/bca.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'bank_transfer',
                    'bank' => 'bca',
                    'fee_fixed' => 4000,
                    'description' => 'Transfer ke Virtual Account BCA',
                    'instruction' => 'Transfer ke nomor VA yang tertera'
                ])
            ],
            [
                'nama_gateway' => 'Bank Transfer - BNI',
                'kode_gateway' => 'bni_va',
                'logo_url' => '/images/payment/bni.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'bank_transfer',
                    'bank' => 'bni',
                    'fee_fixed' => 4000,
                    'description' => 'Transfer ke Virtual Account BNI'
                ])
            ],
            [
                'nama_gateway' => 'Bank Transfer - BRI',
                'kode_gateway' => 'bri_va',
                'logo_url' => '/images/payment/bri.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'bank_transfer',
                    'bank' => 'bri',
                    'fee_fixed' => 4000,
                    'description' => 'Transfer ke Virtual Account BRI'
                ])
            ],
            [
                'nama_gateway' => 'Bank Transfer - Mandiri',
                'kode_gateway' => 'mandiri_va',
                'logo_url' => '/images/payment/mandiri.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'echannel',
                    'bank' => 'mandiri',
                    'fee_fixed' => 4000,
                    'description' => 'Transfer ke Virtual Account Mandiri (E-Channel)'
                ])
            ],
            [
                'nama_gateway' => 'Bank Transfer - Permata',
                'kode_gateway' => 'permata_va',
                'logo_url' => '/images/payment/permata.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'bank_transfer',
                    'bank' => 'permata',
                    'fee_fixed' => 4000,
                    'description' => 'Transfer ke Virtual Account Permata'
                ])
            ],
            [
                'nama_gateway' => 'Credit Card',
                'kode_gateway' => 'credit_card',
                'logo_url' => '/images/payment/credit-card.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'credit_card',
                    'fee_percentage' => 2.9,
                    'installment' => [
                        'required' => false,
                        'terms' => [3, 6, 12]
                    ],
                    'description' => 'Bayar dengan Kartu Kredit (Visa, Mastercard, JCB)',
                    'secure_3d' => true
                ])
            ],
            [
                'nama_gateway' => 'Alfamart',
                'kode_gateway' => 'alfamart',
                'logo_url' => '/images/payment/alfamart.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'cstore',
                    'store' => 'alfamart',
                    'fee_fixed' => 2500,
                    'description' => 'Bayar di Alfamart terdekat',
                    'instruction' => 'Tunjukkan kode payment ke kasir Alfamart'
                ])
            ],
            [
                'nama_gateway' => 'Indomaret',
                'kode_gateway' => 'indomaret',
                'logo_url' => '/images/payment/indomaret.png',
                'is_active' => true,
                'config' => json_encode([
                    'via' => 'midtrans',
                    'payment_type' => 'cstore',
                    'store' => 'indomaret',
                    'fee_fixed' => 2500,
                    'description' => 'Bayar di Indomaret terdekat',
                    'instruction' => 'Tunjukkan kode payment ke kasir Indomaret'
                ])
            ],
            [
                'nama_gateway' => 'Manual Transfer',
                'kode_gateway' => 'manual_transfer',
                'logo_url' => '/images/payment/bank.png',
                'is_active' => true,
                'config' => json_encode([
                    'payment_type' => 'manual',
                    'bank_accounts' => [
                        [
                            'bank_name' => 'BCA',
                            'account_number' => '1234567890',
                            'account_name' => 'PT Saintara Indonesia'
                        ],
                        [
                            'bank_name' => 'Mandiri',
                            'account_number' => '0987654321',
                            'account_name' => 'PT Saintara Indonesia'
                        ]
                    ],
                    'description' => 'Transfer manual ke rekening perusahaan',
                    'instruction' => 'Transfer ke salah satu rekening dan upload bukti transfer',
                    'requires_proof' => true,
                    'verification' => 'manual'
                ])
            ]
        ];

        foreach ($paymentGateways as $gateway) {
            \App\Models\PaymentGateway::create($gateway);
        }
    }
}
