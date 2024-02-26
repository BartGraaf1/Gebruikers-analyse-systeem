@extends('layouts.user_type.auth')

@section('content')

    <div>
        @if(session('success'))
            <div class="alert alert-success mx-4" role="alert">
                <p class="mb-0">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mx-4" role="alert">
                <p class="mb-0">{{ session('error') }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div class="col-md-8">
                                <h5 class="mb-0">All Productions</h5>
                            </div>
                            <div class="col-md-4">
                                <form action="{{ route('production-management.index') }}" method="GET">
                                    <div class="mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar">
                                        <div class="nav-item d-flex align-self-end">
                                            <a href="{{ route('production-management.index') }}" class="btn btn-secondary active mb-0 text-white" role="button" aria-pressed="true">
                                                Clear
                                            </a>
                                        </div>
                                        <div class="ms-md-3 pe-md-3 d-flex align-items-center">
                                            <div class="input-group">
                                                <button data-bs-toggle="tooltip" data-bs-original-title="Search productions" type="submit" class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></button>
                                                <input value="{{ request()->query('search') }}"  type="text" name="search"  class="form-control" placeholder="Search productions" onfocus="focused(this)" onfocusout="defocused(this)">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        ID
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Title
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Description
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Active
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Creation Date
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($productions as $production)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $production->id }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $production->title }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0 text-truncate" style="max-width: 150px;">{{ $production->description }}</p>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-sm bg-gradient-{{ $production->is_active ? 'success' : 'secondary' }}">{{ $production->is_active ? 'Active' : 'Inactive' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $production->created_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="/production-management/{{ $production->id }}/edit" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="Edit production">
                                                <i class="fas fa-edit text-secondary"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-5">
                            @if ($productions->hasPages())
                                {{ $productions->links('vendor.pagination.custom') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
