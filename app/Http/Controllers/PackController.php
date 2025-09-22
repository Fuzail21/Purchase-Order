<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\SizeGroup;
use App\Models\Size;
use Illuminate\Http\Request;

class PackController extends Controller
{
    public function index(){
        $title = "Packs";
        $packs = Pack::latest()->paginate(10);
        return view('packs.index', compact('packs', 'title'));
    }

    public function create(){
        $title = "Add New Pack";
        $sizeGroups = SizeGroup::all();
        $packs = Pack::with('sizes', 'sizeGroup')->latest()->paginate(10);
        return view('packs.create', compact('packs', 'title', 'sizeGroups'));
    }

    public function store(Request $request){
        // Save the new Pack record
        $pack = new Pack();
        $pack->name = $request->name;
        $pack->sizeGroup_id = $request->size_group_id;
        $pack->save();

        // Loop through the array of ratios and save each Size record
        foreach ($request->ratios as $ratio) {
            $size = new Size();
            $size->pack_id = $pack->id;
            $size->size_name = $ratio['size_name'];
            $size->ratio = $ratio['ratio'];
            $size->save();
        }

        return redirect()->route('packs.index')->with('success', 'Pack added successfully.');
    }

    public function edit($id){
        $pack = Pack::with('sizes', 'sizeGroup')->findOrFail($id);
        $title = "Edit Pack";
        $sizeGroups = SizeGroup::all();
        return view('packs.create', compact('pack', 'title', 'sizeGroups'));
    }

    public function update(Request $request, $id){
        $pack = Pack::findOrFail($id);

        // Update the main Pack details
        $pack->name = $request->name;
        $pack->sizeGroup_id = $request->size_group_id;
        $pack->save();

        // Get the IDs of the sizes in the submitted form data
        $submittedSizeIds = collect($request->input('ratios'))->pluck('id')->filter()->all();

        // Delete sizes that were removed from the form
        $pack->sizes()->whereNotIn('id', $submittedSizeIds)->delete();

        // Update or create new sizes
        if ($request->ratios) {
            foreach ($request->ratios as $ratio) {
                // If the ratio has an ID, it's an existing size to be updated
                if (isset($ratio['id'])) {
                    $size = $pack->sizes()->find($ratio['id']);
                    if ($size) {
                        $size->size_name = $ratio['size_name'];
                        $size->ratio = $ratio['ratio'];
                        $size->save();
                    }
                } else {
                    // If no ID, it's a new size to be created
                    $size = new Size();
                    $size->pack_id = $pack->id;
                    $size->size_name = $ratio['size_name'];
                    $size->ratio = $ratio['ratio'];
                    $size->save();
                }
            }
        }

        return redirect()->route('packs.index')->with('success', 'Pack updated successfully.');
    }

    public function destroy($id){
        $pack = Pack::findOrFail($id);

        // First, delete all the sizes associated with this pack.
        $pack->sizes()->delete();

        // Then, delete the pack itself.
        $pack->delete();

        return redirect()->route('packs.index')->with('success', 'Pack deleted successfully.');
    }
}

