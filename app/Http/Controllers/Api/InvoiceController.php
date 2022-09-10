<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Lib\Payment;
use App\Models\User;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Discount;
use App\Models\Payment as PaymentModel;

class InvoiceController extends Controller
{
    
    public function index(Request $request) {
        $request = $request->all();
        $auth = Auth::user();
        
        if (isAdmin($auth))
            $data = Invoice::orderBy('created_at', 'desc')->paginate((integer) pagination($request, 'invoices'));
        else
            $data = Invoice::where('user_id', $auth->user_id)->orderBy('created_at', 'desc')->paginate((integer) pagination($request, 'invoices'));

        return response()->json($data, 200);
    }

    public function create(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        $validator = Validator::make($request, [
            'product_id' => 'required|exists:products,product_id',
            'discount' => 'exists:discounts,code'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $product = Product::where('product_id', $request['product_id'])->first();
        if ($product->exists()) {
            
            if ($request['discount']) {
                $discount = Discount::where('code', $request['discount'])->first();
                if (!$discount)
                    return response()->json(['status' => responseCode(403), 'error' => 'discount is not found']);
                if ($discount->status != 1)
                    return response()->json(['status' => responseCode(403), 'error' => 'discount is not available']);
                if ($discount->expired_at && $discount->expired_at < date('Y-m-d H:i:s'))
                    return response()->json(['status' => responseCode(403), 'error' => 'discount is expired']);

                $value_discount = $discount->value_percent * $product->price / 100;
                $value_discount = ($value_discount > $discount->value_max) ? $discount->value_max : $value_discount;
            }else {
                $value_discount = null;
                $discount = null;
            }

            $invoice = Invoice::create([
                'invoice_id' => uuid('invo'),
                'user_id' => $auth->user_id,
                'product_id' => $product->product_id,
                'value' => $product->price,
                'value_discount' => $value_discount ? $value_discount : null,
                'discount_id' => $discount ? $discount->id : null
            ]);
            return response()->json(['status' => responseCode(200), 'data' => $invoice->invoice_id]);
        }else {
            return response()->json(['status' => responseCode(404)]);
        }
    }

    public function read(Request $request) {

        $request = $request->all();
        $auth = Auth::user();

        $validator = Validator::make($request, [
            'invoice_id' => 'required|exists:invoices,invoice_id'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $invoice = Invoice::where('invoice_id', $request['invoice_id']);

        if (!isAdmin($auth))
            $invoice->where('user_id', $auth->user_id);
        
        if ($invoice->exists()) {
            return response()->json($invoice->get(), 200);
        }else {
            return response()->json(['status' => responseCode(404)]);
        }
    }

    public function update(Request $request) {

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

    public function pay(Request $request) {
        
        $request = $request->all();
        $auth = Auth::user();
        $gateway = config("app.payment.default");

        $validator = Validator::make($request, [
            'invoice_id' => 'required|exists:invoices,invoice_id'
        ]);

        if ($validator->fails())
            return response()->json(['status' => responseCode(403), 'errors' => $validator->errors()]);

        $invoice = Invoice::where('invoice_id', $request['invoice_id'])->first();

        $payment = PaymentModel::create([
            'user_id' => $auth->user_id,
            'invoice_id' => $invoice->invoice_id,
            'value' => $invoice->value - $invoice->value_discount,
            'gateway' => $gateway
        ]);
        $pay = new Payment();
        $result = $pay->pay($invoice->value - $invoice->value_discount, payDesc($invoice->id, $auth->name));

        if ($result) {
            PaymentModel::find($payment->id)->update(['code' => $result]);
            return response()->json(config("app.payment.providers.$gateway.url") . $result, 200);
        }else {
            return response()->json(['status' => responseCode(403)]);
        }
    }

    public function verify() {
        $pay = new Payment();
        $result = $pay->verify($_GET['Status'], $_GET['Authority']);
        return response()->json(['status' => responseCode($result)]);
    }
}