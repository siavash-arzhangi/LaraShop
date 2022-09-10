<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Order;

class OrderController extends Controller
{
    
    public function index(Request $request) {
        $request = $request->all();
        $auth = Auth::user();
        
        if (isAdmin($auth))
            $data = Order::orderBy('created_at', 'desc')->paginate((integer) pagination($request, 'invoices'));
        else
            $data = Order::where('user_id', $auth->user_id)->orderBy('created_at', 'desc')->paginate((integer) pagination($request, 'invoices'));

        return response()->json($data, 200);
    }

    public function create(Request $request) {

    }

    public function read(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        $validator = Validator::make($request, [
            'order_id' => 'required|exists:orders,order_id'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $order = Order::where('order_id', $request['order_id']);

        if (!isAdmin($auth))
            $order->where('user_id', $auth->user_id);
        
        if ($order->exists()) {

            $order = $order->first();
            $user = User::where('user_id', $order->user_id)->first();
            $payment = Payment::where('id', $order->payment_id)->first();
            $invoice = Invoice::where('invoice_id', $payment->invoice_id)->first();
            $product = Product::where('product_id', $invoice->product_id)->first();

            $data = [
                'order' => $order,
                'user' => $user,
                'payment' => $payment,
                'invoice' => $invoice,
                'product' => $product
            ];

            return response()->json($data, 200);
        }else {
            return response()->json(['status' => responseCode(404)]);
        }
    }

    public function update(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        if (isAdmin($auth)) {
            $validator = Validator::make($request, [
                'order_id' => 'required|exists:orders,order_id',
                'status' => 'boolean'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

            $order = Order::where('order_id', $request['order_id']);

            $order->update([
                'order_id' => $request['order_id'],
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
                'invoice_id' => 'required|exists:invoices,invoice_id'
            ]);

            if ($validator->fails())
                return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);
        
            $invoice =  Invoice::where('invoice_id', $request['invoice_id']);
            $invoice->delete();
            return response()->json(['status' => responseCode(200)]);

        }else {
            return response()->json(['status' => responseCode(401)]);
        }
    }

}