<?php

namespace Tests\Feature;

use Tests\TestCase;

class APIRetrieveBalanceTest extends TestCase
{
    /**
     * A test that fails when you search for account balances and the account does not exist.
     */
    public function test_Cant_find_accout_when_retrieve_balances_for_a_given_account(): void
    {
        $id = -1;
        $response = $this->get(route('getBalancesForAccount', ['id' => $id]));
        $response->assertOk();
        $response->assertContent('Can not find account id # ' . $id);
    }

    /**
     * A test that is successful when you search for account balances.
     */
    public function test_retrieve_balances_for_a_given_account(): void
    {
        $id = 3;
        $response = $this->get(route('getBalancesForAccount', ['id' => $id]));
        $response->assertOk();
        $response->assertJsonIsObject();
    }

    /**
     * A test that fails when you search for account balances and the account is null/empty.
     */
    public function test_id_missing_error_retrieve_balances_for_a_given_account(): void
    {
        $id = 0;
        $response = $this->get(route('getBalancesForAccount',['id' => $id]));
        $response->assertOk();
        $response->assertContent('Id field is required');
    }
}
