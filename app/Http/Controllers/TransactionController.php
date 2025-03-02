<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Month;
use App\Models\RecurringType;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'account', 'recurringType'])->get();
        return response()->json($transactions);
    }
    public function create()
    {
       
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'account_id' => 'required|exists:accounts,id',
            'title' => 'required|string',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'recurring_type_id' => 'nullable|exists:recurring_types,id',
            'start_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $transaction = Transaction::create($validatedData);
        return response()->json($transaction, Response::HTTP_CREATED);
    }
        
    public function show(Transaction $transaction)
    {
        return response()->json($transaction);
    }
    public function edit(Transaction $transaction)
    {
        
    }
    public function update(Request $request, Transaction $transaction)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'account_id' => 'required|exists:accounts,id',
            'title' => 'required|string',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'recurring_type_id' => 'nullable|exists:recurring_types,id',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $transaction->update($validatedData);
        return response()->json($transaction);
    }
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
