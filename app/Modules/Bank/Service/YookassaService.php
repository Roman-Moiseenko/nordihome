<?php

namespace App\Modules\Bank\Service;

use App\Modules\Order\Entity\Order\Order;
use YooKassa\Client;

class YookassaService
{
    public function create_payment(): string
    {
        $client = new Client();
        $client->setAuth(1062762, 'test_MAp2FTq0eut9hgOzq_sVUsHGDbRIHs9e9zFkr-JKIxQ');


        $payment = $client->createPayment(
            [
                'amount' => array(
                    'value' => 100.0,
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => 'https://www.example.com/return_url',
                ),
                'capture' => true,
                'description' => 'Заказ №1',
            ],

            uniqid('', true)
        );

        return '';
    }


    public function test()
    {
        $client = new Client();
        $client->setAuth(1062762, 'test_MAp2FTq0eut9hgOzq_sVUsHGDbRIHs9e9zFkr-JKIxQ');


        $payment = $client->createPayment(
            [
                'amount' => [
                    'value' => 200.0,
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => 'https://www.example.com/return_url',
                ],
                'capture' => true,
                'description' => 'Предоплата по Заказу №23',
                'receipt' => [
                    'customer' => [
                        'email' => 'r.a.moiseenko@gmail.com',
                    ],
                    'items' => [
                        [
                            'description' => 'Товар 237',
                            'quantity' => 2.000,
                            'amount' => [
                                'value' => 100.00,
                                'currency' => 'RUB'
                            ],
                            'vat_code' => 1,
                            'payment_mode' => 'full_prepayment',
                            'payment_subject' => 'commodity',
                            'measure' => 'piece',
                        ],
                    ]
                ],
                'metadata' => [
                    'class' => Order::class,
                    'id' => 1,
                ],
            ],
            uniqid('', true)
        );

        return $payment;
    }


    public function test2()
    {
        $client = new Client();
        $client->setAuth(1062762, 'test_MAp2FTq0eut9hgOzq_sVUsHGDbRIHs9e9zFkr-JKIxQ');

        $response = $client->createReceipt(
            array(
                'customer' => [
                    'full_name' => 'Ivanov Ivan Ivanovich',
                    'phone' => '+79000000000',
                    'email' => 'email@email.ru',
                    'inn'   => '6321341814',
                ],
                'payment_id' => '2f82cb0c-000f-5000-b000-1ccd91a643ee',
                'type' => 'payment',
                'send' => true,
                'items' => [
                    [
                        'description' => 'Товар 237',
                        'quantity' => 2.000,
                        'amount' => [
                            'value' => 100.00,
                            'currency' => 'RUB'
                        ],
                        'vat_code' => 1,
                        'payment_mode' => 'full_payment',
                        'payment_subject' => 'commodity',

                      /*  'mark_mode' => 0,
                        'mark_code_info' => [
                            'gs_1m' => 'DFGwNDY0MDE1Mzg2NDQ5MjIxNW9vY2tOelDFuUFwJh05MUVFMDYdOTJXK2ZaMy9uTjMvcVdHYzBjSVR3NFNOMWg1U2ZLV0dRMWhHL0UrZi8ydkDvPQ==',
                        ],
                       */
                    ],

                ],
                'settlements' => [
                    [
                        'type' => 'prepayment',
                        'amount' => [
                            'value' => '200.00',
                            'currency' => 'RUB',
                        ]
                    ],
                ],
            ),
            uniqid('', true)
        );

        return $response;
    }

    public function test3()
    {
        $client = new Client();
        $client->setAuth(1062762, 'test_MAp2FTq0eut9hgOzq_sVUsHGDbRIHs9e9zFkr-JKIxQ');


        $payment = $client->createPayment(
            [
                'amount' => [
                    'value' => 100.0,
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => 'https://www.example.com/return_url',
                ],
                'capture' => true,
                'description' => 'Предоплата по Заказу №13',
                'metadata' => [
                    'class' => Order::class,
                    'id' => 1,
                    'type' => 'prepare',
                ],
            ],
            uniqid('', true)
        );

        return $payment;
    }

    public function test4()
    {
        $client = new Client();
        $client->setAuth(1062762, 'test_MAp2FTq0eut9hgOzq_sVUsHGDbRIHs9e9zFkr-JKIxQ');
        $paymentId = '2f82b687-000f-5000-a000-110dacdb5840';
        $payment = $client->getPaymentInfo($paymentId);
        return $payment['status'];

    }

    public function create_receipt()
    {
        $id = '2f8017b7-000f-5000-b000-198510c2b656';
        $client = new Client(); //
    }
}
