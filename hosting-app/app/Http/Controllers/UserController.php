<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(100);

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show(Request $request,User $user)
    {
        $domain = $request->input('domain');

        $applications = $user->applications()
            ->with(['head', 'responsible', 'featureItems.feature'])
            ->when($domain, function ($query, $domain) {
                return $query->where('domain', 'like', "%{$domain}%");
            })
            ->latest()
            ->paginate(5)
            ->appends($request->query());

        return view('admin.users.show', compact('user', 'applications', 'domain'));
    }
}
