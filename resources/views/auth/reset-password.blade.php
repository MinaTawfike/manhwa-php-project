<x-guest-layout>
    <div class="auth-title">Create New Password</div>
    <div class="auth-subtitle">Set a strong password to secure your account</div>

    <div class="auth-card">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email', $request->email) }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="Your email"
                />
                @if ($errors->has('email'))
                    <div class="form-error">{{ $errors->first('email') }}</div>
                @endif
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">New Password</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Enter new password"
                />
                @if ($errors->has('password'))
                    <div class="form-error">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input 
                    id="password_confirmation" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="Confirm new password"
                />
                @if ($errors->has('password_confirmation'))
                    <div class="form-error">{{ $errors->first('password_confirmation') }}</div>
                @endif
            </div>

            <!-- Reset Button -->
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
    </div>
</x-guest-layout>
