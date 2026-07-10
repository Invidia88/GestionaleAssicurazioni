<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInsuranceCompanyRequest;
use App\Http\Requests\UpdateInsuranceCompanyRequest;
use App\Models\InsuranceCompany;
use App\Models\Policy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InsuranceCompanyController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        return Inertia::render('Companies/Index', [
            'companies' => InsuranceCompany::query()
                ->withCount('policies')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('contact_name', 'like', "%{$search}%")
                            ->orWhere('contact_phone', 'like', "%{$search}%")
                            ->orWhere('contact_email', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->paginate(12)
                ->withQueryString(),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Companies/Form', [
            'company' => null,
        ]);
    }

    public function store(StoreInsuranceCompanyRequest $request): RedirectResponse
    {
        $company = InsuranceCompany::create($request->validated());

        return redirect()
            ->route('insurance-companies.show', $company)
            ->with('success', 'Compagnia creata correttamente.');
    }

    public function show(Request $request, InsuranceCompany $insuranceCompany): Response
    {
        $insuranceCompany->load(['policies' => fn ($query) => $query->with('client')->orderByDesc('end_date')]);

        $sourcePolicy = null;

        if ($request->filled('quote_policy_id')) {
            $sourcePolicy = Policy::query()
                ->with(['client', 'insuranceCompany'])
                ->find($request->integer('quote_policy_id'));
        }

        if ($sourcePolicy && ! $sourcePolicy->can_create_quote) {
            $sourcePolicy = null;
        }

        return Inertia::render('Companies/Show', [
            'company' => $insuranceCompany,
            'quoteContext' => $sourcePolicy ? [
                'sourcePolicy' => $sourcePolicy,
                'client' => $sourcePolicy->client,
            ] : null,
        ]);
    }

    public function edit(InsuranceCompany $insuranceCompany): Response
    {
        return Inertia::render('Companies/Form', [
            'company' => $insuranceCompany,
        ]);
    }

    public function update(UpdateInsuranceCompanyRequest $request, InsuranceCompany $insuranceCompany): RedirectResponse
    {
        $insuranceCompany->update($request->validated());

        return redirect()
            ->route('insurance-companies.show', $insuranceCompany)
            ->with('success', 'Compagnia aggiornata correttamente.');
    }

    public function destroy(InsuranceCompany $insuranceCompany): RedirectResponse
    {
        $insuranceCompany->delete();

        return redirect()
            ->route('insurance-companies.index')
            ->with('success', 'Compagnia eliminata correttamente.');
    }
}
