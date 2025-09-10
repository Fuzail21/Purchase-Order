@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white shadow p-6 rounded">
    <h2 class="text-xl font-semibold mb-4">Website Settings</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.storeOrUpdate') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="logo" class="block font-medium">Logo</label>
            <input type="file" name="logo" id="logo" class="border rounded w-full p-2">
            @if(isset($setting->logo_path))
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" class="h-16">
                </div>
            @endif
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save
        </button>
    </form>
</div>
@endsection
