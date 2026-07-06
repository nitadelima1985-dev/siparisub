<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\ResetUserPasswordRequest;
use App\Http\Requests\Dashboard\Users\StoreUserRequest;
use App\Http\Requests\Dashboard\Users\UpdateOwnPasswordRequest;
use App\Http\Requests\Dashboard\Users\UpdateProfileRequest;
use App\Http\Requests\Dashboard\Users\UpdateUserRequest;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->with(['role', 'organization'])
            ->when($request->user()->hasRole('admin_dinas'), function (Builder $query): void {
                $query->whereDoesntHave('role', fn (Builder $query) => $query->where('code', 'super_admin'));
            })
            ->when($request->filled('role'), fn (Builder $query) => $query->where('role_id', $request->integer('role')))
            ->when($request->filled('is_active'), fn (Builder $query) => $query->where('is_active', $request->boolean('is_active')))
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $search = $request->string('q')->toString();

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('organization_name', 'like', "%{$search}%")
                        ->orWhereHas('organization', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.users.index', [
            'users' => $users,
            'roles' => $this->manageableRoles($request->user()),
            'organizations' => $this->organizations(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', User::class);

        return view('dashboard.users.create', [
            'managedUser' => new User(['is_active' => true]),
            'roles' => $this->manageableRoles($request->user()),
            'organizations' => $this->organizations(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validatedUserData();

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('users/profile-photos', 'public');
        }

        $user = DB::transaction(fn (): User => User::create($data));

        return redirect()->route('dashboard.users.show', $user)->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load(['role', 'organization', 'createdDestinations', 'createdEvents', 'createdArticles']);

        return view('dashboard.users.show', [
            'managedUser' => $user,
        ]);
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorize('update', $user);

        return view('dashboard.users.edit', [
            'managedUser' => $user->load(['role', 'organization']),
            'roles' => $this->manageableRoles($request->user()),
            'organizations' => $this->organizations(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validatedUserData();

        if ($request->user()->hasRole('admin_dinas') && $user->hasRole('super_admin')) {
            abort(403);
        }

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('users/profile-photos', 'public');
        }

        $user->update($data);

        return redirect()->route('dashboard.users.show', $user)->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manageAccount', $user);

        if ($request->user()->is($user)) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Akun super admin tidak dapat dinonaktifkan dari halaman ini.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Akun pengguna berhasil diaktifkan.' : 'Akun pengguna berhasil dinonaktifkan.');
    }

    public function resetPassword(ResetUserPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update(['password' => $request->validated('password')]);

        return back()->with('success', 'Password pengguna berhasil direset.');
    }

    public function profile(Request $request): View
    {
        return view('dashboard.users.profile', [
            'managedUser' => $request->user()->load(['role', 'organization']),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('profile_photo');

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('users/profile-photos', 'public');
        }

        $request->user()->update($data);

        return redirect()->route('dashboard.profile')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function password(Request $request): View
    {
        return view('dashboard.users.password', [
            'managedUser' => $request->user()->load(['role', 'organization']),
        ]);
    }

    public function updatePassword(UpdateOwnPasswordRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => $request->validated('password')]);

        return redirect()->route('dashboard.profile.password')->with('success', 'Password Anda berhasil diperbarui.');
    }

    private function organizations()
    {
        return Organization::query()->active()->orderBy('name')->get(['id', 'name']);
    }

    private function manageableRoles(User $user)
    {
        return Role::query()
            ->when($user->hasRole('admin_dinas'), fn (Builder $query) => $query->where('code', '!=', 'super_admin'))
            ->orderBy('name')
            ->get();
    }
}
