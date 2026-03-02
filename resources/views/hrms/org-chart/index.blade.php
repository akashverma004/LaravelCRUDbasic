@extends('hrms.layouts.app')

@section('title', 'Organization Chart - PeopleFlow HRMS')

@section('content')

<div class="mb-8 text-center">
    <h1 class="text-4xl font-bold text-slate-800 dark:text-white">
        Organization Chart
    </h1>
    <p class="mt-2 text-slate-500 dark:text-slate-400">
        {{ $stats['totalEmployees'] }} members · {{ $stats['managers'] }} managers
    </p>
</div>

@if($ceo)
    <div x-data="binaryTree()" class="overflow-x-auto pb-20">
        <div class="flex justify-center">
            @include('hrms.components.binary-node', ['employee' => $ceo])
        </div>
    </div>
@else
    <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 text-center
                dark:border-slate-700 dark:bg-slate-900">
        <p class="text-slate-500 dark:text-slate-400">
            No organization structure found
        </p>
    </div>
@endif


<script>
function binaryTree() {
    return {
        openNodes: {},

        toggle(id) {
            this.openNodes[id] = !this.openNodes[id]
        },

        isOpen(id) {
            return this.openNodes[id]
        }
    }
}
</script>

@endsection
