<?php

namespace App\Modules\Bank\Service;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderPayment;
use JetBrains\PhpStorm\Deprecated;
use YooKassa\Client;
use YooKassa\Request\Payments\CreatePaymentResponse;
use YooKassa\Request\Receipts\AbstractReceiptResponse;

class YookassaService
{
    const string SUCCESS = 'succeeded';
    const string CANCELLED = 'canceled';
    const string WAITING = 'waiting_for_capture';
    const string PENDING = 'pending';

    public function createPayment(Order $order): ?CreatePaymentResponse
    {
        $client = new Client();
        $client->setAuth(config('shop.yookassa-id'), config('shop.yookassa-key'));

        $payment = $client->createPayment(
            [
                'customer' => [
                    'full_name' => $order->user->fullname->getFullName(),
                    'phone' => $order->user->phone,
                    'email' => $order->user->email,
                ],
                'amount' => [
                    'value' => $order->getTotalAmount(),
                    'currency' => 'RUB',
                ],
                'payment_method_data' => [
                    'type' => 'bank_card', //sbp
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => parse_url(url()->current(), PHP_URL_HOST),
                ],
                'capture' => true,
                'description' => 'Предоплата по Заказу №' . $order->number . ' от ' . $order->htmlDate(),
                'receipt' => [
                    'customer' => [
                        'full_name' => $order->user->fullname->getFullName(),
                        'phone' => $order->user->phone,
                        'email' => $order->user->email,
                    ],
                    'items' => $this->items($order),
                ],
                'metadata' => [
                    'class' => Order::class,
                    'id' => $order->id,
                ],
            ],

            uniqid('', true)
        );

        return $payment;
    }

    public function createReceipt(OrderExpense $expense, OrderPayment $payment): ?AbstractReceiptResponse
    {
        $client = new Client();
        $client->setAuth(config('shop.yookassa-id'), config('shop.yookassa-key'));

        $response = $client->createReceipt(
            [
                'customer' => [
                    'full_name' => $expense->order->user->fullname->getFullName(),
                    'phone' => $expense->order->user->phone,
                    'email' => $expense->order->user->email,
                ],
                'payment_id' => $payment->yookassa_id,
                'type' => 'payment',
                'send' => true,
                'items' => $this->itemsExpense($expense),
                'settlements' => [
                    [
                        'type' => 'prepayment',
                        'amount' => [
                            'value' => $expense->getAmount(),
                            'currency' => 'RUB',
                        ]
                    ],
                ],
            ],
            uniqid('', true)
        );

        return $response;
    }

    private function items(Order $order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'description' => $item->product->name,
                'quantity' => $item->quantity,
                'amount' => [
                    'value' => $item->sell_cost,
                    'currency' => 'RUB'
                ],
                'vat_code' => 1,
                'payment_mode' => 'full_prepayment',
                'payment_subject' => 'commodity',
                'measure' => 'piece',
            ];
        }

        foreach ($order->additions as $addition) {
            $items[] = [
                'description' => $addition->addition->name,
                'quantity' => $addition->quantity,
                'amount' => [
                    'value' => $addition->getAmount(),
                    'currency' => 'RUB'
                ],
                'vat_code' => 1,
                'payment_mode' => 'full_prepayment',
                'payment_subject' => 'commodity',
                'measure' => 'piece',
            ];
        }
        return $items;
    }

    private function itemsExpense(OrderExpense $expense): array
    {
        $items = [];
        foreach ($expense->items as $item) {
            if (!empty($item->honest_signs)) {
                foreach ($item->honest_signs as $sign) { //Раскидываем список qr-кодов по товарам.
                    $items[] = [
                        'description' => $item->orderItem->product->name,
                        'quantity' => 1,
                        'amount' => [
                            'value' => $item->orderItem->sell_cost,
                            'currency' => 'RUB'
                        ],
                        'vat_code' => 1,
                        'payment_mode' => 'full_payment',
                        'payment_subject' => 'commodity',

                        'mark_mode' => 0,
                        'mark_code_info' => [
                            'gs_1m' => $sign,
                        ],

                    ];
                }
            } else {
                $items[] = [
                    'description' => $item->orderItem->product->name,
                    'quantity' => $item->quantity,
                    'amount' => [
                        'value' => $item->orderItem->sell_cost,
                        'currency' => 'RUB'
                    ],
                    'vat_code' => 1,
                    'payment_mode' => 'full_payment',
                    'payment_subject' => 'commodity',
                ];
            }

        }
        foreach ($expense->additions as $addition) {
            $items[] = [
                'description' => $addition->orderAddition->addition->name,
                'quantity' => $addition->orderAddition->quantity,
                'amount' => [
                    'value' => $addition->amount,
                    'currency' => 'RUB'
                ],
                'vat_code' => 1,
                'payment_mode' => 'full_payment',
                'payment_subject' => 'commodity',
            ];
        }
        return $items;
    }

    public function checkPayment(string $paymentId): string
    {
        $client = new Client();
        $client->setAuth(config('shop.yookassa-id'), config('shop.yookassa-key'));
        $payment = $client->getPaymentInfo($paymentId);
        return $payment->getStatus();
        //if ($payment['status'] == 'succeeded') return true;
        //return false;
    }

    public function cancelPayment(string $paymentId): bool
    {
        $client = new Client();
        $client->setAuth(config('shop.yookassa-id'), config('shop.yookassa-key'));
        $payment = $client->cancelPayment($paymentId, uniqid('', true));
        if ($payment['status'] == 'canceled') return true;
        return false;
    }

    #[Deprecated]
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

    #[Deprecated]
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
                    'inn' => '6321341814',
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

    #[Deprecated]
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

}
