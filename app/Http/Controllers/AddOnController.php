<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddOn;

class AddOnController extends Controller
{
    public function index()
    {
        $title = "Add-Ons List";
        $addOns = AddOn::latest()->paginate(10);
        return view('addOns.index', compact('addOns', 'title'));
    }

    public function store(Request $request)
    {

        $addOn = new AddOn();
        $addOn->name = $request->name;
        $addOn->save();

        return redirect()->route('addOn.index')->with('success', 'Add On added successfully.');
    }

    public function destroy($id)
    {
        $addOn = AddOn::findOrFail($id);

        // Finally, delete the size group itself
        $addOn->delete();

        return redirect()->route('addOn.index')->with('success', 'Add On deleted successfully.');
    }
}
