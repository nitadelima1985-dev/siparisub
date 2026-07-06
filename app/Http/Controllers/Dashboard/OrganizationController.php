<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\OrganizationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\OrganizationRequest;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManage($request);

        $organizations = Organization::query()
            ->withCount('users')
            ->when($request->filled('type'), fn (Builder $query) => $query->where('organization_type', $request->input('type')))
            ->when($request->filled('is_active'), fn (Builder $query) => $query->where('is_active', $request->boolean('is_active')))
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $search = $request->string('q')->toString();

                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.organizations.index', [
            'organizations' => $organizations,
            'types' => OrganizationType::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeManage($request);

        return view('dashboard.organizations.create', [
            'organization' => new Organization(['is_active' => true]),
            'types' => OrganizationType::cases(),
        ]);
    }

    public function store(OrganizationRequest $request): RedirectResponse
    {
        $data = $request->validatedData();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('organizations/logos', 'public');
        }

        $organization = Organization::create([
            ...$data,
            'slug' => $this->uniqueSlug($data['name']),
        ]);

        return redirect()->route('dashboard.organizations.show', $organization)->with('success', 'Organisasi berhasil ditambahkan.');
    }

    public function show(Request $request, Organization $organization): View
    {
        $this->authorizeView($request, $organization);

        $organization->load(['users.role']);

        return view('dashboard.organizations.show', [
            'organization' => $organization,
        ]);
    }

    public function edit(Request $request, Organization $organization): View
    {
        $this->authorizeManage($request);

        return view('dashboard.organizations.edit', [
            'organization' => $organization,
            'types' => OrganizationType::cases(),
        ]);
    }

    public function update(OrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $data = $request->validatedData();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('organizations/logos', 'public');
        }

        if ($organization->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $organization->id);
        }

        $organization->update($data);

        return redirect()->route('dashboard.organizations.show', $organization)->with('success', 'Organisasi berhasil diperbarui.');
    }

    private function authorizeManage(Request $request): void
    {
        abort_unless($request->user()->hasRole('super_admin', 'admin_dinas'), 403);
    }

    private function authorizeView(Request $request, Organization $organization): void
    {
        if ($request->user()->hasRole('super_admin', 'admin_dinas')) {
            return;
        }

        abort_unless((int) $request->user()->organization_id === (int) $organization->id, 403);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (Organization::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
