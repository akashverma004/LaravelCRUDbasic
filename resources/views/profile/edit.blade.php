@extends('hrms.layouts.app')

@section('title', 'Profile Settings - PeopleFlow HRMS')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white">Profile Settings</h1>
    <p class="text-slate-400 mt-2">Manage your account information and security</p>
</div>

<div class="max-w-4xl space-y-6">

    <!-- Profile Information -->
    <div class="rounded-2xl border border-slate-700 bg-slate-800 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700 bg-gradient-to-r from-slate-750 to-slate-800">
            <h3 class="text-lg font-semibold text-white">Profile Information</h3>
            <p class="text-sm text-slate-400 mt-1">Update your account information</p>
        </div>
        <div class="p-6">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- Update Password -->
    <div class="rounded-2xl border border-slate-700 bg-slate-800 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700 bg-gradient-to-r from-slate-750 to-slate-800">
            <h3 class="text-lg font-semibold text-white">Password Security</h3>
            <p class="text-sm text-slate-400 mt-1">Keep your account secure with a strong password</p>
        </div>
        <div class="p-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- Delete Account -->
    <div class="rounded-2xl border border-red-900/50 bg-red-950/30 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-red-900/50 bg-gradient-to-r from-red-950/50 to-red-900/30">
            <h3 class="text-lg font-semibold text-red-300">Danger Zone</h3>
            <p class="text-sm text-red-400/70 mt-1">Irreversible account actions</p>
        </div>
        <div class="p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
