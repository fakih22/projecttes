<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(10);
        return view('customer.index', [
            'title' => 'Customer CRUD',
            'customers' => $customers,
        ]);
    }

    public function create()
    {
        return view('customer.create', [
            'title' => 'New Customer',
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'phone' => 'nullable|string|max:15',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo')->store('customers', 'public');
        }

        Customer::create($validatedData);

        return redirect()->route('customer.index')->with('message', 'Customer added successfully!');
    }

    public function show(Customer $customer)
    {
        return view('customer.show', [
            'title' => 'Customer Details',
            'customer' => $customer,
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit', [
            'title' => 'Edit Customer',
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'phone' => 'nullable|string|max:15',
        ]);

        if ($request->filled('password')) {
            $customer->password = Hash::make($validatedData['password']);
        }

        $customer->first_name = $validatedData['first_name'];
        $customer->last_name = $validatedData['last_name'];
        $customer->email = $validatedData['email'];
        $customer->phone = $validatedData['phone'];

        if ($request->hasFile('photo')) {
            if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                Storage::disk('public')->delete($customer->photo);
            }

            $customer->photo = $request->file('photo')->store('customers', 'public');
        }

        $customer->save();

        return redirect()->route('customer.index')->with('message', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {

        if (Auth::id() == $customer->id) {
            return redirect()->route('customer.index')->with('warning', 'Cannot delete yourself!');
        }

        if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
            Storage::disk('public')->delete($customer->photo);
        }

        $customer->delete();

        return redirect()->route('customer.index')->with('message', 'Customer deleted successfully!');
    }

    public function updateProfile(Request $request)
    {
        // user yang login via Sanctum
        /** @var \App\Models\Customer $customer */
        $customer = $request->user(); // auth('sanctum')->user()

        if (!$customer) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'phone' => 'nullable|string|max:15',
        ]);

        // update field basic
        $customer->first_name = $validatedData['first_name'];
        $customer->last_name = $validatedData['last_name'];
        $customer->email = $validatedData['email'];
        $customer->phone = $validatedData['phone'] ?? $customer->phone;

        // password (opsional)
        if (!empty($validatedData['password'])) {
            $customer->password = Hash::make($validatedData['password']);
        }

        // foto (opsional)
        if ($request->hasFile('photo')) {
            // hapus foto lama kalau ada
            if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                Storage::disk('public')->delete($customer->photo);
            }

            $customer->photo = $request->file('photo')->store('customers', 'public');
        }

        $customer->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'photo' => $customer->photo
                    ? asset('storage/' . $customer->photo)
                    : null,
            ],
        ]);
    }


}
