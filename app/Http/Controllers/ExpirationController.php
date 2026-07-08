<?php

namespace App\Http\Controllers;

use App\Models\InsuranceCompany;
use App\Models\Policy;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpirationController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'range' => $request->string('range')->toString(),
            'company_id' => $request->string('company_id')->toString(),
            'type' => $request->string('type')->toString(),
            'status' => $request->string('status')->toString(),
        ];

        return Inertia::render('Expirations/Index', [
            'policies' => Policy::query()
                ->with(['client', 'insuranceCompany'])
                ->when($filters['search'], function ($query, string $search) {
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
                ->when($filters['range'] === 'today', fn ($query) => $query->whereDate('end_date', today()))
                ->when($filters['range'] === '7', fn ($query) => $query->expiringWithin(7))
                ->when($filters['range'] === '30', fn ($query) => $query->expiringWithin(30))
                ->when($filters['range'] === 'expired', fn ($query) => $query->expired())
                ->when($filters['company_id'], fn ($query, string $companyId) => $query->where('insurance_company_id', $companyId))
                ->when($filters['type'], fn ($query, string $type) => $query->where('type', $type))
                ->when($filters['status'], fn ($query, string $status) => $query->where('status', $status))
                ->orderBy('end_date')
                ->paginate(15)
                ->withQueryString(),
            'filters' => $filters,
            'companies' => InsuranceCompany::query()->orderBy('name')->get(['id', 'name']),
            'options' => Policy::options(),
        ]);
    }
}
