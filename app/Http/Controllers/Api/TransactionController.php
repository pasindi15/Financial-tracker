<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id());

        if ($request->type)       $query->where('type', $request->type);
        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->date_from)   $query->whereDate('date', '>=', $request->date_from);
        if ($request->date_to)     $query->whereDate('date', '<=', $request->date_to);

        return response()->json(
            $query->with('category')->orderBy('date', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0',
            'type'        => 'required|in:income,expense',
            'date'        => 'required|date',
            'description' => 'nullable|string',
        ]);
        $data['user_id'] = auth()->id();
        return response()->json(Transaction::create($data), 201);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $transaction->update($request->only(['category_id', 'amount', 'type', 'date', 'description']));
        return response()->json($transaction->load('category'));
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
