<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EAWMS - Employee Attendance & Workflow System</title>
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>

<!-- HEADER & NAVBAR -->
<header>
  <nav class="navbar">
    <div class="navbar-brand">
      <img src="{{ asset('images/tanzania-emblem.png') }}" alt="Government Emblem" class="emblem">
      <div class="brand-text">
        <h1 class="logo">EAWMS</h1>
        <p class="tagline">Workforce Management System</p>
      </div>
    </div>

    <ul class="nav-links">
      <li><a href="#overview">Overview</a></li>
      <li><a href="#roles">Roles & Access</a></li>
      <li><a href="#features">Capabilities</a></li>
      <li><a href="#workflow">Workflow</a></li>
    </ul>

    <a href="{{ route('login') }}" class="btn btn-primary">Access Portal</a>
  </nav>
</header>

<!-- HERO SECTION -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-text">
      <h2>Integrated Workforce Management Platform</h2>
      <p>Streamlined attendance tracking, leave management, task assignment, and performance analytics for government and institutional workforce operations.</p>
      <div class="hero-ctas">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Sign In</a>
        <a href="#overview" class="btn btn-secondary btn-lg">Learn More</a>
      </div>
    </div>
    <div class="hero-visual">
      <img src="{{ asset('images/Home image 1.png') }}" alt="Workforce Management Platform" class="hero-image">
    </div>
  </div>
</section>

<!-- OVERVIEW SECTION -->
<section id="overview" class="overview-section">
  <h2>System Overview</h2>
  <p class="section-intro">EAWMS is a comprehensive workforce management solution designed to streamline operational efficiency across departments and organizational hierarchies.</p>
  
  <div class="overview-grid">
    <div class="overview-card">
      <div class="card-icon">📋</div>
      <h3>Real-Time Monitoring</h3>
      <p>Track attendance, tasks, and leave requests with instant visibility across all departments</p>
    </div>
    <div class="overview-card">
      <div class="card-icon">🔄</div>
      <h3>Automated Workflows</h3>
      <p>Streamline approvals, notifications, and reporting with intelligent automation</p>
    </div>
    <div class="overview-card">
      <div class="card-icon">📊</div>
      <h3>Data-Driven Insights</h3>
      <p>Comprehensive analytics and reporting for informed decision-making</p>
    </div>
    <div class="overview-card">
      <div class="card-icon">🔐</div>
      <h3>Secure Access Control</h3>
      <p>Role-based permissions ensure data security and operational integrity</p>
    </div>
  </div>
</section>

<!-- ROLES & ACCESS SECTION -->
<section id="roles" class="roles-section">
  <h2>Role-Based Access & Workflows</h2>
  <p class="section-intro">Each user role has dedicated workflows tailored to their operational responsibilities.</p>
  
  <div class="roles-container">
    <div class="role-card">
      <h3>System Administrator</h3>
      <div class="role-description">
        <p><strong>Responsibilities:</strong></p>
        <ul>
          <li>System configuration and maintenance</li>
          <li>HR account creation and management</li>
          <li>Department structure setup</li>
          <li>Platform-wide settings and policies</li>
        </ul>
      </div>
      <a href="{{ route('login') }}" class="btn btn-small">Admin Access</a>
    </div>

    <div class="role-card">
      <h3>Human Resources (HR)</h3>
      <div class="role-description">
        <p><strong>Responsibilities:</strong></p>
        <ul>
          <li>Employee account creation and onboarding</li>
          <li>Leave approval and management</li>
          <li>Performance monitoring and reporting</li>
          <li>Staff records and documentation</li>
        </ul>
      </div>
      <a href="{{ route('login') }}" class="btn btn-small">HR Portal</a>
    </div>

    <div class="role-card">
      <h3>Supervisor / Team Lead</h3>
      <div class="role-description">
        <p><strong>Responsibilities:</strong></p>
        <ul>
          <li>Team attendance oversight</li>
          <li>Task assignment and tracking</li>
          <li>Leave request approval</li>
          <li>Performance reviews</li>
        </ul>
      </div>
      <a href="{{ route('login') }}" class="btn btn-small">Supervisor Access</a>
    </div>

    <div class="role-card">
      <h3>Employee</h3>
      <div class="role-description">
        <p><strong>Responsibilities:</strong></p>
        <ul>
          <li>Daily attendance logging</li>
          <li>Leave request submission</li>
          <li>Task completion tracking</li>
          <li>Personal performance view</li>
        </ul>
      </div>
      <a href="{{ route('login') }}" class="btn btn-small">Employee Portal</a>
    </div>
  </div>
