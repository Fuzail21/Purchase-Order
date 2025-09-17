<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use Illuminate\Http\Request;

class PackController extends Controller
{
    public function index()
    {
        $title = "Packs";
        $packs = Pack::latest()->paginate(10);
        return view('packs.index', compact('packs', 'title'));
    }

    public function store(Request $request)
    {

        $pack = new Pack();
        $pack->name = $request->name;
        $pack->save();

        return redirect()->route('packs.index')->with('success', 'Pack added successfully.');
    }

    public function destroy($id)
    {
        $pack = Pack::findOrFail($id);
        $pack->delete();
        return redirect()->route('packs.index')->with('success', 'Pack deleted successfully.');
    }
}

