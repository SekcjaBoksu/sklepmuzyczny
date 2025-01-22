@extends('layouts.app')

@section('title', 'Zarządzanie Użytkownikami')

@section('content')
<div class="container">
    <h1>Zarządzanie Użytkownikami</h1>

    <!-- Wiadomość o sukcesie -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Wiadomość o błędzie -->
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Filtry i Wyszukiwanie -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Szukaj po imieniu lub emailu" 
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select">
                    <option value="">Wszystkie Role</option>
                    <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Klient</option>
                    <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Pracownik</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Zastosuj Filtry</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię i Nazwisko</th>
                <th>Email</th>
                <th>Rola</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.updateRole', $user) }}">
                            @csrf
                            @method('PATCH')
                            <select 
                                name="role" 
                                class="form-select form-select-sm" 
                                {{ auth()->id() === $user->id ? 'disabled' : '' }}
                                onchange="this.form.submit()">
                                <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Klient</option>
                                <option value="employee" {{ $user->role === 'employee' ? 'selected' : '' }}>Pracownik</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                            </form>
                        @else
                            <span class="text-muted">Nie możesz usunąć samego siebie</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Nie znaleziono użytkowników.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@endsection
