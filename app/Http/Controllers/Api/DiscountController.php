<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Discount;

class DiscountController extends Controller
{
    
    public function index(Request $request) {
        $request = $request->header();
        $discounts = Discount::paginate((integer) pagination($request, 'discounts'));
        return response()->json($discounts, 200);
    }

    public function create(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request, [
                'user_id' => 'exists:users,user_id',
                'code' => 'required|max:20|unique:discounts',
                'value_percent' => 'required|integer',
                'value_max' => 'integer',
                'attempts' => 'integer',
                'started_at' => 'date',
                'expired_at' => 'date|after:started_at'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

            $discount = Discount::create([
                'user_id' => isset($request['user_id']) ? $request['user_id'] : null,
                'code' => $request['code'],
                'value_percent' => $request['value_percent'],
                'value_max' => isset($request['value_max']) ? $request['value_max'] : null,
                'attempts' => isset($request['attempts']) ? $request['attempts'] : null,
                'started_at' => isset($request['started_at']) ? $request['started_at'] : null,
                'expired_at' => isset($request['expired_at']) ? $request['expired_at'] : null
            ]);

            if ($discount) {
                return response()->json($discount, 200);
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
            'id' => 'required|exists:discounts,id'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $discount = Discount::where('id', $request['id']);
        $discount = $discount->get();
        
        if (count($discount) > 0) {
            return response()->json($discount, 200);
        }else {
            return response()->json(['status' => responseCode(404)]);
        }
    }

    public function update(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request, [
                'id' => 'required|exists:discounts,id',
                'user_id' => 'exists:users,user_id',
                'code' => 'required|max:20|unique:discounts',
                'value_percent' => 'required|integer',
                'value_max' => 'integer',
                'attempts' => 'integer',
                'started_at' => 'date',
                'expired_at' => 'date|after:started_at'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

            $discount = Discount::where('id', $request['id']);

            $discount->update([
                'user_id' => isset($request['user_id']) ? $request['user_id'] : null,
                'code' => $request['code'],
                'value_percent' => $request['value_percent'],
                'value_max' => isset($request['value_max']) ? $request['value_max'] : null,
                'attempts' => isset($request['attempts']) ? $request['attempts'] : null,
                'started_at' => isset($request['started_at']) ? $request['started_at'] : null,
                'expired_at' => isset($request['expired_at']) ? $request['expired_at'] : null
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
                'id' => 'required|exists:discounts,id'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);
        
            $discount =  Discount::where('id', $request['id']);
            $discount->delete();
            return response()->json(['status' => responseCode(200)]);

        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }
}
