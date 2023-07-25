<?php

namespace Tests\Feature;

use Tests\TestCase;

class APITransferHistoryTest extends TestCase
{
    /**
     * A test that fails when you search for transfer history and the history does not exist.
     */
    public function test_Cant_find_transfer_history_for_a_given_account(): void
    {
        $id = -1;
        $response = $this->get(route('getTransferHistory', ['id' => $id]));
        $response->assertStatus(404);
        $response->assertContent('"No Records were found for account id # ' . $id .'"');
    }

    /**
     * A test that is successfull when you search for an account history.
     */
    public function test_retrieve_history_for_a_given_account(): void
    {
        $id = 3;
        $response = $this->get(route('getTransferHistory', ['id' => $id]));
        $response->assertOk();
        $response->assertJsonStructure([
            '*' => [
                    'id',
                    'customer_id',
                    'action',
                    'amount',
                    'from_account',
                    'to_account',
                    'created_at',
                    'updated_at',
            ]
        ]);
    }

    /**
     * A test that fails when you search for an account history and the id is empty/0.
     */
    public function test_id_missing_error_retrieve_account_history_for_a_given_account(): void
    {
        $id = 0;
        $response = $this->get(route('getTransferHistory',['id' => $id]));
        $response->assertStatus(404);
        $response->assertContent('"Id field is required"');
    }
}