</section>

<!-- CAPABILITIES SECTION -->
<section id="features" class="features-section">
  <h2>Core Capabilities</h2>
  <p class="section-intro">Comprehensive tools for modern workforce management.</p>
  
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">✓</div>
      <h3>Attendance Management</h3>
      <p>Monitor real-time attendance with automated late marking, notifications, and absence tracking across all operational hours.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📅</div>
      <h3>Leave Administration</h3>
      <p>Manage leave requests, approvals, and entitlements with automatic reference number generation and PDF documentation.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📋</div>
      <h3>Task Management</h3>
      <p>Assign, track, and update tasks with status monitoring and completion workflows for team coordination.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📈</div>
      <h3>Analytics & Reporting</h3>
      <p>Access detailed reports on attendance patterns, leave usage, performance metrics, and departmental trends.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🔔</div>
      <h3>Notifications & Alerts</h3>
      <p>Real-time alerts for attendance, leave status, task updates, and critical operational events with email integration.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">👥</div>
      <h3>Department Management</h3>
      <p>Organize workforce by departments with hierarchical access and role-based permissions for operational control.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🎯</div>
      <h3>Performance Tracking</h3>
      <p>Monitor employee performance, promotions, and career progression with comprehensive audit trails.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🔐</div>
      <h3>Security & Compliance</h3>
      <p>Enterprise-grade access control with role-based permissions and audit logging for regulatory compliance.</p>
    </div>
  </div>
</section>

<!-- WORKFLOW SECTION -->
<section id="workflow" class="workflow-section">
  <h2>Implementation Workflow</h2>
  <p class="section-intro">How the system gets deployed and operates in your organization.</p>
  
  <div class="workflow-steps">
    <div class="workflow-step">
      <div class="step-number">1</div>
      <h3>System Setup</h3>
      <p>Administrator configures the platform, defines departments, sets up policies, and establishes system parameters for your organization's structure.</p>
    </div>

    <div class="workflow-connector"></div>

    <div class="workflow-step">
      <div class="step-number">2</div>
      <h3>HR Configuration</h3>
      <p>HR teams create user accounts, manage employee records, set leave entitlements, and configure approval workflows for their departments.</p>
    </div>

    <div class="workflow-connector"></div>

    <div class="workflow-step">
      <div class="step-number">3</div>
      <h3>Team Deployment</h3>
      <p>Supervisors assign team members to projects and tasks while employees access their personalized dashboards to log daily activities.</p>
    </div>

    <div class="workflow-connector"></div>

    <div class="workflow-step">
      <div class="step-number">4</div>
      <h3>Operational Excellence</h3>
      <p>Continuous monitoring, reporting, and analytics drive informed decision-making and organizational improvements across all operational areas.</p>
    </div>
  </div>
</section>

<!-- CTA SECTION -->
<section class="cta-section">
  <div class="cta-content">
    <h2>Ready to Transform Your Workforce Management?</h2>
    <p>Access the EAWMS platform to streamline your organizational operations and enhance employee productivity.</p>
    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Sign In to Portal</a>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-section">
      <h4>About EAWMS</h4>
      <p>An integrated workforce management platform designed for organizational efficiency and operational excellence.</p>
    </div>
    <div class="footer-section">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#overview">Overview</a></li>
        <li><a href="#roles">Roles</a></li>
        <li><a href="#features">Capabilities</a></li>
        <li><a href="{{ route('login') }}">Login</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h4>Support</h4>
      <p>For technical assistance and inquiries, contact your system administrator or HR department.</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2026 Employee Attendance & Workflow Management System (EAWMS). All rights reserved.</p>
  </div>
</footer>

</body>
</html>
