<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pack;
use App\Models\PackInformation;
use App\Models\Color;
use App\Models\SizeGroup;
use App\Models\Ratio;
use App\Models\Extra;
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
        $packs = Pack::all();
        $sizeGroups = SizeGroup::all();
        return view('form.cutting_order', compact('packs', 'title', 'sizeGroups'));
    }

    // Store Order
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            // Save Order
            $order = new Order();
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

            // Save Colors
            foreach ($request->colors as $colorData) {
                $color = new Color();
                $color->order_id = $order->id;
                $color->color_name = $colorData['color_name'];
                $color->size_group_id = $colorData['size_group'] ?? 1;
                $color->save();

                // Save Packs
                foreach ($colorData['packs'] as $packData) {
                    $pack = new PackInformation();
                    $pack->color_id = $color->id;
                    $pack->pack_id = $packData['pack_name'];
                    $pack->pack_qty = $packData['pack_qty'];
                    $pack->pack_extra_percent = $packData['pack_extra_percent'] ?? 0;
                    $pack->pack_extra_qty = $packData['pack_extra_qty'] ?? 0;
                    $pack->save();

                    // Save Ratios
                    foreach ($packData['ratios'] as $ratioData) {
                        $ratio = new Ratio();
                        $ratio->packI_id = $pack->id;
                        $ratio->size_name = $ratioData['size_name'];
                        $ratio->ratio = $ratioData['ratio'];
                        $ratio->actual_qty = $ratioData['actual_qty'];
                        $ratio->save();
                    }
                }
            }

            // Save Overall Extras
            // if ($request->has('extras') && !empty($request->extras)) {
            //     foreach ($request->extras as $extraData) {
            //         if (!empty($extraData['name']) && !empty($extraData['percent'])) {
            //             $extra = new Extra(); // Changed model name
            //             $extra->order_id = $order->id;
            //             $extra->name = $extraData['name'];
            //             $extra->percent = $extraData['percent'];
            //             $extra->value = $extraData['value'] ?? 0;
            //             $extra->save();
            //         }
            //     }
            // }

        });
        $order = Order::findOrFail($id);
        return redirect()->route('orders.pdf', $order->id)->with('success', 'Order created successfully.');
    }


    // Generate PDF
    public function downloadPdf($id)
    {
        // Fetch the order with its related data.
        $order = Order::with('colors.packs.ratios')->findOrFail($id);
        
        // Load the view and set the paper size.
        $pdf = PDF::loadView('orders.pdf', compact('order'))->setPaper('a4', 'landscape');
        
        // Stream the PDF to the browser for download.
        // The filename is based on the order's job number.
        return $pdf->stream('order_' . $order->job_no . '.pdf');
    }

    // Show all orders
    public function index(){
        $title = "Orders List";
        $orders = Order::with(['colors', 'extras'])
        ->whereNull('deleted_at')
        ->latest()
        ->paginate(10);

        // Calculate Final Total for each order
        foreach ($orders as $order) {
            $extrasTotal = $order->extras->sum('value'); // sum all extra values
            $order->final_total = $order->order_qty + $extrasTotal; // dynamic property
        }
        return view('orders.index', compact('orders', 'title'));
    }

    public function edit($id){
        $title = "Edit Order";
        $order = Order::with(['colors','extras'])->findOrFail($id);
        $packs = Pack::all();
        $sizeGroups = SizeGroup::all();
        return view('form.cutting_order', compact('order','packs', 'title', 'sizeGroups')); // reuse same form
    }

    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            // Find Order
            $order = Order::findOrFail($id);

            // Update Order
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

            /** --------------------------
             * Colors, Packs, Ratios
             * ------------------------- */
            // Delete old relations (to replace with new input)
            Color::where('order_id', $order->id)->delete();

            foreach ($request->colors as $colorData) {
                $color = new Color();
                $color->order_id = $order->id;
                $color->color_name = $colorData['color_name'];
                $color->size_group_id = $colorData['size_group'] ?? 1;
                $color->save();

                // Packs
                foreach ($colorData['packs'] as $packData) {
                    $pack = new PackInformation();
                    $pack->color_id = $color->id;
                    $pack->pack_id = $packData['pack_name'];
                    $pack->pack_qty = $packData['pack_qty'];
                    $pack->pack_extra_percent = $packData['pack_extra_percent'] ?? 0;
                    $pack->pack_extra_qty = $packData['pack_extra_qty'] ?? 0;
                    $pack->save();

                    // Ratios
                    foreach ($packData['ratios'] as $ratioData) {
                        $ratio = new Ratio();
                        $ratio->packI_id = $pack->id;
                        $ratio->size_name = $ratioData['size_name'];
                        $ratio->ratio = $ratioData['ratio'];
                        $ratio->actual_qty = $ratioData['actual_qty'];
                        $ratio->save();
                    }
                }
            }

            /** --------------------------
             * Overall Extras
             * ------------------------- */
            // Extra::where('order_id', $order->id)->delete();

            // if ($request->has('extras') && !empty($request->extras)) {
            //     foreach ($request->extras as $extraData) {
            //         if (!empty($extraData['name']) && !empty($extraData['percent'])) {
            //             $extra = new Extra();
            //             $extra->order_id = $order->id;
            //             $extra->name = $extraData['name'];
            //             $extra->percent = $extraData['percent'];
            //             $extra->value = $extraData['value'] ?? 0;
            //             $extra->save();
            //         }
            //     }
            // }
            
        });
        $order = Order::findOrFail($id);
        return redirect()->route('orders.pdf', $order->id)->with('success', 'Order updated successfully.');
    }


    // Soft delete
    public function destroy($id){
        $order = Order::findOrFail($id);
        $order->delete(); // will mark deleted_at
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }


    public function setting()
    {
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

        $setting = Setting::firstOrNew(['id' => 1]);

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
