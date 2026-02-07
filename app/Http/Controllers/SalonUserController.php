<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\Salon\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalonUserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $this->userToApiShape($user),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $this->userToApiShape($user),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $this->userService->delete($user);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    public function loginAs(Request $request, User $user): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $currentRole = (string) $request->session()->get('salon_role', 'admin');
        if ($currentRole !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Only admins can login as another user.'], 403);
        }

        if (! $request->session()->has('salon_impersonator_email')) {
            $request->session()->put('salon_impersonator_email', (string) $request->session()->get('salon_user_email', ''));
            $request->session()->put('salon_impersonator_role', (string) $request->session()->get('salon_role', 'admin'));
        }

        $newRole = (string) ($user->role ?? 'admin');
        if (! in_array($newRole, ['admin', 'receptionist', 'technician'], true)) {
            $newRole = 'admin';
        }

        $request->session()->put('salon_authenticated', true);
        $request->session()->put('salon_user_email', $user->email);
        $request->session()->put('salon_role', $newRole);
        $user->forceFill(['last_login_at' => now()])->save();

        return response()->json([
            'success' => true,
            'message' => 'Logged in as '.$user->first_name.' '.$user->last_name.'.',
            'data' => [
                'role' => $newRole,
                'email' => $user->email,
            ],
        ]);
    }

    public function stopImpersonating(Request $request): RedirectResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return redirect()->route('salon.login');
        }

        $impersonatorEmail = (string) $request->session()->get('salon_impersonator_email', '');
        $impersonatorRole = (string) $request->session()->get('salon_impersonator_role', 'admin');

        $request->session()->forget(['salon_impersonator_email', 'salon_impersonator_role']);

        if ($impersonatorEmail !== '') {
            $request->session()->put('salon_user_email', $impersonatorEmail);
            $request->session()->put('salon_role', $impersonatorRole);
        }

        return redirect()->route('salon.dashboard');
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser($request);
        if (! $user) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->userToApiShape($user),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->getCurrentUser($request);
        if (! $user) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $user = $this->userService->update($user, $request->validated());

        if (array_key_exists('email', $request->validated())) {
            $request->session()->put('salon_user_email', $user->email);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $this->userToApiShape($user),
        ]);
    }

    private function getCurrentUser(Request $request): ?User
    {
        $email = $request->session()->get('salon_user_email');
        if (! $email) {
            return null;
        }

        return User::query()
            ->where('email', $email)
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function userToApiShape(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status ?? 'active',
            'initials' => $user->initials,
            'createdAt' => $user->created_at?->format('Y-m-d'),
            'last_login_at' => $user->last_login_at?->toIso8601String(),
            'lastLogin' => $user->last_login_at?->format('M j, Y g:i A'),
        ];
    }
}
