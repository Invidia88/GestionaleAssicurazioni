<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePolicyRequest;
use App\Http\Requests\UpdatePolicyRequest;
use App\Models\Client;
use App\Models\InsuranceCompany;
use App\Models\Policy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PolicyController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        return Inertia::render('Policies/Index', [
            'policies' => Policy::query()
                ->with(['client', 'insuranceCompany'])
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('number', 'like', "%{$search}%")
                            ->orWhereHas('client', function ($query) use ($search) {
                                $query
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%")
                                    ->orWhere('tax_code', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderBy('end_date')
                ->paginate(12)
                ->withQueryString(),
            'filters' => [
                'search' => $search,
            ],
            'companies' => InsuranceCompany::query()
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function create(Request $request): Response
    {
        $sourcePolicy = null;
        $prefill = [
            'client_id' => $request->integer('client_id') ?: '',
            'insurance_company_id' => $request->integer('insurance_company_id') ?: '',
            'type' => $request->string('type')->toString() ?: 'Auto',
            'number' => '',
            'start_date' => '',
            'end_date' => '',
            'annual_premium' => '',
            'status' => 'attiva',
            'notes' => '',
        ];

        if ($request->filled('source_policy_id')) {
            $sourcePolicy = Policy::query()
                ->with(['client', 'insuranceCompany'])
                ->find($request->integer('source_policy_id'));

            if ($sourcePolicy && $sourcePolicy->can_create_quote) {
                $startDate = $sourcePolicy->end_date?->copy()->addYear();
                $endDate = $sourcePolicy->end_date?->copy()->addYears(2);

                $prefill = [
                    ...$prefill,
                    'client_id' => $sourcePolicy->client_id,
                    'insurance_company_id' => $request->integer('insurance_company_id') ?: '',
                    'type' => $sourcePolicy->type,
                    'number' => 'PREV-'.now()->format('YmdHis').'-'.$sourcePolicy->id,
                    'start_date' => $startDate?->toDateString() ?? '',
                    'end_date' => $endDate?->toDateString() ?? '',
                    'annual_premium' => '',
                    'status' => 'attiva',
                    'notes' => sprintf(
                        'Preventivo recupero cliente da polizza %s scaduta il %s. Compagnia precedente: %s.',
                        $sourcePolicy->number,
                        $sourcePolicy->end_date?->format('d/m/Y'),
                        $sourcePolicy->insuranceCompany?->name,
                    ),
                ];
            } else {
                $sourcePolicy = null;
            }
        }

        return Inertia::render('Policies/Form', [
            'policy' => null,
            'prefill' => $prefill,
            'mode' => $sourcePolicy ? 'quote' : 'policy',
            'sourcePolicy' => $sourcePolicy,
            ...$this->formOptions(),
        ]);
    }

    public function store(StorePolicyRequest $request): RedirectResponse
    {
        $policy = Policy::create($request->validated());

        return redirect()
            ->route('policies.show', $policy)
            ->with('success', 'Polizza creata correttamente.');
    }

    public function show(Policy $policy): Response
    {
        $policy->load(['client', 'insuranceCompany']);

        return Inertia::render('Policies/Show', [
            'policy' => $policy,
            'companies' => InsuranceCompany::query()
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function edit(Policy $policy): Response
    {
        return Inertia::render('Policies/Form', [
            'policy' => $policy,
            ...$this->formOptions(),
        ]);
    }

    public function update(UpdatePolicyRequest $request, Policy $policy): RedirectResponse
    {
        $policy->update($request->validated());

        return redirect()
            ->route('policies.show', $policy)
            ->with('success', 'Polizza aggiornata correttamente.');
    }

    public function destroy(Policy $policy): RedirectResponse
    {
        $policy->delete();

        return redirect()
            ->route('policies.index')
            ->with('success', 'Polizza eliminata correttamente.');
    }

    private function formOptions(): array
    {
        return [
            'clients' => Client::query()
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get(['id', 'first_name', 'last_name']),
            'companies' => InsuranceCompany::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'options' => Policy::options(),
        ];
    }
}
