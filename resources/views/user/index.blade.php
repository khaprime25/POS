@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Users')
@section('content')

{{-- Success Alert --}}
@if(session('success'))
<div class="custom-alert-success">
    <div class="alert-icon">
        <i class="fa-solid fa-circle-check"></i>
    </div>

    <div class="alert-content">
        <h6>Success</h6>
        <p>{{ session('success') }}</p>
    </div>

    <button class="alert-close" onclick="this.parentElement.remove()">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
@endif

{{-- User Header --}}
<div class="mb-3">
    <p class="section-subtitle">
        Manage user accounts here!
    </p>
</div>

<!-- User Form -->
<div class="card mb-4" style="width: 75%;">
    <div class="section-header">
        <h4>Create User</h4>
    </div>

    <form action="{{ route('user.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-col-2">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-input" placeholder="Kaung Htet Aung ..." required>
            </div>

            <div class="form-col-2">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" placeholder="caxper@example.com" required>
            </div>

            <div class="form-col-full">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="cashier">
                        Cashier
                    </option>
                    <option value="chef">
                        Chef
                    </option>
                </select>
            </div>

            <div class="form-col-2">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="xxx xxx" required>
            </div>

            <div class="form-col-2">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="xxx xxx" required>
            </div>
        </div>

        <button class="btn btn-primary mt-3">
            Create User
        </button>
    </form>
</div>

<!-- User Table -->
<div class="card mt-3">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    {{ $user->name }}
                </td>

                <td>
                    {{ $user->role }}
                </td>

                <td>
                    <span class="{{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>

                <td>
                    {{ $user->created_at->diffForHumans() }}
                </td>

                <td>
                    @if($user->role === 'owner')
                    <span class="text-muted">Owner</span>
                    @else
                    <form method="POST" action="{{ route('user.toggle', $user) }}">
                        @csrf
                        <button class="btn {{ $user->is_active ? 'btn-cancel' : 'btn-primary' }}">
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    No Users found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection