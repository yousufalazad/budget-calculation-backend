<?php

namespace App\Http\Controllers;

use App\Models\Month;
use Illuminate\Http\Request;

class MonthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:15|unique:months,name',
        ]);

        try {
            $month = Month::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Month created successfully.',
                'data' => $month
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Month $month)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Month $month)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:15|unique:months,name,' . $id,
        ]);

        try {
            $month = Month::findOrFail($id);
            $month->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Month updated successfully.',
                'data' => $month
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $month = Month::findOrFail($id);
            $month->delete();

            return response()->json([
                'message' => 'Month deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }
}
