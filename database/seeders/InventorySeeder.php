<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Inventory;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $staff = Staff::all();

        $inventoryItems = [
            [
                'name' => 'Multi-Surface Cleaner',
                'description' => 'Professional grade all-purpose cleaner',
                'category' => 'chemicals',
                'quantity' => 50,
                'min_stock_level' => 10,
                'unit' => 'liter',
                'unit_cost' => 15.50,
                'status' => 'available',
            ],
            [
                'name' => 'Glass Cleaner',
                'description' => 'Streak-free glass cleaning solution',
                'category' => 'chemicals',
                'quantity' => 30,
                'min_stock_level' => 5,
                'unit' => 'liter',
                'unit_cost' => 12.00,
                'status' => 'available',
            ],
            [
                'name' => 'Microfiber Cloths',
                'description' => 'High-quality microfiber cleaning cloths',
                'category' => 'cloths',
                'quantity' => 200,
                'min_stock_level' => 50,
                'unit' => 'piece',
                'unit_cost' => 2.50,
                'status' => 'available',
            ],
            [
                'name' => 'Floor Mop',
                'description' => 'Professional floor mop with handle',
                'category' => 'mops',
                'quantity' => 25,
                'min_stock_level' => 5,
                'unit' => 'piece',
                'unit_cost' => 35.00,
                'status' => 'available',
            ],
            [
                'name' => 'Vacuum Cleaner',
                'description' => 'Commercial grade vacuum cleaner',
                'category' => 'machines',
                'quantity' => 8,
                'min_stock_level' => 2,
                'unit' => 'piece',
                'unit_cost' => 450.00,
                'status' => 'available',
            ],
            [
                'name' => 'Floor Buffer',
                'description' => 'Electric floor buffing machine',
                'category' => 'machines',
                'quantity' => 3,
                'min_stock_level' => 1,
                'unit' => 'piece',
                'unit_cost' => 850.00,
                'status' => 'available',
            ],
            [
                'name' => 'Disinfectant Spray',
                'description' => 'Hospital-grade disinfectant',
                'category' => 'chemicals',
                'quantity' => 40,
                'min_stock_level' => 10,
                'unit' => 'liter',
                'unit_cost' => 18.75,
                'status' => 'available',
            ],
            [
                'name' => 'Trash Bags',
                'description' => 'Heavy-duty trash bags',
                'category' => 'other',
                'quantity' => 500,
                'min_stock_level' => 100,
                'unit' => 'piece',
                'unit_cost' => 0.50,
                'status' => 'available',
            ],
        ];

        // Create available inventory items
        foreach ($inventoryItems as $item) {
            Inventory::create(array_merge($item, [
                'assigned_to_type' => null,
                'assigned_to_id' => null,
            ]));
        }

        // Assign some inventory to staff
        $staffItems = Inventory::where('category', 'machines')->take(3)->get();
        foreach ($staffItems as $index => $item) {
            if (isset($staff[$index])) {
                $item->update([
                    'assigned_to_type' => Staff::class,
                    'assigned_to_id' => $staff[$index]->id,
                    'status' => 'assigned',
                    'quantity' => 1,
                ]);
            }
        }

        // Assign some inventory to clients
        $clientItems = Inventory::where('category', 'chemicals')->take(2)->get();
        foreach ($clientItems as $index => $item) {
            if (isset($clients[$index])) {
                $assignedQty = rand(5, 15);
                Inventory::create([
                    'name' => $item->name,
                    'description' => $item->description,
                    'category' => $item->category,
                    'quantity' => $assignedQty,
                    'min_stock_level' => $item->min_stock_level,
                    'unit' => $item->unit,
                    'unit_cost' => $item->unit_cost,
                    'assigned_to_type' => Client::class,
                    'assigned_to_id' => $clients[$index]->id,
                    'status' => 'assigned',
                    'notes' => 'Assigned to ' . $clients[$index]->company_name,
                ]);
            }
        }

        $this->command->info('Inventory seeded successfully!');
    }
}

