<?php

namespace App\Http\Controllers;

use App\Models\FundAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FundAccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('fund-accounts.index', [
            'accounts' => $user->fundAccounts()->latest()->get(),
            'paymentMethods' => $user->paymentMethods()->where('is_active', true)->orderBy('name')->get(),
            'categories' => $user->categories()->orderBy('name')->get(),
            'recentTransfers' => $user->fundTransfers()->with(['fundAccount', 'paymentMethod', 'destinationFundAccount'])->latest('transferred_on')->latest('id')->limit(10)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->fundAccounts()->create($this->validatedAccount($request));

        return back()->with('status', 'Fund account created.');
    }

    public function update(Request $request, FundAccount $fundAccount): RedirectResponse
    {
        abort_unless($fundAccount->user_id === $request->user()->id, 403);

        $fundAccount->update($this->validatedAccount($request, $fundAccount->id));

        return back()->with('status', 'Fund account updated.');
    }

    public function destroy(Request $request, FundAccount $fundAccount): RedirectResponse
    {
        abort_unless($fundAccount->user_id === $request->user()->id, 403);

        if ($fundAccount->transfers()->exists()) {
            return back()->withErrors(['account' => 'This fund account has transfer history and cannot be deleted.']);
        }

        $fundAccount->delete();

        return back()->with('status', 'Fund account deleted.');
    }

    public function transferBetweenAccounts(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'source_fund_account_id' => ['required', 'integer', 'exists:fund_accounts,id'],
            'destination_fund_account_id' => ['required', 'integer', 'exists:fund_accounts,id', 'different:source_fund_account_id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transferred_on' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:200'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($data, $user): void {
            $sourceAccount = $user->fundAccounts()->lockForUpdate()->findOrFail($data['source_fund_account_id']);
            $destinationAccount = $user->fundAccounts()->lockForUpdate()->findOrFail($data['destination_fund_account_id']);
            $amount = (float) $data['amount'];

            if ((float) $sourceAccount->current_balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient balance in source account for this transfer.',
                ]);
            }

            $sourceAccount->decrement('current_balance', $amount);
            $destinationAccount->increment('current_balance', $amount);

            $user->fundTransfers()->create([
                'fund_account_id' => $data['source_fund_account_id'],
                'destination_fund_account_id' => $data['destination_fund_account_id'],
                'amount' => $data['amount'],
                'transferred_on' => $data['transferred_on'],
                'remarks' => $data['remarks'],
            ]);
        });

        return back()->with('status', 'Transfer between accounts completed successfully.');
    }

    public function transferToPaymentMethod(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fund_account_id' => ['required', 'integer', 'exists:fund_accounts,id'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transferred_on' => ['required', 'date'],
            'remarks' => ['nullable', 'string', 'max:200'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($data, $user): void {
            $account = $user->fundAccounts()->lockForUpdate()->findOrFail($data['fund_account_id']);
            $method = $user->paymentMethods()->lockForUpdate()->findOrFail($data['payment_method_id']);
            $amount = (float) $data['amount'];

            if ((float) $account->current_balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient account balance for this transfer.',
                ]);
            }

            $account->decrement('current_balance', $amount);
            $method->increment('balance', $amount);

            $user->fundTransfers()->create($data);
        });

        return back()->with('status', 'Transfer completed successfully.');
    }

    private function validatedAccount(Request $request, ?int $accountId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:90',
                Rule::unique('fund_accounts', 'name')
                    ->where(fn ($query) => $query->where('user_id', $request->user()->id))
                    ->ignore($accountId),
            ],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'string', 'max:60'],
            'current_balance' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
