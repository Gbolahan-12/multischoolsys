@extends('layouts.staff')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Update Profile</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Full Name --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Full Name</label>
            <input type="text" name="fullname" value="{{ old('fullname', auth()->user()->fullname) }}" 
                   class="w-full border p-2 rounded">
            @error('fullname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                   class="w-full border p-2 rounded">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- School --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">School</label>
            <select name="school_id" class="w-full border p-2 rounded">
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ auth()->user()->school_id == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
            @error('school_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Profile Image --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Profile Image</label>
            @if(auth()->user()->image)
                <div class="mb-2">
                    <img src="{{ asset(auth()->user()->image) }}" alt="Profile" class="w-24 h-24 rounded object-cover">
                    <label class="inline-flex items-center ml-2">
                        <input type="checkbox" name="remove_image" value="1" class="mr-1"> Delete current image
                    </label>
                </div>
            @endif
            <input type="file" name="image" class="border p-2 rounded w-full">
            @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Profile</button>
    </form>
</div>
@endsection