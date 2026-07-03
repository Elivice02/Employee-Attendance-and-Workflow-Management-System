<h2>Welcome to the System</h2>

<p>Hello {{ $user->name }},</p>

<p>Your account has been created.</p>

<p><strong>Email:</strong> {{ $user->email }}</p>
<p><strong>Temporary Password:</strong> {{ $password }}</p>

<p>
    <a href="{{ $loginUrl }}" style="display: inline-block; padding: 10px 16px; background: #0f766e; color: #ffffff; text-decoration: none; border-radius: 6px;">
        Login to the Application
    </a>
</p>

<p>If the button does not work, copy and paste this link into your browser:</p>
<p><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>

<p>Please login and change your password immediately.</p>
