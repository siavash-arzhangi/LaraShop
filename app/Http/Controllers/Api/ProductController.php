<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;

class ProductController extends Controller
{
    
    public function index(Request $request) {

        $request = $request->header();
        $auth = Auth::user();

        if (isAdmin($auth))
            $data = Product::paginate((integer) pagination($request, 'products'));
        else
            $data = Product::where('status', '1')->paginate((integer) pagination($request, 'products'));

        return response()->json($data, 200);
    }

    public function create(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request, [
                'category_id' => 'required|exists:categories,category_id',
                'title' => 'required|max:150',
                'description' => 'required|max:250',
                'price' => 'required',
                'sku' => 'required',
                'status' => 'boolean'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

            $product = Product::create([
                'product_id' => uuid('prdu'),
                'category_id' => $request['category_id'],
                'title' => $request['title'],
                'description' => $request['description'],
                'price' => $request['price'],
                'sku' => $request['sku'],
                'status' => isset($request['status']) ? $request['status'] : '1'
            ]);

            if ($product) {
                return response()->json($product, 200);
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
            'product_id' => 'required|exists:products,product_id'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $product = Product::where('product_id', $request['product_id']);

        if (!isAdmin($auth))
            $product->where('status', '1');

        $product = $product->get();
        
        if (count($product) > 0) {
            return response()->json($product, 200);
        }else {
            return response()->json(['status' => responseCode(404)]);
        }
    }

    public function update(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request, [
                'product_id' => 'required|exists:products,product_id',
                'category_id' => 'required|exists:categories,category_id',
                'title' => 'required|max:150',
                'description' => 'required|max:250',
                'price' => 'required',
                'sku' => 'required',
                'status' => 'boolean'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

            $product = Product::where('product_id', $request['product_id']);

            $product->update([
                'product_id' => $request['product_id'],
                'category_id' => $request['category_id'],
                'title' => $request['title'],
                'description' => $request['description'],
                'price' => $request['price'],
                'sku' => $request['sku'],
                'status' => $request['status']
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
                'product_id' => 'required|exists:products,product_id'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);
        
            $product = Product::where('product_id', $request['product_id']);
            $product->delete();
            return response()->json(['status' => responseCode(200)]);

        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }
}
