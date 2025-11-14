@extends('layouts.admin')

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ $title ?? __('Customer Management') }}</h1>

    <!-- Main Content goes here -->

    <a href="{{ route('customer.create') }}" class="btn btn-primary mb-3">New Customer</a>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto Profil</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td scope="row">{{ $loop->iteration }}</td>
                    <td>
                        @if ($customer->photo)
                            <img src="{{ asset('storage/' . $customer->photo) }}" alt="Profile Image" width="50" height="50">
                        @else
                            <span>No Image</span>
                        @endif
                    </td>
                    <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>

                    <td>
                        <div class="d-flex">
                            <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-sm btn-primary mr-2">Edit</a>

                            <form action="{{ route('customer.destroy', $customer->id) }}" method="post"
                                onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $customers->links() }}

    <!-- End of Main Content -->
@endsection