<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class CategoryController extends Controller
{
    
    public function index(Request $request) {
        $request = $request->header();
        $categories = Category::paginate((integer) pagination($request, 'categories'));
        return response()->json($categories, 200);
    }

    public function create(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request, [
                'title' => 'required|max:150',
                'description' => 'required|max:250'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

            $category = Category::create([
                'category_id' => uuid('cate'),
                'title' => $request['title'],
                'description' => $request['description']
            ]);

            if ($category) {
                return response()->json($category, 200);
            }else {
                return response()->json(['status' => responseCode(500)]);
            }
        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }

    public function read(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        $validator = Validator::make($request, [
            'category_id' => 'required|exists:categories,category_id'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $category = Category::where('category_id', $request['category_id']);
        $category = $category->get();
        
        if (count($category) > 0) {
            return response()->json($category, 200);
        }else {
            return response()->json(['status' => responseCode(404)]);
        }
    }

    public function update(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request, [
                'category_id' => 'required|exists:categories,category_id',
                'title' => 'required|max:150',
                'description' => 'required|max:250'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

            $category = Category::where('category_id', $request['category_id']);

            $category->update([
                'category_id' => $request['category_id'],
                'title' => $request['title'],
                'description' => $request['description']
            ]);

            return response()->json(['status' => responseCode(200)]);
        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }

    public function delete(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request, [
                'category_id' => 'required|exists:categories,category_id'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);
        
            $category = Category::where('category_id', $request['category_id']);
            $category->delete();
            return response()->json(['status' => responseCode(200)]);

        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }
}
