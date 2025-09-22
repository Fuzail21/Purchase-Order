<?php

namespace App\Http\Controllers;

use App\Models\SizeGroup;
use Illuminate\Http\Request;

class SizeGroupController extends Controller
{
    public function index()
    {
        $title = "Size Groups";
        $sizeGroup = SizeGroup::latest()->paginate(10);
        return view('sizeGroup.index', compact('sizeGroup', 'title'));
    }

    public function store(Request $request)
    {

        $sizeGroup = new SizeGroup();
        $sizeGroup->name = $request->name;
        $sizeGroup->save();

        return redirect()->route('size.index')->with('success', 'Size Group added successfully.');
    }

    public function destroy($id)
    {
        $sizeGroup = SizeGroup::findOrFail($id);

        // Find all packs associated with this size group
        $packsToDelete = $sizeGroup->packs;

        // Loop through each pack and delete it (this also deletes its sizes)
        foreach ($packsToDelete as $pack) {
            $pack->sizes()->delete();
            $pack->delete();
        }

        // Finally, delete the size group itself
        $sizeGroup->delete();

        return redirect()->route('size.index')->with('success', 'Size Group deleted successfully.');
    }
}
