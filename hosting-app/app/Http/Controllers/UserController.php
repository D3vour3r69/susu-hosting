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

    public function show(User $user)
    {
        $applications = $user->applications()
            ->with(['head', 'responsible', 'featureItems.feature'])
            ->latest()
            ->paginate(5);

        return view('admin.users.show', compact('user', 'applications'));
    }
}
