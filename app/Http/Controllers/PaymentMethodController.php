<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(Request $request): View
    {
        $methods = $request->user()
            ->paymentMethods()
            ->latest()
            ->get();

        return view('payment-methods.index', [
            'methods' => $methods,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $request->user()->paymentMethods()->create($data);

        return back()->with('status', 'Payment method added.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        abort_unless($paymentMethod->user_id === $request->user()->id, 403);

        $data = $this->validatedData($request, $paymentMethod->id);

        $paymentMethod->update($data);

        return back()->with('status', 'Payment method updated.');
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        abort_unless($paymentMethod->user_id === $request->user()->id, 403);

        if ($paymentMethod->fundTransfers()->exists()) {
            return back()->withErrors(['payment_method' => 'This payment method has transfer history and cannot be deleted.']);
        }

        $paymentMethod->delete();

        return back()->with('status', 'Payment method deleted.');
    }

    private function validatedData(Request $request, ?int $methodId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('payment_methods', 'name')
                    ->where(fn ($query) => $query->where('user_id', $request->user()->id))
                    ->ignore($methodId),
            ],
            'type' => ['required', Rule::in(['cash', 'bank_card', 'bank_transfer', 'ewallet', 'other'])],
            'details' => ['nullable', 'string', 'max:120'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
