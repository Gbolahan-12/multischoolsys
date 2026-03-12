@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Update Profile</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.password.update') }}" method="POST" enctype="multipart/form-data">
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


<div class="db- container-fluid">
      <div class=" mb-4">
        <div class="row mb-5">
          <div class="col-12">
            <h1 class="h2 mb-0">Account Admin</h1>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <!-- Card -->
            <div class="card">
              <!-- Card header -->
              <div class="card-header">
                <h3 class="mb-0">Profile Details</h3>
                <p class="mb-0">You have full control to manage your own account setting.</p>
              </div>
              <!-- Card body -->
              <div class="card-body">
                <div class="d-lg-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center mb-4 mb-lg-0">

                <img src="{{ $profile && $profile->image ? asset($profile->image) : asset('images/default-profile.jpeg') }}" id="img-uploaded"  class="avatar-xl rounded-circle" alt="avatar">
                
                    <div class="ms-3">
                      <h4 class="mb-0">Your avatar</h4>
                      <p class="mb-0">PNG or JPG no bigger than 800px wide and tall.</p>
                    </div>
                  </div>
                  <div>
                    <a href="{{ route('admin-dashboard') }}" class="btn btn-outline-primary btn-sm">Back</a>
                  </div>
                </div>
                <hr class="my-5" />
                <div>
                  <h4 class="mb-0">Personal Details</h4>
                  <p class="mb-4">Edit your personal information and address.</p>
                  <!-- Form -->
                  <form class="row gx-3 needs-validation" method="POST" action="{{ route('profile.password.update') }}" enctype="multipart/form-data" novalidate>

                    @csrf
                    <input type="file" name="image">
                    <!-- First name -->
                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label" for="first_name">First Name</label>
                      <input type="text" id="profileEditFname" class="form-control" placeholder="First Name" name="fullname" value="{{ old('fullname', auth()->user()->fullname) }}" required />
                      <div class="invalid-feedback">Please enter first name.</div>
                    </div>
                    <!-- Last name -->
                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label" for="last_name">Last Name</label>
                      <input type="text" id="profileEditLname" name="last_name" class="form-control" placeholder="Last Name" value="{{ old('last_name', $profile->last_name  ?? '') }}" required />
                      <div class="invalid-feedback">Please enter last name.</div>
                    </div>
                    <!-- Phone -->
                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label" for="phone">Phone</label>
                      <input type="text" id="profileEditPhone" name="phone" class="form-control" placeholder="Phone number" value="{{ old('phone', $profile->phone  ?? '') }}" required />
                      <div class="invalid-feedback">Please enter phone number.</div>
                    </div>
                    <!-- Birthday -->
                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label" for="address">Home address</label>
                      <input class="form-control" type="text" placeholder="Address here" id="homeAddress" value="{{ old('address', $profile->address  ?? '') }}" name="address" />
                      <div class="invalid-feedback">Please choose a address.</div>
                    </div>
                    <!-- Address -->
                    {{-- <div class="mb-3 col-12 col-md-6">
                      <label class="form-label" for="profileEditAddress1">Address Line 1</label>
                      <input type="text" id="profileEditAddress1" name="profileEditAddress1" class="form-control" placeholder="Address" required />
                      <div class="invalid-feedback">Please enter address.</div>
                    </div>
                    <!-- Address -->
                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label" for="profileEditAddress2">Address Line 2</label>
                      <input type="text" id="profileEditAddress2" name="profileEditAddress2" class="form-control" placeholder="Address" required />
                      <div class="invalid-feedback">Please enter address.</div>
                    </div>
                    <!-- State -->
                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label" for="profileEditState">State</label>
                      <select class="form-select" data-choices="" id="profileEditState" name="profileEditState" required>
                        <option value="">Select State</option>
                        <option value="1">Gujarat</option>
                        <option value="2">Rajasthan</option>
                        <option value="3">Maharashtra</option>
                      </select>
                      <div class="invalid-feedback">Please choose state.</div>
                    </div>
                    <!-- Country -->
                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label" for="editCountry">Country</label>
                      <select class="form-select" data-choices="" id="editCountry" required>
                        <option value="">Select Country</option>
                        <option value="1">India</option>
                        <option value="2">UK</option>
                        <option value="3">USA</option>
                      </select>
                      <div class="invalid-feedback">Please choose country.</div>
                    </div> --}}
                    <div class="col-12">
                      <!-- Button -->
                      <button class="btn btn-primary" type="submit"> {{ $profile ? 'Update Profile' : 'Create Profile' }}</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection