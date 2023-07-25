<?php

namespace Tests\Feature;

use Tests\TestCase;

class APICreateNewAccountTest extends TestCase
{
    /**
     * A test that fails when you search for a customer and the customer doe not exist.
     */
    public function test_Cant_find_customer_when_creating_a_new_account(): void
    {
        $customer_id = -1;
        $account_type = "Savings";
        $balance = 3000;
        $available_balance = 3000;
        $response = $this->post(route('createNewUserAccount', [
            'customer_id' => $customer_id,
            'account_type' => $account_type,
            'balance' => $balance,
            'available_balance' => $available_balance
        ]));
        $response->assertStatus(400);
        $response->assertContent('"Can not find customer id # ' . $customer_id .'"');
    }

    /**
     * A test successful when you create a new account.
     */
    public function test_create_a_new_account(): void
    {
        $customer_id = 3;
        $account_type = 1;
        $balance = 3000;
        $available_balance = 3000;
        $response = $this->post(route('createNewUserAccount', [
            'customer_id' => $customer_id,
            'account_type' => $account_type,
            'balance' => $balance,
            'available_balance' => $available_balance
        ]));
        $response->assertOk();
        $response->assertContent('"new account created successfully"');
    }

    /**
     * A test that fails when you create a new account and the customer id is missing.
     */
    public function test_customer_id_missing_error_when_creating_a_new_account(): void
    {
        $customer_id = null;
        $account_type = "Savings";
        $balance = 3000;
        $available_balance = 3000;
        $response = $this->post(route('createNewUserAccount', [
            'customer_id' => $customer_id,
            'account_type' => $account_type,
            'balance' => $balance,
            'available_balance' => $available_balance
        ]));
        $response->assertStatus(400);
        $response->assertContent('"{\"\":[\"The customer id is required\"],\"0\":[\"The 0 field is required.\"]}"');
    }

    /**
     * A test that fails when you create a new account and the account type is missing.
     */
    public function test_account_type_missing_error_when_creating_a_new_account(): void
    {
        $customer_id = 3;
        $account_type = "";
        $balance = 3000;
        $available_balance = 3000;
        $response = $this->post(route('createNewUserAccount', [
            'customer_id' => $customer_id,
            'account_type' => $account_type,
            'balance' => $balance,
            'available_balance' => $available_balance
        ]));
        $response->assertStatus(400);
        $response->assertContent('"The account type is required"');
    }

    /**
     * A test that fails when you create a new account and the balance is missing.
     */
    public function test_balance_missing_error_when_creating_a_new_account(): void
    {
        $customer_id = 3;
        $account_type = "Savings";
        $balance = null;
        $available_balance = 3000;
        $response = $this->post(route('createNewUserAccount', [
            'customer_id' => $customer_id,
            'account_type' => $account_type,
            'balance' => $balance,
            'available_balance' => $available_balance
        ]));
        $response->assertStatus(400);
        $response->assertContent('"{\"\":[\"The balance is required\"],\"1\":[\"The 1 field is required.\"]}"');
    }

    /**
     * A test that fails when you create a new account and the available balance is missing.
     */
    public function test_available_balance_missing_error_when_creating_a_new_account(): void
    {
        $customer_id = 3;
        $account_type = "Savings";
        $balance = 3000;
        $available_balance = null;
        $response = $this->post(route('createNewUserAccount', [
            'customer_id' => $customer_id,
            'account_type' => $account_type,
            'balance' => $balance,
            'available_balance' => $available_balance
        ]));
        $response->assertStatus(400);
        $response->assertContent('"{\"\":[\"The available balance is required\"]}"');
    }
}
