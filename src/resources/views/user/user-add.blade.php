@extends('layouts.user_type.auth')

@section('content')

    <div>
        <div class="container-fluid py-4">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <h6 class="mb-0">{{ __('Add New User') }}</h6>
                </div>
                <div class="card-body pt-4 p-3">
                    <!-- Update the form action to the route that handles storing a new user -->
                    <form action="{{ route('user.store') }}" method="POST" role="form text-left">
                        @csrf
                        @if($errors->any())
                            <div class="mt-3  alert alert-primary alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">
                            {{$errors->first()}}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif
                        @if(session('success'))
                            <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                            <span class="alert-text text-white">
                            {{ session('success') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user-name" class="form-control-label">{{ __('Full Name') }}</label>
                                    <div class="@error('name')border border-danger rounded-3 @enderror">
                                        <!-- Remove the value attribute to clear pre-filled data -->
                                        <input class="form-control" type="text" placeholder="Name" id="user-name" name="name">
                                        @error('name')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user-email" class="form-control-label">{{ __('Email') }}</label>
                                    <div class="@error('email')border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="email" placeholder="@example.com" id="user-email" name="email">
                                        @error('email')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user.phone" class="form-control-label">{{ __('Phone') }}</label>
                                    <div class="@error('phone')border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="tel" placeholder="40770888444" id="number" name="phone">
                                        @error('phone')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user.location" class="form-control-label">{{ __('Location') }}</label>
                                    <div class="@error('location') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="text" placeholder="Location" id="name" name="location">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="user.user_role" class="form-control-label">{{ __('User role') }}</label>
                                <select name="user_role" id="user_role" class="form-control">
                                    <option value="2">Regular user</option>
                                    <option value="1">Admin user</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <i>An email with the generated password will be send to the new users</i>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <!-- Change the button text to reflect the action -->
                            <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Add User' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
