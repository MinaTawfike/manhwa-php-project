@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="container">
        <h1 style="color:#ff6b6b; margin-bottom:1rem;">User Management</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #444;">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr style="border-bottom:1px solid #333;">
                        <td style="padding:0.75rem 0;">{{ $user->name }}</td>
                        <td style="padding:0.75rem 0; color:#b0b0b0;">{{ $user->email }}</td>
                        <td style="padding:0.75rem 0;">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                @csrf
                                <select name="role" style="padding:0.4rem;">
                                    @php $roles = ['super_admin','admin','editor','creator','viewer']; @endphp
                                    @foreach($roles as $r)
                                        <option value="{{ $r }}" @if($user->role === $r) selected @endif>{{ ucfirst(str_replace('_',' ', $r)) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-secondary" style="margin-left:0.5rem;">Save</button>
                            </form>
                        </td>
                        <td style="padding:0.75rem 0; text-align:right; width:220px;">
                            @if($user->isSuperAdmin())
                                <span style="color:#ff6b6b; font-weight:bold;">Super Admin</span>
                            @else
                                <span style="color:#999;">{{ ucfirst($user->role) }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:1rem;">
            {{ $users->links() }}
        </div>
    </div>
@endsection
