@extends('hrms.layouts.app')

@section('title', 'Profile Settings - PeopleFlow HRMS')

@section('content')

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
    <p class="text-gray-600 dark:text-slate-400 mt-2">
        Manage your account information and security
    </p>
</div>

<div class="max-w-4xl space-y-6">

    <!-- Profile Information -->
    <div class="rounded-2xl border border-gray-200 dark:border-slate-700
        bg-white dark:bg-slate-800 shadow-lg overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700
            bg-gray-50 dark:bg-gradient-to-r dark:from-slate-750 dark:to-slate-800">

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Profile Information
            </h3>

            <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                Update your account information
            </p>
        </div>

        <div class="p-6">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- Update Password -->
    <div class="rounded-2xl border border-gray-200 dark:border-slate-700
        bg-white dark:bg-slate-800 shadow-lg overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700
            bg-gray-50 dark:bg-gradient-to-r dark:from-slate-750 dark:to-slate-800">

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Password Security
            </h3>

            <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                Keep your account secure with a strong password
            </p>
        </div>

        <div class="p-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- Delete Account -->
    <div class="rounded-2xl border border-red-200 dark:border-red-900/50
        bg-red-50 dark:bg-red-950/30 shadow-lg overflow-hidden">

        <div class="px-6 py-4 border-b border-red-200 dark:border-red-900/50
            bg-red-100 dark:bg-gradient-to-r dark:from-red-950/50 dark:to-red-900/30">

            <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">
                Danger Zone
            </h3>

            <p class="text-sm text-gray-600 dark:text-red-300/80 mt-1">
                Irreversible account actions
            </p>
        </div>

        <div class="p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
