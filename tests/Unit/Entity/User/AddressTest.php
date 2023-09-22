<?php


namespace Entity\User;


use App\Entity\Address;
use App\Entity\User\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use DatabaseTransactions;

    public function testAddress(): void
    {
        $user = User::new( 'email', '880000000');
        $address = new Address();
        $address->city = 'City';
        $address->index = 100100;
        $address->street = 'Street';
        $address->house = '99';
        $address->build = 'A';
        $address->flat = '999';
        $user->setAddress($address);
        $user->save();
        self::assertEquals($address->index, $user->address_index);
        self::assertEquals($address->index, $user->address->index);
        //TODO
    }
}
