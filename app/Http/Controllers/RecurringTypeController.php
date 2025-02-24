<?php

namespace App\Http\Controllers;

use App\Models\RecurringType;
use Illuminate\Http\Request;

class RecurringTypeController extends Controller
{
    /**
     * Display a listing of the recurring types.
     */
    public function index()
    {
        try {
            
            $recurringTypes = RecurringType::all();

            return response()->json([
                'message' => 'Recurring types fetched successfully.',
                'data' => $recurringTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Store a newly created recurring type.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:55|unique:recurring_types,name',
            'is_active' => 'required|boolean'
        ]);

        try {
            $recurringType = RecurringType::create([
                'name' => $request->name,
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'message' => 'Recurring type created successfully.',
                'data' => $recurringType
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the specified recurring type.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:30|unique:recurring_types,name,' . $id,
            'is_active' => 'required|boolean'
        ]);

        try {
            $recurringType = RecurringType::findOrFail($id);
            $recurringType->update([
                'name' => $request->name,
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'message' => 'Recurring type updated successfully.',
                'data' => $recurringType
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified recurring type.
     */
    public function destroy($id)
    {
        try {
            $recurringType = RecurringType::findOrFail($id);
            $recurringType->delete();

            return response()->json([
                'message' => 'Recurring type deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }
}
