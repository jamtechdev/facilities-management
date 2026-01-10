<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Client;
use App\DataTables\StaffDataTable;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Services\StaffService;
use App\Helpers\RouteHelper;
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
        $clients = Client::where('is_active', true)->orderBy('company_name')->get();
        return view('superadmin.staff.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $clientId = $data['client_id'] ?? null;
            unset($data['client_id']);

            $staff = $this->staffService->create($data);

            // Assign to client if provided
            if ($clientId) {
                $this->staffService->assignToClient($staff, $clientId, [
                    'assigned_weekly_hours' => $data['assigned_weekly_hours'] ?? 0,
                    'assigned_monthly_hours' => $data['assigned_monthly_hours'] ?? 0,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully.',
                'redirect' => RouteHelper::url('staff.index')
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
        $clients = Client::where('is_active', true)->orderBy('company_name')->get();
        return view('superadmin.staff.edit', compact('staff', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffRequest $request, Staff $staff): JsonResponse
    {
        try {
            $data = $request->validated();
            $clientId = $data['client_id'] ?? null;
            unset($data['client_id']);

            $this->staffService->update($staff, $data);

            // Handle client assignment
            if ($clientId) {
                $this->staffService->assignToClient($staff, $clientId, [
                    'assigned_weekly_hours' => $data['assigned_weekly_hours'] ?? 0,
                    'assigned_monthly_hours' => $data['assigned_monthly_hours'] ?? 0,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully.',
                'redirect' => RouteHelper::url('staff.index')
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
