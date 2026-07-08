<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\InsuranceCompany;
use App\Models\Policy;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'clients' => Client::count(),
                'companies' => InsuranceCompany::count(),
                'policies' => Policy::count(),
                'expiring7' => Policy::expiringWithin(7)->count(),
                'expiring30' => Policy::expiringWithin(30)->count(),
                'expired' => Policy::expired()->count(),
            ],
            'upcomingPolicies' => Policy::query()
                ->with(['client', 'insuranceCompany'])
                ->whereDate('end_date', '>=', today())
                ->orderBy('end_date')
                ->limit(8)
                ->get(),
            'latestPolicies' => Policy::query()
                ->with(['client', 'insuranceCompany'])
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }
}
