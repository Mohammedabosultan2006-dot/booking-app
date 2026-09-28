<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = auth()->user()->paymentMethods()->latest()->get();
        return view('provider.payment_methods.index', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'           => 'required|in:bank,palpay,wallet',
            'label'          => 'required|string|max:100',
            'account_name'   => 'required|string|max:150',
            'account_number' => 'required|string|max:100',
        ]);

        auth()->user()->paymentMethods()->create($data);

        return back()->with('success', 'تمت إضافة وسيلة الدفع بنجاح.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        abort_unless($paymentMethod->provider_id === auth()->id(), 403);

        $data = $request->validate([
            'type'           => 'required|in:bank,palpay,wallet',
            'label'          => 'required|string|max:100',
            'account_name'   => 'required|string|max:150',
            'account_number' => 'required|string|max:100',
            'is_active'      => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $paymentMethod->update($data);

        return back()->with('success', 'تم تحديث وسيلة الدفع.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        abort_unless($paymentMethod->provider_id === auth()->id(), 403);
        $paymentMethod->delete();

        return back()->with('success', 'تم حذف وسيلة الدفع.');
    }
}
