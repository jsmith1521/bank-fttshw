<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccountController extends Controller
{
    /**
     *      
     * Create a new bank account for a customer, with an initial deposit amount.     
     * @param Request $request     
     * @return JsonResponse      
     */    
    public function createNewUserAccount(Request $request): JsonResponse
    {
        try{
            // set variables
            $customerId = $request->input('customer_id');
            $accountType = $request->input('account_type');
            $balance = $request->input('balance');
            $availableBalance = $request->input('available_balance');
            $createdAt = date("Y-m-d h:i:s"); 
            // validation checks
            $validated = $this->validateDataForNewAccount(
                $customerId, 
                $accountType, 
                $balance, 
                $availableBalance
            );
            if(is_string($validated)) {
                return Response::json($validated, 400);
            }
            // insert data
            $usersId = DB::table('accounts')->insertGetId(
                [
                    'customer_id' => $customerId,
                    'type' => $accountType,
                    'balance' => $balance,
                    'available_balance' => $availableBalance,
                    'active' => 'Active',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
            // add transaction to log
            DB::table('customer_histories')->insert(
                [
                    'customer_id' => $customerId,
                    'action' => 'Create a new ' . $accountType,
                    'amount' => $balance,
                    'from_account' => null,
                    'to_account' => $usersId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
            return Response::json('new account created successfully', 200);
        } catch(Exception $e) {
            return Response::json($e->getMessage(), 500);
        }
    }

    /**
     * Transfer an amount between two accounts.
     * @param Request $request
     * @return JsonResponse
     */
    public function transferAmount(Request $request): JsonResponse
    {
        try{
            // set variables
            $customerIdTo = $request->input('customer_id_to');
            $customerIdFrom = $request->input('customer_id_from');
            $accountIdTo = $request->input('account_id_to');
            $accountIdFrom = $request->input('account_id_from');
            $amount = $request->input('amount');
            $createdAt = date("Y-m-d h:i:s");
            // validation checks
            $validated = $this->validateData($customerIdTo, $customerIdFrom, $accountIdFrom, $accountIdTo, $amount);
            if(is_string($validated)) {
                return Response::json($validated, 400);
            }
            // insert data
            $rowUpdated1 = DB::table('accounts')
            ->where('id', $accountIdFrom)
            ->where('customer_id', $customerIdFrom)
            ->update(['balance' => $validated['newAccountFromBalance'], 'available_balance' => $validated['newAccountFromAvaliableBalance']]);
            
            $rowUpdated2 = DB::table('accounts')
            ->where('id', $accountIdTo)
            ->where('customer_id', $customerIdTo)
            ->update(['balance' => $validated['newAccountToBalance'], 'available_balance' => $validated['newAccountToAvaliableBalance']]);
            // add transaction to log
            if($rowUpdated1 === 1 && $rowUpdated2 === 1) {
                DB::table('customer_histories')->insert(
                    [
                        'customer_id' => $customerIdFrom,
                        'action' => 'Transfered money to customer id ' . $customerIdTo,
                        'amount' => $amount,
                        'from_account' => $accountIdFrom,
                        'to_account' => $accountIdTo,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
                return Response::json('Transfer transaction made successfully', 200);
            }
            return Response::json('somthing went wrong during the process', 500);
        } catch(Exception $e) {
            // return error if something goes wrong
            return Response::json($e->getMessage(), 500);
        }
    }

    /**
     * Display the balences of an account.
     * @param int $id
     * @return object|string
     */
    public function getBalancesForAccount(int $id): object|string
    {
        if(empty($id)){
            return 'Id field is required';
        }
        $accountBalances = DB::table('accounts')
        ->select('balance', 'available_balance')
        ->where('id', $id)
        ->first();
        if(!empty($accountBalances)){
            return $accountBalances;
        }
        return 'Can not find account id # ' . $id;
    }
    
     /**
     * Validate the data used to transfer funds from one account to another.
     * @param ?int $customerIdTo
     * @param ?int $customerIdFrom
     * @param ?int $accountIdFrom
     * @param ?int $accountIdTo
     * @param ?int $amount
     * @return array|string
     */
    private function validateData(
        ?int $customerIdTo,
        ?int $customerIdFrom,
        ?int $accountIdFrom,
        ?int $accountIdTo,
        ?int $amount
    ): array|string
    {
        // validate data passed is not empty
        $validate = Validator(
            [
                $customerIdTo, 
                $customerIdFrom,
                $accountIdFrom,
                $accountIdTo,
                $amount,
            ],
            [
                $customerIdTo => 'required',
                $customerIdFrom => 'required',
                $accountIdFrom => 'required',
                $accountIdTo => 'required',
                $amount => 'required',
            ],
            [
                $customerIdTo => 'The customer Id to is required',
                $customerIdFrom => 'The customer Id from is required',
                $accountIdFrom => 'The account Id from is required',
                $accountIdTo => 'The account Id to is required',
                $amount => 'The amount is required',
            ]
        );
        if($validate->fails()) {
            return $validate->errors();
        }
        // validate the customers exist
        $customerTo = $this->validateCustomerExist($customerIdTo);
        if(is_string($customerTo)){
            return $customerTo;
        }
        $customerFrom = $this->validateCustomerExist($customerIdFrom);
        if(is_string($customerFrom)){
            return $customerFrom;
        }
        // validate the accounts exist
        $accountFromBalances = $this->validateAccountExist($accountIdFrom);
        if(is_string($accountFromBalances)){
            return $accountFromBalances;
        }
        $accountToBalances = $this->validateAccountExist($accountIdTo);
        if(is_string($accountToBalances)){
            return $accountToBalances;
        }
        // validate that the customer is linked the the account, in question, and it exist
        $validateCustomerAndAccountExist1 = $this->validateCustomerAndAccountExist($accountIdTo, $customerIdTo);
        if(is_string($validateCustomerAndAccountExist1)){
            return $validateCustomerAndAccountExist1;
        }
        $validateCustomerAndAccountExist2 = $this->validateCustomerAndAccountExist($accountIdFrom, $customerIdFrom);
        if(is_string($validateCustomerAndAccountExist2)){
            return $validateCustomerAndAccountExist2;
        }
        // get the balances and verify if the amount is subtracted from the balance, the balance is NOT less then 0 (negitive number)
        $accountToBalances = $this->getBalancesForAccount($accountIdTo);
        $accountFromBalances = $this->getBalancesForAccount($accountIdFrom);
        $newAccountToBalance = $accountToBalances->balance + $amount;
        $newAccountToAvaliableBalance = $accountToBalances->available_balance + $amount;
        $newAccountFromBalance = $accountFromBalances->balance - $amount;
        $newAccountFromAvaliableBalance = $accountFromBalances->available_balance - $amount;
        if($newAccountFromBalance < 0) {
            return 'you dont have enought funds to make this transaction';
        }
        // if no errors, return array of data to use in the process.
        return [
            'customerTo' => $customerTo, 
            'customerFrom' => $customerFrom, 
            'accountFromBalances' => $accountFromBalances, 
            'accountToBalances' => $accountToBalances, 
            'newAccountFromBalance' => $newAccountFromBalance,
            'newAccountFromAvaliableBalance' => $newAccountFromAvaliableBalance, 
            'newAccountToBalance' => $newAccountToBalance, 
            'newAccountToAvaliableBalance' => $newAccountToAvaliableBalance
        ];
    }

    /**
     * Validate the data used to create a new account.
     * @param ?int $customerId
     * @param ?string $accountType
     * @param ?int $balance
     * @param ?int $availableBalance
     * @return Validator|string|array
     */
    private function validateDataForNewAccount(
        ?int $customerId, 
        ?string $accountType, 
        ?int $balance, 
        ?int $availableBalance
    ): Validator|string|array
    {
        // validate data passed is not empty
        $validate = Validator(
                [
                    $customerId, 
                    $balance,
                    $availableBalance,
                ],
                [
                    $customerId => 'required',
                    $balance => 'required',
                    $availableBalance => 'required',
                ],
                [
                    $customerId => 'The customer id is required',
                    $balance => 'The balance is required',
                    $availableBalance => 'The available balance is required',
                ]
        );
        if($validate->fails()) {
            return $validate->errors();
        }
        if(empty($accountType)) {
            return 'The account type is required';
        }
        // validate customer exist
        $doesCustomerExist = $this->validateCustomerExist($customerId);
        if(is_string($doesCustomerExist)) {
            return $doesCustomerExist;
        }
        // return empty array to continue the process.
        return [];
    }

    /**
     * validate a customer exist by id.
     * @param int $id
     * @return bool
     */
    private function validateCustomerExist(int $id): object|string
    {
        $customer = DB::table('customers')
        ->where('id', $id)
        ->first();
        // return the customer if the customer is found or a string if it is not found.
        return $customer ?? 'Can not find customer id # ' . $id;
    }

    /**
     * validate an account exist by id.
     * @param int $id
     * @return object|string
     */
    private function validateAccountExist(int $id): object|string
    {
        $account = DB::table('accounts')
        ->where('id', $id)
        ->first();
        // return the customer if the customer is found or a string if it is not found.
        return $account ?? 'Can not find account id # ' . $id;
    }

    /**
     * validate a customer and account exist by ids.
     * @param int $id
     * @param int $customerId
     * @return object|string
     */
    private function validateCustomerAndAccountExist(int $id, int $customerId): object|string
    {
        $account = DB::table('accounts')
        ->where('id', $id)
        ->where('customer_id', $customerId)
        ->first();
        // return the customer if the customer is found or a string if it is not found.
        return $account ?? 'Can not find account id # ' . $id . ' with customer id # '. $customerId;
    }
}
