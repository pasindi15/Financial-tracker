<?php

namespace App\Http\Controllers\Api;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BudgetController extends Controller
{
    public function index()
    {
        return response()->json(Budget::where('user_id', auth()->id())->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0',
            'month'       => 'required|integer|between:1,12',
            'year'        => 'required|integer|min:2020',
        ]);
        $data['user_id'] = auth()->id();
        return response()->json(Budget::create($data), 201);
    }

    public function update(Request $request, Budget $budget)
    {
        $budget->update($request->only(['category_id', 'amount', 'month', 'year']));
        return response()->json($budget);
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
