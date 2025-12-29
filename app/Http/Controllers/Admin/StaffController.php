<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\DataTables\StaffDataTable;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Services\StaffService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StaffController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(StaffDataTable $dataTable)
    {
        return $dataTable->render('superadmin.staff.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.staff.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request): JsonResponse
    {
        try {
            $staff = $this->staffService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully.',
                'redirect' => route('admin.staff.index')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        // Check permission to view staff details
        if (!auth()->user()->can('view staff details')) {
            abort(403, 'You do not have permission to view staff details.');
        }
        $staff->load([
            'user',
            'clients',
            'leads',
            'timesheets.client',
            'jobPhotos.client',
            'documents.uploadedBy'
        ]);

        return view('superadmin.staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        return view('superadmin.staff.edit', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffRequest $request, Staff $staff): JsonResponse
    {
        try {
            $this->staffService->update($staff, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully.',
                'redirect' => route('admin.staff.index')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff): JsonResponse
    {
        try {
            $this->staffService->delete($staff);

            return response()->json([
                'success' => true,
                'message' => 'Staff deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete staff: ' . $e->getMessage()
            ], 500);
        }
    }
}
