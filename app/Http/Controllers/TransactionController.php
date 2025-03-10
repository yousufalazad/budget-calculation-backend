<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Month;
use App\Models\RecurringType;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        Log::info('Getting all transactions');
        $transactions = Transaction::with(['user', 'account', 'recurringType'])->get();
        return response()->json($transactions);
        
    }
    public function create()
    {
       
    }
    public function store(Request $request)
    {
        //$userId = Auth::user()->id;
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

        $transaction = Transaction::create([
            'user_id' => $validatedData['user_id'],
            'account_id' => $validatedData['account_id'],
            'title' => $validatedData['title'],
            'type' => $validatedData['type'],
            'amount' => $validatedData['amount'],
            'recurring_type_id' => $validatedData['recurring_type_id'],
            'start_date' => $validatedData['start_date'],
            'notes' => $validatedData['notes'],
            'is_active' => $validatedData['is_active']
        ]);
        
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

        $transaction->update([
            'user_id' => $validatedData['user_id'],
            'account_id' => $validatedData['account_id'],
            'title' => $validatedData['title'],
            'type' => $validatedData['type'],
            'amount' => $validatedData['amount'],
            'recurring_type_id' => $validatedData['recurring_type_id'],
            'start_date' => $validatedData['start_date'],
            'notes' => $validatedData['notes'],
            'is_active' => $validatedData['is_active']
        ]);
        return response()->json($transaction);
    }
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
