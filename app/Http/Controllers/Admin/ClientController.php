<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use App\DataTables\ClientDataTable;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Services\ClientService;
use App\Helpers\RouteHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ClientDataTable $dataTable)
    {
        $viewPrefix = RouteHelper::getViewPrefix();
        return $dataTable->render($viewPrefix . '.clients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreClientRequest $request): JsonResponse
    // {
    //     try {
    //         $client = $this->clientService->create($request->validated());

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Client created successfully.',
    //             'redirect' => RouteHelper::url('clients.index')
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create client: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        try {

            $data = $request->validated();

            $data['is_active'] = $request->has('is_active');

            $client = $this->clientService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully.',
                'redirect' => RouteHelper::url('clients.index')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create client: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // Check permission to view client details
        if (!auth()->user()->can('view client details')) {
            abort(403, 'You do not have permission to view client details.');
        }
        $client->load([
            'user',
            'lead',
            'staff',
            'timesheets.staff',
            'jobPhotos.staff',
            'communications.user',
            'documents.uploadedBy',
            'feedback',
            'invoices'
        ]);

        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $viewPrefix = RouteHelper::getViewPrefix();
        return view($viewPrefix . '.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateClientRequest $request, Client $client): JsonResponse
    // {
    //     try {
    //         $this->clientService->update($client, $request->validated());

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Client updated successfully.',
    //             'redirect' => RouteHelper::url('clients.index')
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to update client: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        try {

            $data = $request->validated();

            $data['is_active'] = $request->has('is_active');

            $this->clientService->update($client, $data);

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully.',
                'redirect' => RouteHelper::url('clients.index')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update client: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): JsonResponse
    {
        try {
            $this->clientService->delete($client);

            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete client: ' . $e->getMessage()
            ], 500);
        }
    }
}
