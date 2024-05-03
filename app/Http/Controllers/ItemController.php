<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ItemController extends Controller
{
    public function index()
    {
        return view('items.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image1' => 'required|image',
            'image2' => 'required|image',
        ]);

        $item = new Item;
        $item->title = $request->title;
        $item->image1 = $request->file('image1')->store('images');
        $item->image2 = $request->file('image2')->store('images');
        $item->save();

        return response()->json(['success' => 'Item saved successfully.']);
    }

    public function show(Item $item)
    {
        return response()->json($item);
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'title' => 'sometimes|string',
            'image1' => 'sometimes|image',
            'image2' => 'sometimes|image',
        ]);

        if ($request->has('title')) {
            $item->title = $request->title;
        }


        if ($request->hasFile('image1')) {
            if ($item->image1 && Storage::exists($item->image1)) {
                Storage::delete($item->image1);
            }
            $item->image1 = $request->file('image1')->store('images');
        }


        if ($request->hasFile('image2')) {
            if ($item->image2 && Storage::exists($item->image2)) {
                Storage::delete($item->image2);
            }
            $item->image2 = $request->file('image2')->store('images');
        }

        $item->save();

        return response()->json(['success' => 'Item updated successfully.']);
    }


    public function destroy(Item $item)
    {
        $item->delete();

        return response()->json(['success' => 'Item deleted successfully.']);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Item::latest()->get();

            return DataTables::of($data)
                ->editColumn('image1', function ($item) {
                    return '<img src="' . asset('storage/app/' . $item->image1) . '" style="width: 100px;">';
                })
                ->editColumn('image2', function ($item) {
                    return '<img src="' . asset('storage/app/' . $item->image2) . '" style="width: 100px;">';
                })
                ->addColumn('action', function ($item) {
                    return '<button class="btn btn-success edit-item" data-id="' . $item->id . '">Edit</button>
                        <button class="btn btn-danger delete-item" data-id="' . $item->id . '">Delete</button>';
                })
                ->rawColumns(['image1', 'image2', 'action']) 
                ->make(true);
        }
        return view('items.index');
    }


}
