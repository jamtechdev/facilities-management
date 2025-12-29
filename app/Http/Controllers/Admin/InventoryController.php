<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Staff;
use App\Models\Client;
use App\DataTables\InventoryDataTable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items
     */
    public function index(InventoryDataTable $dataTable)
    {
        return $dataTable->render('superadmin.inventory.index');
    }

    /**
     * Show the form for creating a new inventory item
     */
    public function create()
    {
        return view('superadmin.inventory.create');
    }

    /**
     * Display the specified inventory item
     */
    public function show(Inventory $inventory)
    {
        // Check permission to view inventory details
        if (!auth()->user()->can('view inventory')) {
            abort(403, 'You do not have permission to view inventory details.');
        }

        $inventory->load('assignedTo');
        return view('superadmin.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified inventory item
     */
    public function edit(Inventory $inventory)
    {
        return view('superadmin.inventory.edit', compact('inventory'));
    }

    /**
     * Store a newly created inventory item
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['status'] = Inventory::STATUS_AVAILABLE;

            $inventory = Inventory::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Inventory item created successfully.',
                'redirect' => route('admin.inventory.index')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an inventory item
     */
    public function update(Request $request, Inventory $inventory): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'quantity' => 'sometimes|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|in:available,assigned,used,returned',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $inventory->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Inventory item updated successfully.',
                'redirect' => route('admin.inventory.index')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign inventory to staff or client
     */
    public function assign(Request $request, Inventory $inventory): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'assigned_to_type' => 'required|string|in:App\Models\Staff,App\Models\Client',
            'assigned_to_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:' . $inventory->quantity,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify the assigned entity exists
            $assignedToType = $validator->validated()['assigned_to_type'];
            $assignedTo = $assignedToType::findOrFail($request->assigned_to_id);

            // Update inventory
            $inventory->update([
                'assigned_to_type' => $request->assigned_to_type,
                'assigned_to_id' => $request->assigned_to_id,
                'quantity' => $inventory->quantity - $request->quantity,
                'status' => Inventory::STATUS_ASSIGNED,
            ]);

            // Create a new inventory record for the assigned item
            Inventory::create([
                'name' => $inventory->name,
                'description' => $inventory->description,
                'category' => $inventory->category,
                'quantity' => $request->quantity,
                'min_stock_level' => $inventory->min_stock_level,
                'unit' => $inventory->unit,
                'unit_cost' => $inventory->unit_cost,
                'assigned_to_type' => $request->assigned_to_type,
                'assigned_to_id' => $request->assigned_to_id,
                'status' => Inventory::STATUS_ASSIGNED,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inventory item assigned successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified inventory item
     */
    public function destroy(Inventory $inventory): JsonResponse
    {
        try {
            $inventory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Inventory item deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete inventory item: ' . $e->getMessage()
            ], 500);
        }
    }
}
