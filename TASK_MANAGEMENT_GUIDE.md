# 📋 TASK MANAGEMENT SYSTEM - COMPREHENSIVE GUIDE

**System Name**: Employee Attendance and Workflow Management System (EAWMS)  
**Version**: 2.0 - Task Management Module  
**Last Updated**: July 3, 2026

---

## 📑 TABLE OF CONTENTS

1. [System Overview](#system-overview)
2. [Role Definitions](#role-definitions)
3. [Workflow Architecture](#workflow-architecture)
4. [Database Schema](#database-schema)
5. [API Endpoints](#api-endpoints)
6. [Permission Matrix](#permission-matrix)
7. [Status Transitions](#status-transitions)
8. [Notifications](#notifications)
9. [Reports & Analytics](#reports--analytics)
10. [Implementation Checklist](#implementation-checklist)

---

## 🎯 SYSTEM OVERVIEW

### **Purpose**
Enable supervisors to assign weekly tasks to employees, track daily progress, and generate performance metrics while HR maintains oversight of organizational compliance and performance.

### **Key Principles**
- ✓ **One Task, Multiple Progress Records** - Single assignment, daily updates
- ✓ **Role Separation** - HR (Admin), Supervisors (Operations), Employees (Execution)
- ✓ **Audit Trail** - Complete history of all progress submissions
- ✓ **Scalability** - Works for 1-day to 30-day projects
- ✓ **Performance Tracking** - Natural data aggregation for KPIs

### **Core Workflow**

```
Supervisor Creates Task (Mon)
        ↓
Employee Receives Notification
        ↓
Employee Starts Task (Mon)
        ↓
Daily Progress Updates (Mon → Fri)
        ↓
Supervisor Reviews (Fri)
        ↓
Task Completion & Approval
        ↓
Performance Data Generated
        ↓
HR Reports & Analytics
```

---

## 👥 ROLE DEFINITIONS

### **1. HR Manager**

**Responsibilities:**
- Create and manage employee accounts
- Assign employees to departments
- Assign supervisors to departments
- Create organizational hierarchy
- Assign HR compliance tasks
- Monitor organizational metrics
- Generate monthly/quarterly reports
- Calculate performance KPIs
- View employee performance summaries

**Cannot Do:**
- Create operational work tasks
- Assign tasks directly to employees
- Monitor daily task progress
- Approve weekly task submissions
- Make technical decisions on work

**Dashboard Access:** HR Dashboard (Aggregate Metrics)

---

### **2. Supervisor / Department Manager**

**Responsibilities:**
- Create weekly operational tasks
- Assign tasks to direct reports
- Monitor daily progress submissions
- Review task completion quality
- Approve/reject task submissions
- Provide feedback to employees
- Manage team workflow

**Cannot Do:**
- Create HR compliance tasks
- Assign to employees outside their team
- Bypass employee approval workflow
- Access other supervisors' tasks
- View HR compliance tasks

**Dashboard Access:** Supervisor Dashboard (Team Operations)

---

### **3. Employee**

**Responsibilities:**
- Receive assigned tasks
- Start tasks when ready
- Submit daily progress updates
- Upload supporting attachments/evidence
- Complete work by deadline
- Communicate challenges

**Cannot Do:**
- Create or modify tasks
- Mark work as complete without approval
- Approve their own progress
- Reassign tasks

**Dashboard Access:** Employee Dashboard (My Work)

---

## 🔄 WORKFLOW ARCHITECTURE

### **Phase 1: Task Creation (Supervisor)**

```
Supervisor Dashboard
    ↓
[Create New Task] Button
    ↓
Form: Title, Description, Start Date, End Date, Priority
    ↓
Select Employee from Team
    ↓
Submit
    ↓
Task Status: ASSIGNED
    ↓
Notification Sent to Employee
```

**Data Created:**
```
tasks table:
{
  id: 1,
  title: "Develop Leave Module",
  description: "Create leave management system",
  assigned_by: 5 (Supervisor ID),
  assigned_to: 12 (Employee ID),
  start_date: 2026-07-01,
  end_date: 2026-07-05,
  priority: 'high',
  status: 'assigned',
  completion_percentage: 0,
  created_at: NOW(),
  updated_at: NOW()
}
```

---

### **Phase 2: Task Acknowledgment (Employee)**

```
Employee Dashboard
    ↓
New Task Notification: "Develop Leave Module"
    ↓
[View Task] Details
    ↓
Reads: Title, Description, Timeline, Requirements
    ↓
[Start Task] Button
    ↓
Task Status: IN_PROGRESS
    ↓
Notification: Supervisor sees "Employee started task"
```

**Data Updated:**
```
tasks table:
{
  status: 'in_progress',
  started_at: NOW(),
  updated_at: NOW()
}
```

---

### **Phase 3: Daily Progress Updates (Mon-Fri)**

```
Each Day:

Employee Dashboard
    ↓
[Update Progress] Button
    ↓
Form Appears:
    ├─ Date: [Auto-filled with today]
    ├─ Work Done: [Text area]
    ├─ Completion %: [Numeric input]
    ├─ Challenges: [Text area]
    └─ Attachments: [File upload]
    ↓
Submit
    ↓
task_progress record created
    ↓
Task completion_percentage updated
    ↓
Supervisor notified: "John updated progress to 45%"
```

**Data Created:**
```
task_progress table:
{
  id: 1,
  task_id: 1,
  employee_id: 12,
  progress_date: 2026-07-01,
  work_done: "Database tables created",
  completion_percentage: 20,
  challenges: "None",
  attachment_path: "storage/progress/task_1_mon.png",
  supervisor_reviewed_at: NULL,
  reviewed_by: NULL,
  remarks: NULL,
  created_at: NOW()
}
```

Repeated Daily:
- Tuesday: 45%
- Wednesday: 70%
- Thursday: 90%
- Friday: 100%

---

### **Phase 4: Supervisor Review (Friday)**

```
Supervisor Dashboard
    ↓
Task: "Develop Leave Module" - Status: PENDING_REVIEW
    ↓
[Review Details] Opens
    ↓
Timeline View Shows:
    ├─ MON: Database tables (20%) ✓
    ├─ TUE: CRUD completed (45%) ✓
    ├─ WED: Approval workflow (70%) ✓
    ├─ THU: Notifications (90%) ✓
    └─ FRI: Testing (100%) ⏳ PENDING
    ↓
Reviews All Progress Records & Attachments
    ↓
[Approve] or [Request Changes]
    ↓
If Approved:
    Task Status: COMPLETED
    Employee Notified: "Task Approved!"
    Performance Data Generated
    
If Rejected:
    Task Status: IN_REVISION
    Employee Notified: "Changes Required: ..."
    Can resubmit
```

**Data Updated:**
```
task_progress table (each record):
{
  supervisor_reviewed_at: NOW(),
  reviewed_by: 5 (Supervisor ID),
  remarks: "Good work on notifications"
}

tasks table:
{
  status: 'completed' OR 'in_revision',
  completion_percentage: 100,
  completed_at: NOW(),
  updated_at: NOW()
}
```

---

### **Phase 5: Performance Data & Reporting**

```
End of Month/Quarter

HR Dashboard
    ↓
Automatic Aggregation:
    ├─ John:
    │   ├─ Tasks Assigned: 4
    │   ├─ Completed: 4 (100%)
    │   ├─ Average Progress: 92%
    │   ├─ On Time: 4/4
    │   └─ Quality: Excellent
    │
    ├─ Mary:
    │   ├─ Tasks Assigned: 4
    │   ├─ Completed: 3 (75%)
    │   ├─ Average Progress: 85%
    │   ├─ On Time: 3/4
    │   └─ Quality: Good
    
    ↓
Generate Reports:
    ├─ Weekly Performance Report
    ├─ Monthly Performance Summary
    ├─ Department-wise KPIs
    └─ Individual Performance Cards
    
    ↓
Data Available for:
    ├─ Appraisal Process
    ├─ Promotion Recommendations
    ├─ Performance Bonuses
    └─ Training Needs Analysis
```

---

## 🗄️ DATABASE SCHEMA

### **tasks Table** (Enhanced)

```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    assigned_by BIGINT UNSIGNED NOT NULL,  -- Supervisor ID
    assigned_to BIGINT UNSIGNED NOT NULL,  -- Employee ID
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,               -- NEW: Task start
    end_date DATE NOT NULL,                 -- NEW: Task deadline
    due_date DATE,                          -- Keep for compatibility
    priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
    status ENUM('assigned','in_progress','pending_review','completed','in_revision','archived') DEFAULT 'assigned',
    completion_percentage INT UNSIGNED DEFAULT 0,  -- NEW: Overall progress
    scope ENUM('operational','hr_compliance') DEFAULT 'operational',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_assigned_to_status (assigned_to, status),
    INDEX idx_assigned_by_status (assigned_by, status),
    INDEX idx_start_end_date (start_date, end_date)
);
```

### **task_progress Table** (Renamed from task_updates)

```sql
CREATE TABLE task_progress (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT UNSIGNED NOT NULL,
    employee_id BIGINT UNSIGNED NOT NULL,          -- NEW: Explicit field
    progress_date DATE NOT NULL,                   -- NEW: When progress was made
    work_done TEXT NOT NULL,                       -- NEW: Description of work
    completion_percentage INT UNSIGNED NOT NULL,   -- NEW: Progress %
    challenges TEXT,                               -- NEW: Issues faced
    attachment_path VARCHAR(255),                  -- NEW: Evidence file
    supervisor_reviewed_at TIMESTAMP NULL,         -- NEW: Review timestamp
    reviewed_by BIGINT UNSIGNED NULL,              -- NEW: Reviewer ID
    remarks TEXT,                                  -- NEW: Supervisor feedback
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_task_id (task_id),
    INDEX idx_employee_id (employee_id),
    INDEX idx_progress_date (progress_date),
    UNIQUE KEY unique_daily_progress (task_id, employee_id, progress_date)
);
```

### **Migration Strategy**

```
Old task_updates Table:
├─ id
├─ task_id
├─ user_id
├─ progress_notes
├─ status_after_update
└─ created_at

↓ Migrate to ↓

New task_progress Table:
├─ id
├─ task_id
├─ employee_id (from user_id)
├─ progress_date (extracted from created_at)
├─ work_done (from progress_notes)
├─ completion_percentage (NEW)
├─ challenges (NEW)
├─ attachment_path (NEW)
├─ supervisor_reviewed_at (NEW)
├─ reviewed_by (NEW)
├─ remarks (NEW)
└─ created_at
```

---

## 🔌 API ENDPOINTS

### **Supervisor Endpoints**

```
POST   /supervisor/tasks
GET    /supervisor/tasks
GET    /supervisor/tasks/{id}
PUT    /supervisor/tasks/{id}
DELETE /supervisor/tasks/{id}
GET    /supervisor/tasks/{id}/progress
POST   /supervisor/tasks/{id}/approve
POST   /supervisor/tasks/{id}/reject
GET    /supervisor/dashboard
```

### **Employee Endpoints**

```
GET    /employee/tasks
GET    /employee/tasks/{id}
POST   /employee/tasks/{id}/start
POST   /employee/tasks/{id}/progress
GET    /employee/dashboard
```

### **HR Endpoints**

```
GET    /hr/dashboard
GET    /hr/reports/weekly
GET    /hr/reports/monthly
GET    /hr/reports/performance
GET    /hr/reports/kpi
GET    /hr/compliance/tasks
POST   /hr/compliance/tasks
GET    /hr/compliance/tasks/{id}
POST   /hr/compliance/tasks/{id}/approve
```

---

## 🔐 PERMISSION MATRIX

| Action | HR | Supervisor | Employee |
|--------|----|-----------:|----------|
| Create Operational Task | ❌ | ✅ Own Team | ❌ |
| Create HR Task | ✅ | ❌ | ❌ |
| View Task Details | ✅ HR Tasks | ✅ Own | ✅ Assigned |
| Edit Task | ❌ | ✅ Before Start | ❌ |
| Assign Task | ✅ HR Tasks | ✅ Own Team | ❌ |
| Start Task | ❌ | ❌ | ✅ Own |
| Submit Progress | ❌ | ❌ | ✅ Own |
| Review Progress | ❌ | ✅ Own | ❌ |
| Approve Task | ❌ | ✅ Own | ❌ |
| View Dashboard | ✅ Org View | ✅ Team View | ✅ My Tasks |
| Generate Reports | ✅ | ⚠️ Own Team | ❌ |
| View KPIs | ✅ | ⚠️ Own Team | ❌ |

---

## 🔄 STATUS TRANSITIONS

### **Valid Status Flow**

```
ASSIGNED
    ↓
IN_PROGRESS ──→ PENDING_REVIEW
    ↓                  ↓
(Optional Revision) ← IN_REVISION
    ↓
COMPLETED
    ↓
ARCHIVED (after 30 days)
```

### **Transition Rules**

```
ASSIGNED → IN_PROGRESS
├─ Who: Employee
├─ Condition: Task exists & not started
└─ Action: Click "Start Task"

IN_PROGRESS → PENDING_REVIEW
├─ Who: System (auto)
├─ Condition: end_date reached
└─ Action: Auto-move if not manually moved

IN_PROGRESS → IN_REVISION
├─ Who: Supervisor
├─ Condition: Review progress, find issues
└─ Action: Click "Request Changes"

PENDING_REVIEW → COMPLETED
├─ Who: Supervisor
├─ Condition: All progress records reviewed
└─ Action: Click "Approve"

COMPLETED → ARCHIVED
├─ Who: System (scheduled)
├─ Condition: 30 days since completion
└─ Action: Nightly cron job

IN_REVISION → IN_PROGRESS
├─ Who: Employee
├─ Condition: Resubmit progress
└─ Action: Click "Reopen Task"
```

---

## 🔔 NOTIFICATIONS

### **Notification Types & Triggers**

| Trigger | To | Message | Via |
|---------|----|---------|----|
| Task Assigned | Employee | "New task assigned: Develop Leave Module (Due: Jul 5)" | Email + App |
| Task Started | Supervisor | "John started: Develop Leave Module" | App |
| Daily Update | Supervisor | "John updated progress: 70% - Approval workflow added" | App + Email (opt) |
| Friday Reminder | Employee | "Reminder: Submit today's progress before 5 PM" | Email + App |
| Task Rejected | Employee | "Your task needs revision. Supervisor says: ..." | Email + App |
| Task Approved | Employee | "Congratulations! Task approved with excellent quality" | Email + App |
| HR Task Assigned | Supervisor | "HR Task: Complete Security Training (Due: Jul 10)" | Email + App |
| Overdue Task | Supervisor | "John's task overdue by 1 day" | Email |

---

## 📊 REPORTS & ANALYTICS

### **Weekly Performance Report**

```
Employee: John Mwaluko
Week: June 30 - July 5, 2026
Department: IT

SUMMARY:
├─ Tasks Assigned: 1
├─ Completed: 1 (100%)
├─ On Time: Yes
├─ Completion Quality: Excellent
└─ Average Progress: 86%

TASK DETAILS:
├─ Task: Develop Leave Module
├─ Assigned: June 30
├─ Deadline: July 5
├─ Status: Completed
├─ Daily Progress:
│  ├─ Mon: 20% - Database tables created
│  ├─ Tue: 45% - CRUD operations completed
│  ├─ Wed: 70% - Leave approval workflow added
│  ├─ Thu: 90% - SMS notifications integrated
│  └─ Fri: 100% - Testing & documentation completed
├─ Supervisor Remarks: "Excellent work, well organized"
└─ Approval Date: July 5

PERFORMANCE METRICS:
├─ Consistency: ⭐⭐⭐⭐⭐ (Steady daily progress)
├─ Quality: ⭐⭐⭐⭐⭐ (All features working)
├─ Communication: ⭐⭐⭐⭐⭐ (Clear daily updates)
└─ Overall: Excellent (92/100)
```

### **Monthly Performance Summary**

```
Employee: John Mwaluko
Month: July 2026

STATISTICS:
├─ Total Tasks: 4
├─ Completed: 4 (100%)
├─ Late: 0
├─ Average Completion %: 92%
├─ Average Daily Updates: 5 per task
└─ Supervisor Approvals: 4/4

KPI METRICS:
├─ Task Completion Rate: 100%
├─ On-Time Delivery: 100%
├─ Quality Score: 94/100
├─ Consistency Score: 95/100
├─ Responsiveness: Excellent
└─ Overall Performance: EXCELLENT

SUPERVISOR FEEDBACK SUMMARY:
├─ Positive Trends:
│  ├─ Consistent daily updates
│  ├─ High-quality deliverables
│  └─ Proactive problem-solving
├─ Areas for Development: None noted
└─ Recommendation: Continue current performance

NEXT MONTH GOALS:
├─ Maintain 95%+ task completion
├─ Explore advanced features in Leave Module
└─ Mentor junior team members on best practices
```

### **Department-wise KPIs**

```
Department: IT
Month: July 2026

TEAM OVERVIEW:
├─ Team Size: 8
├─ Total Tasks Assigned: 12
├─ Completed: 11 (92%)
├─ Pending: 1
└─ Team Average Progress: 89%

TOP PERFORMERS:
├─ 1. John Mwaluko - 100% (4/4 tasks)
├─ 2. Mary Kipchoge - 100% (3/3 tasks)
└─ 3. Alex Mwangi - 80% (4/5 tasks)

METRICS:
├─ Department Completion Rate: 92%
├─ Average Task Duration: 4.5 days
├─ Quality Baseline: 92/100
└─ Overall Department Performance: GOOD

AREAS FOR IMPROVEMENT:
├─ Alex Mwangi needs support (1 overdue task)
├─ Document code quality standards
└─ Increase peer code reviews

RECOMMENDATIONS:
├─ Recognize John's excellent performance
├─ Provide training to Alex on time management
└─ Schedule team meeting on best practices
```

---

## ✅ IMPLEMENTATION CHECKLIST

### **Database**
- [ ] Create migration: `create_task_progress_table.php`
- [ ] Update migration: `update_tasks_table.php` (add fields)
- [ ] Migrate database
- [ ] Verify schema

### **Models**
- [ ] Update `Task` model with new relationships
- [ ] Create `TaskProgress` model
- [ ] Add scopes and methods
- [ ] Add query helpers

### **Controllers**
- [ ] Create `TaskProgressController`
- [ ] Update `TaskController` with authorization
- [ ] Create `HRDashboardController`
- [ ] Create `SupervisorDashboardController`
- [ ] Update `EmployeeDashboardController`
- [ ] Create `TaskReportController`

### **Authorization & Policies**
- [ ] Create `TaskPolicy`
- [ ] Create `TaskProgressPolicy`
- [ ] Add middleware checks
- [ ] Update route groups

### **Notifications**
- [ ] Create `TaskAssignedNotification`
- [ ] Create `TaskStartedNotification`
- [ ] Create `TaskProgressNotification`
- [ ] Create `TaskApprovedNotification`
- [ ] Create `TaskRejectedNotification`
- [ ] Create `FridayReminderNotification`

### **Services**
- [ ] Create `TaskProgressService`
- [ ] Create `TaskApprovalService`
- [ ] Create `PerformanceReportService`
- [ ] Create `KPICalculationService`

### **Routes**
- [ ] Update `web.php` with new routes
- [ ] Add middleware groups
- [ ] Add route parameters

### **Views** (Frontend)
- [ ] Supervisor task creation form
- [ ] Employee task detail view
- [ ] Employee progress update form
- [ ] Supervisor review dashboard
- [ ] HR dashboard view
- [ ] Report templates

### **Testing**
- [ ] Unit tests for authorization
- [ ] Feature tests for workflows
- [ ] Integration tests for notifications
- [ ] Permission boundary tests

### **Documentation**
- [ ] API documentation
- [ ] User guide for each role
- [ ] Troubleshooting guide

---

## 🚀 QUICK START

### **For Supervisors**
1. Navigate to `/supervisor/tasks`
2. Click `[Create New Task]`
3. Fill in task details
4. Select employee from your team
5. Submit
6. Monitor progress in dashboard

### **For Employees**
1. Check `/employee/tasks` for new tasks
2. Click `[Start Task]` when ready
3. Each day, click `[Update Progress]`
4. Submit work done, % complete, challenges
5. Wait for supervisor approval

### **For HR**
1. Navigate to `/hr/dashboard`
2. View organizational metrics
3. Click `[Reports]` for performance summaries
4. Use data for appraisals and promotions

---

## 📞 SUPPORT & TROUBLESHOOTING

### **Common Issues**

**Q: Employee can't see assigned task**
- A: Check if supervisor assigned to correct employee
- A: Verify employee's department assignment

**Q: Supervisor can't approve task**
- A: Check task status is "pending_review"
- A: Ensure all progress records are submitted

**Q: HR can't see performance metrics**
- A: Wait for tasks to be completed
- A: Check if tasks have scope='operational'

---

**End of Guide**

For detailed API documentation, see: [API_ENDPOINTS.md](./API_ENDPOINTS.md)
For database migration guides, see: [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)
