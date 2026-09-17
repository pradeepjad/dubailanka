<?php

namespace App\Http\Controllers\Seller;

use App\Actions\Seller\CreateBusinessEntity;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreBusinessEntityRequest;
use App\Models\BusinessEntity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BusinessEntityController extends Controller
{
    public function index(Request $request): Response
    {
        $businesses = $request->user()
            ->businessEntities()
            ->orderBy('business_entities.created_at')
            ->get([
                'business_entities.id',
                'business_entities.type',
                'business_entities.legal_name',
                'business_entities.trading_name',
                'business_entities.country_code',
                'business_entities.status',
            ]);

        return Inertia::render('Seller/Start', [
            'businesses' => $businesses,
        ]);
    }

    public function create(Request $request): Response
    {
        $type = $request->string('type')->toString();

        if (! in_array($type, [BusinessEntity::TYPE_PERSONAL, BusinessEntity::TYPE_REGISTERED], true)) {
            $type = BusinessEntity::TYPE_PERSONAL;
        }

        return Inertia::render('Seller/Business/Create', [
            'initialType' => $type,
            'accountEmail' => $request->user()->email,
        ]);
    }

    public function store(
        StoreBusinessEntityRequest $request,
        CreateBusinessEntity $createBusinessEntity,
    ): RedirectResponse {
        $business = $createBusinessEntity->execute(
            $request->user(),
            $request->validated(),
        );

        return to_route('seller.start')->with(
            'success',
            "{$business->legal_name} was created successfully. Next, we'll create your first store.",
        );
    }
}
