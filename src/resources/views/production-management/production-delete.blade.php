@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <h6 class="mb-0">{{ __('Delete User Confirmation') }}</h6>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <div class="mb-4">
                                <p>Are you sure you want to delete the user with the email: <strong>{{ $user->email }}</strong>?</p>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary btn-md">Cancel</a>
                                <button type="submit" class="btn btn-danger btn-md">Delete User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
