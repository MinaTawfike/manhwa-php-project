<x-guest-layout>
    <div class="auth-title">Reset Password</div>
    <div class="auth-subtitle">We'll help you get back in</div>

    <div class="auth-card">
        <p style="color: #b0b0b0; margin-bottom: 1.5rem; font-size: 0.9rem; line-height: 1.5;">
            Forgot your password? No problem. Just let us know your email address and we'll send you a password reset link.
        </p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="auth-message success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="Enter your email"
                />
                @if ($errors->has('email'))
                    <div class="form-error">{{ $errors->first('email') }}</div>
                @endif
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary">Send Reset Link</button>

            <!-- Back to Login -->
            <div style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('login') }}" style="color: #ff6b6b; text-decoration: none; font-size: 0.9rem;">Back to login</a>
            </div>
        </form>
    </div>
</x-guest-layout>
