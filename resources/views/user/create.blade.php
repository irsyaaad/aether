@extends('layouts.app')

@section('title', 'Add User - ' . config('app.name'))

@section('content')
<div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
    <div class="flex flex-col justify-center gap-2">
        <h1 class="text-xl font-medium leading-none text-mono">Add User</h1>
        <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
            Create a new user account
        </div>
    </div>
    <div class="flex items-center gap-2.5">
        <a class="kt-btn kt-btn-outline" href="{{ route('users.index') }}">
            <i class="ki-filled ki-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card p-7.5 max-w-2xl">
    <form action="{{ route('users.store') }}" method="POST" class="flex flex-col gap-5">
        @csrf

        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground">Name</label>
            <input class="kt-input" type="text" name="name" value="{{ old('name') }}" required placeholder="Enter full name"/>
            @error('name') <span class="text-xs text-destructive">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground">Email</label>
            <input class="kt-input" type="email" name="email" value="{{ old('email') }}" required placeholder="Enter email address"/>
            @error('email') <span class="text-xs text-destructive">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground">Password</label>
            <input class="kt-input" type="password" name="password" required placeholder="Min. 8 characters"/>
            @error('password') <span class="text-xs text-destructive">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-2.5 pt-2.5">
            <button class="kt-btn kt-btn-primary" type="submit">Save User</button>
            <a class="kt-btn kt-btn-outline" href="{{ route('users.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
