<?php

namespace App\Http\Controllers;

use App\Models\Size;
use App\Models\Pack;
use App\Models\PackInformation;
use App\Models\Color;
use App\Models\SizeGroup;
use App\Models\PurchaseOrder;
use App\Models\AddOn;
use App\Models\Ratio;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{
    public function dashboard(){
        $title = "Dashboard";
        return view('dashboard', compact( 'title'));
    }

    // Show form with packs
    public function create(){
        $title = "New Order";
    
        // Eager load relationships
        $sizeGroups = SizeGroup::with('packs.sizes')->get();
        $addOns = AddOn::all();

    
        // Convert to JS-friendly structure
        $simulatedDB = [
            'sizeGroups' => $sizeGroups->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name,
            ]),
            'sizes' => $sizeGroups->flatMap(function ($group) {
                return $group->packs->flatMap(function ($pack) use ($group) {
                    return $pack->sizes->map(fn($size) => [
                        'id' => $size->id,
                        'name' => $size->size_name,
                        'size' => $size->size_name,
                        'group' => $group->name,
                    ]);
                });
            }),
            'packs' => $sizeGroups->flatMap(function ($group) {
                return $group->packs->map(function ($pack) use ($group) {
                    return [
                        'id' => $pack->id,
                        'name' => $pack->name,
                        'group' => $group->id,
                        'ratios' => $pack->sizes->pluck('ratio', 'id'),
                    ];
                });
            }),
        ];
    
        return view('form.cutting_order', compact('title', 'simulatedDB', 'addOns'));
    }

    // Store order
    public function store(Request $request){
        DB::beginTransaction();
            // Manually set the attributes
            $order = new PurchaseOrder();
            $order->job_no = $request->job_no;
            $order->style_no = $request->style_no;
            $order->po_date = $request->po_date;
            $order->ship_date = $request->ship_date;
            $order->fabrics = $request->fabrics;
            $order->gsm = $request->gsm;
            $order->buyer = $request->buyer;
            $order->order_qty = $request->order_qty;
            $order->title = $request->title;
            $order->description = $request->description;
            $order->po_label = $request->po_label;
            $order->care_label = $request->care_label;
            if ($request->hasFile('file')) {
                $order->file_path = $request->file('file')->store('uploads', 'public');
            }
            $order->final_total = $request->finalTotal;
            $order->save();

            // Loop through the packs from the request
            foreach ($request->packs as $packData) {
                // Create a new instance of PackInformation
                $packInfo = new PackInformation();
                $packInfo->pack_id = $packData['pack_id'];
                $packInfo->purchase_order_id = $order->id;
                
                // Use the relationship to link and save the child model
                $order->packInformation()->save($packInfo);

                // Loop through colors inside each pack
                foreach ($packData['colors'] as $colorData) {
                    // Create a new instance of Color
                    $color = new Color();
                    $color->color_name = $colorData['color_name'];
                    $color->qty = $colorData['qty'];
                    $color->extra_usage_qty = $colorData['extra_usage'] ?? 0; // Using extra_usage_qty
                    $color->add_on_id = $colorData['add_on'] ?? null; // Associate AddOn if provided
                    
                    // Use the relationship to link and save the child model
                    $packInfo->colors()->save($color);

                    // Loop ratios for each color
                    if (isset($colorData['ratios'])) {
                        foreach ($colorData['ratios'] as $sizeId => $ratioQty) {
                            // Create a new instance of Ratio
                            $ratio = new Ratio();
                            $ratio->color_id = $color->id;
                            $ratio->size_id = $sizeId;
                            $ratio->qty = $ratioQty;

                            // Use the relationship to link and save the child model
                            $color->ratios()->save($ratio);
                        }
                    }
                }
            }

            // Commit the transaction if all database operations are successful
            DB::commit();

            $latestOrder = PurchaseOrder::latest('id')->first();
            return redirect()->route('orders.pdf', $latestOrder->id)->with('success', 'Order created successfully.');
    }

    // Generate PDF
    public function downloadPdf($id){
        $purchaseOrder = PurchaseOrder::with([
            'packInformation.colors.ratios.size',
            'packInformation.colors.addOn',
            'packInformation.pack.sizes',
        ])->findOrFail($id);

        // Group by sizeGroup_id + color_name
        $grouped = $purchaseOrder->packInformation->groupBy(function ($pi) {
            $sizeGroupId = $pi->pack->sizeGroup_id ?? 'no_group';
            $colorName   = $pi->colors->first()->color_name ?? 'no_color';
            return $sizeGroupId . '_' . $colorName;

        });

        $pdf = PDF::loadView('orders.pdf', [
            'purchaseOrder' => $purchaseOrder,
            'grouped'       => $grouped,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('order_' . $purchaseOrder->job_no . '.pdf');
    }

    // Show all orders
    public function index(){
        $title = "Orders List";
        $orders = PurchaseOrder::all()
        ->whereNull('deleted_at');

        // Calculate Final Total for each order
        // foreach ($orders as $order) {
        //     $extrasTotal = $order->extras->sum('value'); // sum all extra values
        //     $order->final_total = $order->order_qty + $extrasTotal; // dynamic property
        // }
        return view('orders.index', compact('orders', 'title'));
    }

    public function edit($id) {
        $title = "Edit Order";
        $order = PurchaseOrder::with(['packInformation.pack.sizes', 'packInformation.colors.addOn'])->findOrFail($id);
        $sizeGroups = SizeGroup::with('packs.sizes')->get();
        $addOns = AddOn::all();

        $simulatedDB = [
            'sizeGroups' => $sizeGroups->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name,
            ]),
            'sizes' => $sizeGroups->flatMap(function ($group) {
                return $group->packs->flatMap(function ($pack) use ($group) {
                    return $pack->sizes->map(fn($size) => [
                        'id' => $size->id,
                        'name' => $size->size_name,
                        'size' => $size->size_name,
                        'group' => $group->name,
                    ]);
                });
            }),
            'packs' => $sizeGroups->flatMap(function ($group) {
                return $group->packs->map(function ($pack) use ($group) {
                    return [
                        'id' => $pack->id,
                        'name' => $pack->name,
                        'group' => $group->id,
                        'ratios' => $pack->sizes->pluck('ratio', 'id'),
                    ];
                });
            }),
        ];

        // Add the existing order's pack data to a new variable for JavaScript
        $existingPacks = $order->packInformation->map(function ($packInfo) {
            return [
                'id' => $packInfo->pack_id,
                'sizeGroup_id' => $packInfo->sizeGroup_id,
                'pack' => [
                    'name' => $packInfo->pack->name,
                    'ratios' => $packInfo->pack->sizes->pluck('ratio', 'id'),
                ],
                'colors' => $packInfo->colors->map(function ($color) {
                    return [
                        'color_name' => $color->color_name,
                        'qty' => $color->qty,
                        'extra_usage' => $color->extra_usage_qty,
                        'add_on' => $color->add_on_id,
                        'ratios' => json_decode($color->ratios, true), // Assuming ratios are stored as JSON
                    ];
                }),
            ];
        });

        return view('form.cutting_order', compact('order', 'title', 'sizeGroups', 'simulatedDB', 'addOns', 'existingPacks'));
    }

    public function update(Request $request, $id){
        DB::beginTransaction();

        // Find the existing purchase order
        $order = PurchaseOrder::findOrFail($id);

        // Update attributes
        $order->job_no = $request->job_no;
        $order->style_no = $request->style_no;
        $order->po_date = $request->po_date;
        $order->ship_date = $request->ship_date;
        $order->fabrics = $request->fabrics;
        $order->gsm = $request->gsm;
        $order->buyer = $request->buyer;
        $order->order_qty = $request->order_qty;
        $order->title = $request->title;
        $order->description = $request->description;
        $order->po_label = $request->po_label;
        $order->care_label = $request->care_label;

        if ($request->hasFile('file')) {
            $order->file_path = $request->file('file')->store('uploads', 'public');
        }

        $order->final_total = $request->finalTotal;
        $order->save();

        // Remove existing related data (packs, colors, ratios) before re-inserting
        $order->packInformation()->each(function ($packInfo) {
            $packInfo->colors()->each(function ($color) {
                $color->ratios()->delete(); // delete ratios
            });
            $packInfo->colors()->delete(); // delete colors
        });
        $order->packInformation()->delete(); // delete packs

        // Re-insert updated packs/colors/ratios from request
        foreach ($request->packs as $packData) {
            $packInfo = new PackInformation();
            $packInfo->pack_id = $packData['pack_id'];
            $packInfo->purchase_order_id = $order->id;
            $order->packInformation()->save($packInfo);

            foreach ($packData['colors'] as $colorData) {
                $color = new Color();
                $color->color_name = $colorData['color_name'];
                $color->qty = $colorData['qty'];
                $color->extra_usage_qty = $colorData['extra_usage'] ?? 0;
                $color->add_on_id = $colorData['add_on'] ?? null;
                $packInfo->colors()->save($color);

                if (isset($colorData['ratios'])) {
                    foreach ($colorData['ratios'] as $sizeId => $ratioQty) {
                        $ratio = new Ratio();
                        $ratio->color_id = $color->id;
                        $ratio->size_id = $sizeId;
                        $ratio->qty = $ratioQty;
                        $color->ratios()->save($ratio);
                    }
                }
            }
        }

        DB::commit();

        return redirect()->route('orders.pdf', $order->id)->with('success', 'Order updated successfully.');
    }


    // Soft delete
    public function destroy($id){
        $order = PurchaseOrder::findOrFail($id);
        $order->delete(); // will mark deleted_at
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }

    public function setting(){
        // fetch the first (and only) settings record
        $title = "Settings";
        $setting = Setting::first();
        return view('setting.edit', compact('setting', 'title'));
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $setting = Setting::first(); // always get the first settings row
    
        if (!$setting) {
            $setting = new Setting();
        }
    
        if ($request->hasFile('logo')) {
            // delete old logo if exists
            if ($setting->logo_path && Storage::exists('public/' . $setting->logo_path)) {
                Storage::delete('public/' . $setting->logo_path);
            }
        
            // store new logo
            $path = $request->file('logo')->store('logos', 'public');
            $setting->logo_path = $path;
        }
    
        $setting->save();
    
        return redirect()->route('settings')->with('success', 'Settings updated successfully.');
    }
}
