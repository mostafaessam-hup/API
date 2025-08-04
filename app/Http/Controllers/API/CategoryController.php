<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $categories = Category::select('id', 'name_'.app()->getLocale())->get();
        if ($categories) {
            return response()->json(['status' => true, 'data' => $categories]);
        }
        return response()->json(['status' => false, 'message' => 'No categories found']);
    }

    public function store(Request $request)
    {
        $category = Category::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
        ]);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not created',
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ]);
    }

    public function update(Request $request, $id)
    {

        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ]);
        }

        //Update only the fields present in the request, without specifying them explicitly
        // $category->update($request->only(array_keys($request->all())));  

        // Update only the fields I specified ('name_ar' and 'name_en') from the request, if they are sent
        $category->update($request->only(['name_ar', 'name_en']));

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ]);
        }
        $category->delete();
        return response()->json([
            'status' => true,
            'message' => ' category ' . $category->id . ' has been deleted ',

        ]);
    }

    public function show(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $category,
        ]);
    }
}
