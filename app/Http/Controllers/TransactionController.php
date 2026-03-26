<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'transacted_on' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'remarks' => ['nullable', 'string', 'max:800'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
            'fund_account_id' => ['nullable', 'integer', 'exists:fund_accounts,id'],
            'payslip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $user = $request->user();

        if (! empty($data['payment_method_id']) && ! $user->paymentMethods()->whereKey($data['payment_method_id'])->exists()) {
            return back()
                ->withErrors(['payment_method_id' => 'Selected payment method is invalid.'])
                ->withInput();
        }

        if (! empty($data['fund_account_id']) && ! $user->fundAccounts()->whereKey($data['fund_account_id'])->exists()) {
            return back()
                ->withErrors(['fund_account_id' => 'Selected fund account is invalid.'])
                ->withInput();
        }

        if (! empty($data['category_id']) && ! $user->categories()->whereKey($data['category_id'])->exists()) {
            return back()
                ->withErrors(['category_id' => 'Selected category is invalid.'])
                ->withInput();
        }

        if ($request->hasFile('payslip')) {
            $file = $request->file('payslip');
            $data['payslip_path'] = $file->store("payslips/{$user->id}");
            $data['payslip_original_name'] = $file->getClientOriginalName();
        }

        $data['currency'] = 'LKR';

        $transaction = DB::transaction(function () use ($data, $user): Transaction {
            $method = null;
            $account = null;

            if (! empty($data['payment_method_id'])) {
                $method = $user->paymentMethods()->lockForUpdate()->find($data['payment_method_id']);
            }

            if (! empty($data['fund_account_id'])) {
                $account = $user->fundAccounts()->lockForUpdate()->find($data['fund_account_id']);
            }

            $amount = (float) $data['amount'];

            if ($method && $data['type'] === 'expense' && (float) $method->balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment method balance is too low for this expense.',
                ]);
            }

            if ($account && $data['type'] === 'expense' && (float) $account->current_balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Fund account balance is too low for this expense.',
                ]);
            }

            $transaction = $user->transactions()->create($data);

            if ($method) {
                if ($data['type'] === 'income') {
                    $method->increment('balance', $amount);
                } else {
                    $method->decrement('balance', $amount);
                }
            }

            if ($account) {
                if ($data['type'] === 'income') {
                    $account->increment('current_balance', $amount);
                } else {
                    $account->decrement('current_balance', $amount);
                }
            }

            return $transaction;
        });

        return redirect()
            ->route('dashboard', ['date' => $transaction->transacted_on->toDateString()])
            ->with('status', 'Transaction saved successfully.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);

        $selectedDate = $transaction->transacted_on->toDateString();

        DB::transaction(function () use ($request, $transaction): void {
            if ($transaction->payment_method_id) {
                $method = $request->user()->paymentMethods()->lockForUpdate()->find($transaction->payment_method_id);

                if ($method) {
                    $amount = (float) $transaction->amount;

                    if ($transaction->type === 'expense') {
                        $method->increment('balance', $amount);
                    } else {
                        $method->decrement('balance', $amount);
                    }
                }
            }

            if ($transaction->fund_account_id) {
                $account = $request->user()->fundAccounts()->lockForUpdate()->find($transaction->fund_account_id);

                if ($account) {
                    $amount = (float) $transaction->amount;

                    if ($transaction->type === 'expense') {
                        $account->increment('current_balance', $amount);
                    } else {
                        $account->decrement('current_balance', $amount);
                    }
                }
            }

            if ($transaction->payslip_path && Storage::exists($transaction->payslip_path)) {
                Storage::delete($transaction->payslip_path);
            }

            $transaction->delete();
        });

        return redirect()
            ->route('dashboard', ['date' => $selectedDate])
            ->with('status', 'Transaction deleted.');
    }

    public function downloadPayslip(Request $request, Transaction $transaction): StreamedResponse
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
        abort_if(! $transaction->payslip_path, 404);
        abort_unless(Storage::exists($transaction->payslip_path), 404);

        $downloadName = $transaction->payslip_original_name ?: basename($transaction->payslip_path);

        return Storage::download($transaction->payslip_path, $downloadName);
    }
}
