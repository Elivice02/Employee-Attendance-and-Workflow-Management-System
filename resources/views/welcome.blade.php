<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EAWMS - Employee Attendance & Workflow System</title>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>

<header>
  <nav class="navbar">
    <h1 class="logo">EAWMS</h1>

    <ul class="nav-links">
      <li><a href="#">Home</a></li>
      <li><a href="#features">Features</a></li>
      <li><a href="#how">How it works</a></li>
    </ul>

    <a href="{{ route('login') }}" class="btn">Login</a>
  </nav>
</header>

<!-- HERO SECTION -->
<section class="welcome-section">
  <h2>Employee Attendance & Workflow System</h2>
  <p>Manage attendance, tasks, leave, and reports in one centralized platform.</p>
  <a href="{{ route('login') }}" class="btn" style="margin-top: 20px; display: inline-block;">Get Started</a>
</section>

<!-- FEATURES SECTION -->
<section id="features">
  <h2>Core Features</h2>
  <div class="feature-grid">
    <div class="feature-card">
      <h3>📊 Attendance Tracking</h3>
      <p>Real-time attendance monitoring and reporting</p>
    </div>
    <div class="feature-card">
      <h3>📅 Leave Management</h3>
      <p>Streamlined leave request and approval process</p>
    </div>
    <div class="feature-card">
      <h3>✓ Task Assignment</h3>
      <p>Assign and track tasks with ease</p>
    </div>
    <div class="feature-card">
      <h3>📈 Reports & Analytics</h3>
      <p>Comprehensive insights and data analytics</p>
    </div>
    <div class="feature-card">
      <h3>🔐 Role-Based Access</h3>
      <p>Secure access control for different user roles</p>
    </div>
    <div class="feature-card">
      <h3>🔔 Notifications</h3>
      <p>Real-time alerts and updates</p>
    </div>
  </div>
</section>

<!-- HOW IT WORKS SECTION -->
<section id="how" class="how-it-works">
  <h2>How It Works</h2>
  <div class="app-feature-grid">
    <div class="app-feature-card">
      <h3>Step 1: Admin Setup</h3>
      <p>Admin creates HR accounts and configures departments</p>
    </div>
    <div class="app-feature-card">
      <h3>Step 2: HR Management</h3>
      <p>HR manages employees, departments, and supervisors</p>
    </div>
    <div class="app-feature-card">
      <h3>Step 3: Daily Operations</h3>
      <p>Users log in and manage attendance, tasks, and leave</p>
    </div>
  </div>
</section>

<!-- CTA SECTION -->
<section class="cta">
  <h2>Start Managing Your Workforce Smarter Today</h2>
  <p>Join us and streamline your employee management process</p>
  <a href="{{ route('login') }}" class="btn">Login Now</a>
</section>

<footer>
  <p>&copy; 2026 Employee Attendance & Workflow Management System. All rights reserved.</p>
</footer>

</body>
</html>