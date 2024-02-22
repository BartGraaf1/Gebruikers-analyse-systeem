@extends('layouts.user_type.auth')

@section('content')

    <div>
        <div class="container-fluid py-4">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <h6 class="mb-0">{{ __('Edit User') }}</h6> <!-- Change title to Edit User -->
                </div>
                <div class="card-body pt-4 p-3">
                    <!-- Update the form action to the route that handles user update, include user ID -->
                    <!-- Assume route name is 'user.update' and we are editing $user -->
                    <form action="{{ route('user.update', $user->id) }}" method="POST" role="form text-left">
                        @csrf
                        @method('PATCH') <!-- Add this line to specify the form method as PUT -->

                        @if($errors->any())
                            <div class="mt-3  alert alert-primary alert-dismissible fade show" role="alert">
                                <span class="alert-text text-white">{{$errors->first()}}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif
                        @if(session('success'))
                            <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                                <span class="alert-text text-white">{{ session('success') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif

                        <!-- For each input, pre-fill the value using the user's existing data -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user-name" class="form-control-label">{{ __('Full Name') }}</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" value="{{ $user->name }}" id="user-name" name="name">
                                    @error('name')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user-email" class="form-control-label">{{ __('Email') }}</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" value="{{ $user->email }}" id="user-email" name="email">
                                    @error('email')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user-phone" class="form-control-label">{{ __('Phone') }}</label>
                                    <input class="form-control @error('phone') is-invalid @enderror" type="tel" value="{{ $user->phone }}" id="user-phone" name="phone">
                                    @error('phone')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user-location" class="form-control-label">{{ __('Location') }}</label>
                                    <input class="form-control @error('location') is-invalid @enderror" type="text" value="{{ $user->location }}" id="user-location" name="location">
                                    @error('location')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- If you have roles or other properties, pre-select/pre-fill them as well -->
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="user-role" class="form-control-label">{{ __('User Role') }}</label>
                                <select name="user_role" id="user_role" class="form-control">
                                    <option value="1" {{ $user->user_role == 1 ? 'selected' : '' }}>Admin</option>
                                    <option value="2" {{ $user->user_role == 2 ? 'selected' : '' }}>Regular User</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Update User' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
