<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:12|confirmed',
            'role'     => 'required|in:admin,commercial',
        ]);

        $user = User::create([
            ...$data,
            'password'  => Hash::make($data['password']),
            'is_active' => true,
        ]);

        AuditLog::record('create', $user, [], ['email' => $user->email, 'role' => $user->role]);

        return redirect()->route('users.index')->with('success', 'Utilizador criado.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required|in:admin,commercial',
            'is_active' => 'boolean',
            'password'  => 'nullable|min:12|confirmed',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $old = ['email' => $user->email, 'role' => $user->role, 'is_active' => $user->is_active];
        $user->update($data);
        AuditLog::record('update', $user, $old, ['email' => $user->email, 'role' => $user->role]);

        return redirect()->route('users.index')->with('success', 'Utilizador actualizado.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Não pode eliminar a sua própria conta.']);
        }

        AuditLog::record('delete', $user, ['email' => $user->email]);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilizador removido.');
    }
}
