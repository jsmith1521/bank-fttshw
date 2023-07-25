<?php

namespace Tests\Feature;

use Tests\TestCase;

class APITransferAmountTest extends TestCase
{
    /**
     * A test that fails when customoer id from does not exist.
     */
    public function test_Cant_find_customer_id_from_when_when_transfering_funds_from_one_account_to_another(): void
    {
        $amount = 300;
        $customer_id_from = -1;
        $account_id_from = 7;
        $customer_id_to = 1;
        $account_id_to = 1;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"Can not find customer id # ' . $customer_id_from .'"');
    }

    /**
     * A test that fails when customoer id to does not exist.
     */
    public function test_Cant_find_customer_id_to_when_when_transfering_funds_from_one_account_to_another(): void
    {
        $amount = 300;
        $customer_id_from = 4;
        $account_id_from = 7;
        $customer_id_to = -1;
        $account_id_to = 1;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"Can not find customer id # ' . $customer_id_to .'"');
    }

    /**
     * A test that is successful when you transfer money from one account to another.
     */
    public function test_transfer_funds_from_one_account_to_another(): void
    {
        $amount = 300;
        $customer_id_from = 4;
        $account_id_from = 7;
        $customer_id_to = 1;
        $account_id_to = 1;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertOk();
        $response->assertContent('"Transfer transaction made successfully"');
    }

    /**
     * A test that fails when the amount is null/blank
     */
    public function test_amount_missing_error_when_creating_a_new_account(): void
    {
        $amount = null;
        $customer_id_from = 4;
        $account_id_from = 7;
        $customer_id_to = 1;
        $account_id_to = 1;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"{\"\":[\"The amount is required\"]}"');
    }

    /**
     * A test that fails when the customer id from is null/blank.
     */
    public function test_customer_id_from_missing_error_when_creating_a_new_account(): void
    {
        $amount = 300;
        $customer_id_from = null;
        $account_id_from = 7;
        $customer_id_to = 1;
        $account_id_to = 1;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"{\"\":[\"The customer Id from is required\"],\"1\":[\"The account Id to is required\"]}"');
    }

    /**
     * A test that fails when the account id from is null/blank.
     */
    public function test_account_id_from_error_when_creating_a_new_account(): void
    {
        $amount = 300;
        $customer_id_from = 4;
        $account_id_from = null;
        $customer_id_to = 1;
        $account_id_to = 1;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"{\"\":[\"The account Id from is required\"],\"2\":[\"The 2 field is required.\"]}"');
    }

    /**
     * A test that fails when customer id to is null/blank.
     */
    public function test_customer_id_to_missing_error_when_creating_a_new_account(): void
    {
        $amount = 300;
        $customer_id_from = 4;
        $account_id_from = 7;
        $customer_id_to = null;
        $account_id_to = 1;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"{\"\":[\"The customer Id to is required\"],\"0\":[\"The 0 field is required.\"]}"');
    }

    /**
     * A test that fails when the account id to is null/blank
     */
    public function test_account_id_to_missing_error_when_creating_a_new_account(): void
    {
        $amount = 300;
        $customer_id_from = 4;
        $account_id_from = 7;
        $customer_id_to = 1;
        $account_id_to = null;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"{\"\":[\"The account Id to is required\"],\"3\":[\"The 3 field is required.\"]}"');
    }

    /**
     * A test that fails when the customer id to and account id to are not linked.
     */
    public function test_when_the_customer_id_to_and_account_id_to_are_not_linked(): void
    {
        $amount = 300;
        $customer_id_from = 4;
        $account_id_from = 7;
        $customer_id_to = 1;
        $account_id_to = 2;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"Can not find account id # ' . $account_id_to . ' with customer id # ' . $customer_id_to . '"');
    }

    /**
     * A test that fails when the customer id from and account id from are not linked.
     */
    public function test_when_the_customer_id_from_and_account_id_from_are_not_linked(): void
    {
        $amount = 300;
        $customer_id_from = 3;
        $account_id_from = 7;
        $customer_id_to = 1;
        $account_id_to = 1;
        $response = $this->post(route('transferAmount', [
            'amount' => $amount,
            'customer_id_from' => $customer_id_from,
            'account_id_from' => $account_id_from,
            'customer_id_to' => $customer_id_to,
            'account_id_to' => $account_id_to
        ]));
        $response->assertStatus(400);
        $response->assertContent('"Can not find account id # ' . $account_id_from . ' with customer id # ' . $customer_id_from . '"');
    }
}
