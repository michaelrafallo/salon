<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\GoHighLevelService;
use App\Services\Salon\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SalonUserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo');
        }

        $user = $this->userService->create($data);

        // Sync to GHL (non-blocking)
        GoHighLevelService::syncUserContact($user);
        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $this->userToApiShape($user),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo');
        }

        $user = $this->userService->update($user, $data);

        // Sync changes to GHL (non-blocking)
        GoHighLevelService::updateGhlUserContact($user);

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
        if (! in_array($currentRole, ['superadmin', 'admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Only admins can login as another user.'], 403);
        }

        if (! $request->session()->has('salon_impersonator_email')) {
            $request->session()->put('salon_impersonator_email', (string) $request->session()->get('salon_user_email', ''));
            $request->session()->put('salon_impersonator_role', (string) $request->session()->get('salon_role', 'admin'));
        }

        $newRole = (string) ($user->role ?? 'admin');
        if (! in_array($newRole, ['superadmin', 'admin', 'receptionist', 'technician'], true)) {
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

        $data = $request->validated();
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo');
        }

        $user = $this->userService->update($user, $data);

        if (array_key_exists('email', $data)) {
            $request->session()->put('salon_user_email', $user->email);
        }

        // Sync changes to GHL (non-blocking)
        GoHighLevelService::updateGhlUserContact($user);

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

    public function syncGhlFetch(): JsonResponse
    {
        $ghlUsers = GoHighLevelService::fetchGhlUsers();

        if (empty($ghlUsers)) {
            return response()->json([
                'success' => false,
                'message' => 'No staff found in GoHighLevel. Please check your GHL connection and Location ID.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'users' => $ghlUsers,
            'total' => count($ghlUsers),
        ]);
    }

    public function syncGhlUpsert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ghl_id' => ['required', 'string'],
            'firstName' => ['nullable', 'string'],
            'lastName' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'role_type' => ['nullable', 'string'],
            'calendar_id' => ['nullable', 'string'],
        ]);

        $ghlId = $validated['ghl_id'];
        $firstName = $validated['firstName'] ?? '';
        $lastName = $validated['lastName'] ?? '';
        $email = $validated['email'] ?? '';
        $phone = $validated['phone'] ?? '';
        $roleType = strtolower($validated['role_type'] ?? '');

        // Determine role: admin if GHL role type contains 'admin', else technician
        $role = str_contains($roleType, 'admin') ? 'admin' : 'technician';

        // Try to find existing user: first by ghl_staff_id, then by email
        $user = User::query()->where('ghl_staff_id', $ghlId)->first();
        $status = 'updated';

        if (! $user && $email) {
            $user = User::query()->where('email', $email)->first();
            if ($user) {
                $status = 'linked';
            }
        }

        if (! $user) {
            $status = 'created';
        }

        $initials = strtoupper(mb_substr($firstName, 0, 1).mb_substr($lastName, 0, 1));
        $name = trim($firstName.' '.$lastName);

        $calendarId = $validated['calendar_id'] ?? null;

        // Use null for empty email/phone to avoid unique constraint issues
        $data = [
            'ghl_staff_id' => $ghlId,
            'first_name' => $firstName ?: null,
            'last_name' => $lastName ?: null,
            'name' => $name ?: null,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
            'role' => $role,
            'initials' => $initials ?: null,
            'status' => 'active',
        ];

        if ($calendarId) {
            $data['ghl_calendar_id'] = $calendarId;
        }

        if ($user) {
            // Only update fields that have values from GHL (don't overwrite with null)
            $updateData = array_filter($data, fn ($v) => $v !== null);
            $updateData['ghl_staff_id'] = $ghlId; // always set this
            // Do not overwrite existing user's role or password
            unset($updateData['role'], $updateData['password']);
            $user->update($updateData);
        } else {
            // Generate unique username
            $username = $email ? Str::before($email, '@') : ($name ? Str::slug($name, '.') : 'ghl-'.$ghlId);
            $baseUsername = $username ?: 'user';
            $counter = 1;
            while (User::query()->where('username', $username)->exists()) {
                $username = $baseUsername.$counter;
                $counter++;
            }
            $data['username'] = $username;
            $data['password'] = Str::random(16);
            $user = User::create($data);
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'data' => $this->userToApiShape($user),
        ]);
    }

    public function updateCalendar(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'ghl_calendar_id' => ['nullable', 'string'],
        ]);

        $user->update(['ghl_calendar_id' => $validated['ghl_calendar_id']]);

        return response()->json([
            'success' => true,
            'message' => 'Calendar updated successfully.',
            'data' => $this->userToApiShape($user),
        ]);
    }

    public function syncGhlTo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $token = SalonSettingsController::getClickaioToken();
        $locationId = \App\Models\Setting::query()->where('option_key', 'clickaio_location_id')->value('option_value');

        if (! $token || ! $locationId) {
            return response()->json([
                'success' => false,
                'message' => 'Clickaio not connected. Please authorize and set Location ID first.',
            ], 422);
        }

        // If already synced, update via Users API
        if ($user->ghl_staff_id) {
            GoHighLevelService::updateGhlUserContact($user);

            return response()->json([
                'success' => true,
                'status' => 'updated',
                'data' => $this->userToApiShape($user),
            ]);
        }

        // Not synced — create as GHL staff user
        $ghlUserId = GoHighLevelService::createGhlUser($token, $locationId, $user);

        if ($ghlUserId) {
            $user->update(['ghl_staff_id' => $ghlUserId]);

            return response()->json([
                'success' => true,
                'status' => 'created',
                'data' => $this->userToApiShape($user),
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => 'failed',
            'message' => 'Failed to create staff in Clickaio.',
        ], 422);
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
            'profilePhoto' => $user->profile_photo,
            'profilePhotoUrl' => $user->profile_photo ? asset('storage/'.$user->profile_photo) : null,
            'createdAt' => $user->created_at?->format('Y-m-d'),
            'last_login_at' => $user->last_login_at?->toIso8601String(),
            'lastLogin' => $user->last_login_at?->format('M j, Y g:i A'),
            'ghlStaffId' => $user->ghl_staff_id,
            'ghlCalendarId' => $user->ghl_calendar_id,
        ];
    }
}
