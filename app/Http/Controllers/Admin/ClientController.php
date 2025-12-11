<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Lead;
use App\DataTables\ClientDataTable;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Services\ClientService;
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
        return $dataTable->render('admin.clients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $leads = Lead::where('stage', 'qualified')
            ->whereNull('converted_to_client_id')
            ->get();
        return view('admin.clients.create', compact('leads'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully.',
                'redirect' => route('admin.clients.index')
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

        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $leads = Lead::where('stage', 'qualified')
            ->where(function($q) use ($client) {
                $q->whereNull('converted_to_client_id')
                  ->orWhere('converted_to_client_id', $client->id);
            })
            ->get();
        return view('admin.clients.edit', compact('client', 'leads'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        try {
            $this->clientService->update($client, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully.',
                'redirect' => route('admin.clients.show', $client)
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
