<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\Http\Requests\EditUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index', [
            'title' => 'User CRUD',
            'users' => User::paginate(10)
        ]);
    }

    public function create()
    {
        return view('user.create', [
            'title' => 'New User',
            'users' => User::paginate(10)
        ]);
    }

    public function store(AddUserRequest $request)
    {
        $data = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('users', 'public');
            $data['photo'] = $photo;
        }

        User::create($data);

        return redirect()->route('user.index')->with('message', 'User added successfully!');
    }

    public function show($id)
    {
        //
    }

    public function edit(User $User)
    {
        return view('user.edit', [
            'title' => 'Edit User',
            'user' => $User
        ]);
    }

    public function update(EditUserRequest $request, User $User)
    {
        if ($request->filled('password')) {
            $User->password = Hash::make($request->password);
        }

        $User->name = $request->name;
        $User->last_name = $request->last_name;
        $User->email = $request->email;
        if ($request->hasFile('photo')) {
            if ($User->photo && Storage::disk('public')->exists($User->photo)) {
                Storage::disk('public')->delete($User->photo);
            }
            $photo = $request->file('photo')->store('users', 'public');
            $User->photo = $photo;
        }

        $User->save();

        return redirect()->route('user.index')->with('message', 'User updated successfully!');
    }

    public function destroy(User $User)
    {
        if (Auth::id() == $User->getKey()) {
            return redirect()->route('user.index')->with('warning', 'Can not delete yourself!');
        }

        if ($User->photo && Storage::disk('public')->exists($User->photo)) {
            Storage::disk('public')->delete($User->photo);
        }

        $User->delete();

        return redirect()->route('user.index')->with('message', 'User deleted successfully!');
    }
}
