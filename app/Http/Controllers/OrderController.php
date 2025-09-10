<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pack;
use App\Models\OrderRatio;
use App\Models\OrderExtra;
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
        return view('form.cutting_order', compact('packs', 'title'));
    }

    // Save order with ratios & extras
    public function store(Request $request){
        // Create new order instance
        $order = new Order();
        $order->job_no     = $request->job_no;
        $order->style_no   = $request->style_no;
        $order->po_date    = $request->po_date;
        $order->ship_date  = $request->ship_date;
        $order->fabrics    = $request->fabrics;
        $order->gsm        = $request->gsm;
        $order->buyer      = $request->buyer;
        $order->order_qty  = $request->order_qty;
        // 🔹 New fields
        $order->title       = $request->title;
        $order->description = $request->description;
        $order->po_label    = $request->po_label;
        $order->care_label  = $request->care_label;

        if ($request->hasFile('file')) {
            $order->file_path = $request->file('file')->store('uploads', 'public');
        }

        $order->body_color = $request->body_color;
        $order->pack_id    = $request->pack_id;
        $order->save();

        // ---- HANDLE RATIOS WITH CALCULATION ----
        if ($request->ratios) {
            // Step 1: sum all ratios
            $totalRatio = collect($request->ratios)->sum('ratio');
            $finalTotal = (int) $request->finalTotal; // from form

            foreach ($request->ratios as $ratio) {
                $orderRatio = new OrderRatio();
                $orderRatio->order_id   = $order->id;
                $orderRatio->size_name  = $ratio['size_name'];
                $orderRatio->ratio      = $ratio['ratio'];
                $orderRatio->actual_qty = $ratio['actual_qty'] ?? 0;

                // Step 2: calculate proportional cutting qty
                if ($totalRatio > 0) {
                    $calculated = ($finalTotal / $totalRatio) * $ratio['ratio'];
                    $orderRatio->cutting_qty = floor($calculated);
                } else {
                    $orderRatio->cutting_qty = 0;
                }

                $orderRatio->save();
            }
        }

        // ---- HANDLE EXTRAS ----
        if ($request->extras) {
            foreach ($request->extras as $extra) {
                $orderExtra = new OrderExtra();
                $orderExtra->order_id = $order->id;
                $orderExtra->name     = $extra['name'];
                $orderExtra->percent  = $extra['percent'];
                $orderExtra->value    = $extra['value'] ?? 0;
                $orderExtra->save();
            }
        }

        return redirect()->route('orders.pdf', $order->id)->with('success', 'Order created successfully.');
    }

    // Generate PDF
    public function downloadPdf($id){
        $order = Order::with(['ratios', 'extras', 'pack'])->findOrFail($id);
        $pdf = Pdf::loadView('orders.pdf', compact('order'))->setPaper('a4', 'landscape');
        return $pdf->stream('order_'.$order->id.'.pdf');
    }

    // Show all orders
    public function index(){
        $title = "Orders List";
        $orders = Order::with(['pack', 'ratios', 'extras'])
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
        $order = Order::with(['ratios','extras','pack'])->findOrFail($id);
        $packs = Pack::all();
        return view('form.cutting_order', compact('order','packs', 'title')); // reuse same form
    }

    // Update order
    public function update(Request $request, $id){
        $order = Order::findOrFail($id);

        $order->job_no     = $request->job_no;
        $order->style_no   = $request->style_no;
        $order->po_date    = $request->po_date;
        $order->ship_date  = $request->ship_date;
        $order->fabrics    = $request->fabrics;
        $order->gsm        = $request->gsm;
        $order->buyer      = $request->buyer;
        $order->order_qty  = $request->order_qty;
        // 🔹 New fields
        $order->title       = $request->title;
        $order->description = $request->description;
        $order->po_label    = $request->po_label;
        $order->care_label  = $request->care_label;


        if ($request->hasFile('file')) {
            $order->file_path = $request->file('file')->store('uploads', 'public');
        }

        $order->body_color = $request->body_color;
        $order->pack_id    = $request->pack_id;
        $order->save();

        // Delete old ratios & extras first
        $order->ratios()->delete();
        $order->extras()->delete();

        // ---- HANDLE RATIOS ----
        if ($request->ratios) {
            $totalRatio = collect($request->ratios)->sum('ratio');
            $finalTotal = (int) $request->finalTotal;

            foreach ($request->ratios as $ratio) {
                $orderRatio = new OrderRatio();
                $orderRatio->order_id   = $order->id;
                $orderRatio->size_name  = $ratio['size_name'];
                $orderRatio->ratio      = $ratio['ratio'];
                $orderRatio->actual_qty = $ratio['actual_qty'] ?? 0;

                if ($totalRatio > 0) {
                    $calculated = ($finalTotal / $totalRatio) * $ratio['ratio'];
                    $orderRatio->cutting_qty = floor($calculated);
                } else {
                    $orderRatio->cutting_qty = 0;
                }

                $orderRatio->save();
            }
        }

        // ---- HANDLE EXTRAS ----
        if ($request->extras) {
            foreach ($request->extras as $extra) {
                $orderExtra = new OrderExtra();
                $orderExtra->order_id = $order->id;
                $orderExtra->name     = $extra['name'];
                $orderExtra->percent  = $extra['percent'];
                $orderExtra->value    = $extra['value'] ?? 0;
                $orderExtra->save();
            }
        }

        return redirect()->route('orders.pdf', $order->id)->with('success', 'Order created successfully.');
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
