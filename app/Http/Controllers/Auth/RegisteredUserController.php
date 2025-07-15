<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Notifications\AdminNotification;
use PragmaRX\Countries\Package\Countries;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $countries = Countries::all()
            ->pluck('name.common', 'cca2')
            ->toArray();

        $roles = Role::whereNotIn('name', ['admin', 'user', 'company'])->pluck('name', 'id');



        return view('auth.register', compact('countries', 'roles'));
    }

    public function searchAccountants(Request $request)
    {
        $query = $request->get('query');

        if (strlen($query) < 3) {
            return response()->json([]);
        }

        $users = User::whereHas('roles', function ($queryBuilder) {
            $queryBuilder->where('name', 'accountant');
        })
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', '%' . $query . '%')
                    ->orWhere('last_name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->get(['id', 'name', 'last_name', 'email']);

        return response()->json($users);
    }


    public function getCities(string $countryCode): JsonResponse
    {

        $cities = Countries::where('cca2', $countryCode)
            ->first()
            ->hydrate('cities')
            ->cities
            ->pluck('name', 'name')
            ->toArray();

        if (!$cities) {
            return response()->json(['error' => 'Country not found'], 404);
        }


        return response()->json($cities);
    }



    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'birthday' => ['nullable', 'date', 'before:today'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_type' => ['nullable', 'in:management,agency,broker,other'],
            'role_in_company' => ['nullable', 'in:owner,co-owner,manager,operator,other'],
            'website_link' => ['nullable', 'url'],
            'country_code' => ['nullable', 'string', 'max:5'],
            'number' => ['required'],
            'telegram' => ['nullable', 'phone'],
            'viber' => ['nullable', 'phone'],
            'whatsapp' => ['nullable', 'phone'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'street_address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state_province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:6'],
            'about_agency' => ['nullable', 'string'],
            'heard_about_us' => ['nullable', 'in:partner_recommendation,search,social_media,ad,conference,other'],
            'additional_comments' => ['nullable', 'string'],
            'permissions.rent' => ['nullable', 'boolean'],
            'permissions.real_estate' => ['nullable', 'boolean'],
            'permissions.service' => ['nullable', 'boolean'],
            'accountant_id' => ['nullable', 'exists:users,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->roles);

        if ($request->role !== 'user') {
            UserMeta::create([
                'user_id' => $user->id,
                'birthday' => $request->birthday,
                'company_name' => $request->company_name,
                'company_type' => $request->company_type,
                'role_in_company' => $request->role_in_company,
                'website_link' => $request->website_link,
                'country_code' => $request->country_code,
                'number' => $request->number,
                'telegram' => $request->telegram,
                'viber' => $request->viber,
                'whatsapp' => $request->whatsapp,
                'facebook' => $request->facebook,
                'instagram' => $request->instagram,
                'tiktok' => $request->tiktok,
                'street_address' => $request->street_address,
                'street_address_line_2' => $request->street_address_line_2,
                'city' => $request->city,
                'state_province' => $request->state_province,
                'postal_code' => $request->postal_code,
                'about_agency' => $request->about_agency,
                'heard_about_us' => $request->heard_about_us,
                'additional_comments' => $request->additional_comments,
                'rent' => $request->input('permissions.rent', false),
                'real_estate' => $request->input('permissions.real_estate', false),
                'service' => $request->input('permissions.service', false),
                'accountant_id' => $request->accountant_id,
            ]);
        }

        // Trigger the Registered event to send email verification
        event(new Registered($user));

        // Log in the user temporarily to show verification notice
        Auth::login($user);

        $message = "A new user has registered:\n\n" .
            "First_name: {$user->name}\n" .
            "Last_name: {$user->last_name}\n" .
            "Email: {$user->email}\n";
        if ($request->role !== 'user') {
            $message .= "Company Name: {$request->company_name}\n" .
                "Company Type: {$request->company_type}\n" .
                "Phone: {$request->country_code} {$request->number}\n";
        }

        $subject = "New User Registered on the Site";

        $notification = new AdminNotification($message, $subject);
        $notification->send();

        return redirect(route('verification.notice.email'));
    }
}
