<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\StartRegistration;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StartRegistrationRequest;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    /**
     * Start a new registration or continue an existing
     * lightweight account registration.
     */
    public function store(
        StartRegistrationRequest $request,
        StartRegistration $startRegistration
    ): RedirectResponse {
        $data = $request->validated();

        $startRegistration->execute(
            $data['name'],
            $data['email']
        );

        return back()->with([
            'registration_email' => $data['email'],
            'registration_name' => $data['name'],
            'registration_started' => true,
        ]);
    }
}
