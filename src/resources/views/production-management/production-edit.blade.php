@extends('layouts.user_type.auth')

@section('content')

    <div>
        <div class="container-fluid py-4">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <h6 class="mb-0">{{ __('Edit Production') }}</h6>
                </div>
                <div class="card-body pt-4 p-3">
                    <!-- Assuming route name is 'productions.update' and we are editing $production -->
                    <form action="{{ route('production.update', ['production' => $production->id]) }}" method="POST" role="form text-left">
                        @csrf
                        @method('PATCH')

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

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="production-title" class="form-control-label">{{ __('Title') }}</label>
                                    <input class="form-control @error('title') is-invalid @enderror" type="text" value="{{ $production->title }}" id="production-title" name="title">
                                    @error('title')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="production-description" class="form-control-label">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="production-description" name="description">{{ $production->description }}</textarea>
                                    @error('description')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="is_active" class="form-control-label">{{ __('Active') }}</label>
                                    <div class="form-check form-switch ps-0">
                                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="is_active" name="is_active"
                                        @if($production->is_active) checked @endif>
                                    </div>
                                    @error('is_active') <!-- Adjusted to listen for 'is_active' errors -->
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Update Production' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
