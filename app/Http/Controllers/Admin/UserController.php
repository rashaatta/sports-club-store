<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            }))
            ->orderBy('name')
            ->paginate(config('store.pagination.admin', 15))
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::options(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['role'] = Role::from($data['role']);

        User::query()->create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('admin.users.created'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::options(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::from($data['role']);

        if ($request->user()->is($user) && $role !== $user->role) {
            return back()->withInput()->withErrors(['role' => __('admin.users.cannot_demote_self')]);
        }

        if (
            $user->role === Role::SuperAdmin
            && $role !== Role::SuperAdmin
            && User::query()->where('role', Role::SuperAdmin)->count() <= 1
        ) {
            return back()->withInput()->withErrors(['role' => __('admin.users.last_super_admin')]);
        }

        if (blank($data['password'] ?? null)) {
            $data = Arr::except($data, ['password']);
        }

        $data['role'] = $role;
        $user->update($data);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', __('admin.users.updated'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', __('admin.users.cannot_delete_self'));
        }

        if (
            $user->role === Role::SuperAdmin
            && User::query()->where('role', Role::SuperAdmin)->count() <= 1
        ) {
            return back()->with('error', __('admin.users.last_super_admin'));
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('admin.users.deleted'));
    }
}
