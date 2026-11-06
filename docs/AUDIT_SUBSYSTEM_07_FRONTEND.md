# AUDIT REPORT: Subsystem 07 — FRONTEND JAVASCRIPT/UI

**Audit Date:** 2026-07-24
**Auditor:** Repository Auditor
**Subsystem:** FRONTEND JAVASCRIPT/UI

---

## FRONTEND FILE INVENTORY

**JavaScript Files Found (5 files):**
1. public/js/api.js (API client)
2. public/js/app.js (Application bootstrap)
3. public/js/components.js (UI components)
4. public/js/dashboard.js (Dashboard logic)
5. public/js/forms.js (Form handling)

---

## ISSUES FOUND

### FE-001: Components.js References Undefined Toast Class

**Issue ID:** FE-001
**Priority:** CRITICAL
**Status:** OPEN
**Description:** components.js line 119 references Toast class but Toast is defined in forms.js, not components.js, causing "Toast is not defined" error

**Evidence:**
```javascript
// public/js/components.js:119
window.Toast = Toast;

// Toast class is defined in forms.js:108-122
// components.js doesn't import or include forms.js
// This will cause ReferenceError: Toast is not defined
```

**File path:** `public/js/components.js`
**Line numbers:** 119

**Function/class:** Toast export

**Dependency trace:**
- Page loads components.js
- components.js tries to export window.Toast
- Toast class not defined in components.js
- ReferenceError: Toast is not defined

**Root cause:** Toast class defined in forms.js but referenced in components.js without import

**Production impact:** CRITICAL — JavaScript error on page load breaks components

**Allowed files to modify:**
- public/js/components.js
- public/js/forms.js

**Required fix:** Option 1: Move Toast class to components.js
Option 2: Add script tag to load forms.js before components.js
Option 3: Remove Toast export from components.js

**Acceptance test:**
1. Page loads without JavaScript errors
2. Toast class accessible globally

**Regression test:**
1. Verify all components work
2. Verify toast notifications work

---

### FE-002: API.js Calls Non-Existent Backend Endpoints

**Issue ID:** FE-002
**Priority:** CRITICAL
**Status:** OPEN
**Description:** api.js defines 20+ API methods that call backend endpoints which don't exist in the router

**Evidence:**
```javascript
// public/js/api.js:38 - companies/stats endpoint doesn't exist
static companyStats() { return this.request('GET', 'companies/stats'); }

// public/js/api.js:42 - companies/bulk-status endpoint doesn't exist
static bulkCompanyStatus(data) { return this.request('POST', 'companies/bulk-status', data); }

// public/js/api.js:47 - jobs/stats endpoint doesn't exist
static jobStats() { return this.request('GET', 'jobs/stats'); }

// public/js/api.js:55 - prospects/company/{id} endpoint doesn't exist
static prospectsByCompany(id) { return this.request('GET', `prospects/company/${id}`); }

// public/js/api.js:70 - applications/pipeline endpoint doesn't exist
static applicationPipeline() { return this.request('GET', 'applications/pipeline'); }

// public/js/api.js:74 - applications/stage endpoint doesn't exist
static updateStage(data) { return this.request('POST', 'applications/stage', data); }

// public/js/api.js:79 - interviews/upcoming endpoint doesn't exist
static upcomingInterviews() { return this.request('GET', 'interviews/upcoming'); }

// public/js/api.js:80 - interviews/calendar endpoint doesn't exist
static interviewCalendar(params = '') { return this.request('GET', 'interviews/calendar' + params); }

// public/js/api.js:88 - documents/type endpoint doesn't exist
static documentsByType(type) { return this.request('GET', `documents/type?type=${type}`); }

// public/js/api.js:89 - documents/active endpoint doesn't exist
static activeDocuments() { return this.request('GET', 'documents/active'); }

// public/js/api.js:96 - resumes/active endpoint doesn't exist
static activeResume() { return this.request('GET', 'resumes/active'); }

// public/js/api.js:97 - resumes/active endpoint doesn't exist
static setActiveResume(id) { return this.request('POST', 'resumes/active', { id }); }

// public/js/api.js:105 - cover-letters/job endpoint doesn't exist
static coverLettersForJob(jobId) { return this.request('GET', `cover-letters/job?job_id=${jobId}`); }

// public/js/api.js:113 - conversations/type endpoint doesn't exist
static conversationsByType(type) { return this.request('GET', `conversations/type?type=${type}`); }

// public/js/api.js:114 - conversations/follow-ups endpoint doesn't exist
static followUps() { return this.request('GET', 'conversations/follow-ups'); }

// public/js/api.js:115 - conversations/thread endpoint doesn't exist
static conversationThread(threadId) { return this.request('GET', `conversations/thread?thread_id=${threadId}`); }

// public/js/api.js:121 - analytics/summary endpoint doesn't exist
static analyticsSummary() { return this.request('GET', 'analytics/summary'); }

// public/js/api.js:122 - analytics/dashboard endpoint doesn't exist
static analyticsDashboard() { return this.request('GET', 'analytics/dashboard'); }

// public/js/api.js:123 - analytics/applications endpoint doesn't exist
static analyticsApplications() { return this.request('GET', 'analytics/applications'); }

// public/js/api.js:124 - analytics/companies endpoint doesn't exist
static analyticsCompanies() { return this.request('GET', 'analytics/companies'); }

// public/js/api.js:125 - analytics/activity endpoint doesn't exist
static analyticsActivity(params = '') { return this.request('GET', 'analytics/activity' + params); }

// public/js/api.js:128-132 - admin endpoints don't exist
static adminHealth() { return this.request('GET', 'admin/system-health'); }
static adminTables() { return this.request('GET', 'admin/tables'); }
static adminLogs(lines = 50) { return this.request('GET', `admin/logs?lines=${lines}`); }
static clearLogs() { return this.request('POST', 'admin/clear-logs'); }
static adminBackup() { return this.request('GET', 'admin/backup'); }
```

**File path:** `public/js/api.js`
**Line numbers:** 38, 42, 47, 55, 70, 74, 79, 80, 88, 89, 96, 97, 105, 113, 114, 115, 121-125, 128-132

**Function/class:** API class methods

**Dependency trace:**
- Frontend calls API methods
- API methods request non-existent endpoints
- Router returns "Invalid route" or "Controller not found"
- Features fail silently or with errors

**Root cause:** API client defines methods for endpoints that were never implemented in backend

**Production impact:** CRITICAL — 20+ frontend features will fail with 404 errors

**Allowed files to modify:**
- public/js/api.js
- Backend controllers (to implement missing endpoints)

**Required fix:** Option 1: Remove unused API methods
Option 2: Implement missing backend endpoints
Option 3: Mark methods as TODO and add error handling

**Acceptance test:**
1. All API calls to existing endpoints work
2. Non-existent endpoints return clear error or are removed

**Regression test:**
1. Verify frontend features work
2. Verify no breaking changes

---

### FE-003: App.js Calls Non-Existent API Methods

**Issue ID:** FE-003
**Priority:** CRITICAL
**Status:** OPEN
**Description:** app.js calls API.analyticsDashboard(), API.applicationPipeline(), and API.analyticsActivity() which don't exist in backend

**Evidence:**
```javascript
// public/js/app.js:113
const data = await API.analyticsDashboard();

// public/js/dashboard.js:19
const data = await API.analyticsSummary();

// public/js/dashboard.js:30
const data = await API.applicationPipeline();

// public/js/dashboard.js:44
const data = await API.analyticsActivity('?start=' + this.getStartDate() + '&end=' + this.getEndDate());

// These endpoints don't exist in backend router
```

**File path:** `public/js/app.js`
**Line numbers:** 113

**Function/class:** App::renderDashboard()

**Dependency trace:**
- User navigates to dashboard
- app.js calls API.analyticsDashboard()
- API requests /api/analytics/dashboard
- Router returns "Invalid route"
- Dashboard fails to load

**Root cause:** Frontend calls analytics endpoints that were never implemented

**Production impact:** CRITICAL — Dashboard fails to load completely

**Allowed files to modify:**
- public/js/app.js
- public/js/dashboard.js
- Backend controllers (to implement analytics endpoints)

**Required fix:** Option 1: Implement analytics endpoints in backend
Option 2: Change frontend to use existing endpoints
Option 3: Remove dashboard analytics features

**Acceptance test:**
1. Dashboard loads without errors
2. Analytics data displays correctly

**Regression test:**
1. Verify dashboard works
2. Verify no breaking changes

---

### FE-004: App.js renderProspects Expects priority_score Field

**Issue ID:** FE-004
**Priority:** MEDIUM
**Status:** OPEN
**Description:** app.js::renderProspects() expects priority_score field but prospects table schema doesn't have this column

**Evidence:**
```javascript
// public/js/app.js:152
async renderProspects(container) { await this.renderTable(container, 'prospects', API.prospects(), ['name', 'title', 'company', 'priority_score']); }

// database/schema.sql:53-73 (prospects table)
// No priority_score column in schema
// Has: id, title, company, description, rating, status, created_at, updated_at, deleted_at
```

**File path:** `public/js/app.js`
**Line numbers:** 152

**Function/class:** App::renderProspects()

**Dependency trace:**
- User navigates to prospects section
- app.js calls API.prospects()
- renderTable tries to display priority_score column
- Column doesn't exist in database
- Displays "-" for missing field

**Root cause:** Frontend expects field that doesn't exist in schema

**Production impact:** MEDIUM — Prospects table displays "-" for priority_score instead of actual data

**Allowed files to modify:**
- public/js/app.js
- database/schema.sql (add priority_score column)

**Required fix:** Option 1: Change frontend to use rating field instead
Option 2: Add priority_score column to schema

**Acceptance test:**
1. Prospects table displays correctly
2. Rating/priority displays actual data

**Regression test:**
1. Verify prospects section works
2. Verify no breaking changes

---

### FE-005: App.js deleteItem Uses Dynamic Method Names

**Issue ID:** FE-005
**Priority:** MEDIUM
**Status:** OPEN
**Description:** app.js::deleteItem() constructs dynamic method names that may not match API methods

**Evidence:**
```javascript
// public/js/app.js:221
deleteItem(section, id) { if (confirm('Delete?')) { API['delete' + this.capitalize(section.slice(0, -1))](id).then(() => this.loadSection(section)); } }

// Example: section = 'companies' → API.deleteCompany(id) ✓
// Example: section = 'prospects' → API.deleteProspect(id) ✓
// Example: section = 'applications' → API.deleteApplication(id) ✓
// Example: section = 'conversations' → API.deleteConversation(id) ✓
// Example: section = 'documents' → API.deleteDocument(id) ✓
// Example: section = 'interviews' → API.deleteInterview(id) ✓
// Example: section = 'recruiters' → API.deleteRecruiter(id) ✓

// But section = 'jobs' → API.deleteJob(id) ✓
// section = 'resumes' → API.deleteResume(id) ✓
// section = 'cover-letters' → API.deleteCoverLetter(id) ✓

// Risk: If section name doesn't match API method pattern, fails
```

**File path:** `public/js/app.js`
**Line numbers:** 221

**Function/class:** App::deleteItem()

**Dependency trace:**
- User clicks delete button
- deleteItem() constructs method name dynamically
- If method doesn't exist, TypeError
- Delete fails

**Root cause:** Dynamic method construction without validation

**Production impact:** MEDIUM — Delete may fail if section name doesn't match API method pattern

**Allowed files to modify:**
- public/js/app.js

**Required fix:** Add method existence check or use explicit mapping:
```javascript
deleteItem(section, id) {
  if (confirm('Delete?')) {
    const methodMap = {
      'companies': 'deleteCompany',
      'jobs': 'deleteJob',
      'applications': 'deleteApplication',
      'interviews': 'deleteInterview',
      'prospects': 'deleteProspect',
      'recruiters': 'deleteRecruiter',
      'conversations': 'deleteConversation',
      'documents': 'deleteDocument',
      'resumes': 'deleteResume',
      'cover-letters': 'deleteCoverLetter'
    };
    const method = methodMap[section];
    if (method && API[method]) {
      API[method](id).then(() => this.loadSection(section));
    }
  }
}
```

**Acceptance test:**
1. Delete buttons work for all sections
2. No JavaScript errors on delete

**Regression test:**
1. Verify delete operations work
2. Verify no breaking changes

---

### FE-006: Dashboard.js Calls Non-Existent Analytics Endpoints

**Issue ID:** FE-006
**Priority:** CRITICAL
**Status:** OPEN
**Description:** dashboard.js calls API.analyticsSummary(), API.applicationPipeline(), and API.analyticsActivity() which don't exist

**Evidence:**
```javascript
// public/js/dashboard.js:19
const data = await API.analyticsSummary();

// public/js/dashboard.js:30
const data = await API.applicationPipeline();

// public/js/dashboard.js:44
const data = await API.analyticsActivity('?start=' + this.getStartDate() + '&end=' + this.getEndDate());

// These endpoints don't exist in backend router
```

**File path:** `public/js/dashboard.js`
**Line numbers:** 19, 30, 44

**Function/class:** Dashboard::loadStats(), loadPipeline(), loadRecentActivity()

**Dependency trace:**
- Dashboard page loads
- dashboard.js calls analytics endpoints
- API requests non-existent endpoints
- Router returns "Invalid route"
- Dashboard features fail silently

**Root cause:** Dashboard calls analytics endpoints that were never implemented

**Production impact:** CRITICAL — Dashboard analytics features fail completely

**Allowed files to modify:**
- public/js/dashboard.js
- Backend controllers (to implement analytics endpoints)

**Required fix:** Option 1: Implement analytics endpoints in backend
Option 2: Change dashboard to use existing endpoints
Option 3: Remove analytics features from dashboard

**Acceptance test:**
1. Dashboard loads without errors
2. Analytics data displays correctly

**Regression test:**
1. Verify dashboard works
2. Verify no breaking changes

---

### FE-007: Forms.js API.request() May Call Non-Existent Endpoints

**Issue ID:** FE-007
**Priority:** MEDIUM
**Status:** OPEN
**Description:** forms.js::submit() uses API.request() with dynamic endpoint from form data, may call non-existent endpoints

**Evidence:**
```javascript
// public/js/forms.js:82-85
async submit(data) {
  const endpoint = this.form.dataset.endpoint;
  const method = this.form.dataset.method || 'POST';
  const result = await API.request(method, endpoint, data);

// If form has data-endpoint="companies/stats", will call non-existent endpoint
// No validation that endpoint exists
```

**File path:** `public/js/forms.js`
**Line numbers:** 82-85

**Function/class:** FormHandler::submit()

**Dependency trace:**
- User submits form
- FormHandler reads data-endpoint attribute
- API.request() calls endpoint without validation
- If endpoint doesn't exist, 404 error

**Root cause:** No endpoint validation before API call

**Production impact:** MEDIUM — Form submissions may fail if endpoint doesn't exist

**Allowed files to modify:**
- public/js/forms.js

**Required fix:** Add endpoint validation or error handling

**Acceptance test:**
1. Form submissions work for valid endpoints
2. Invalid endpoints show clear error

**Regression test:**
1. Verify form submissions work
2. Verify no breaking changes

---

## SUMMARY

**Total Issues:** 7
**Critical:** 4 (FE-001, FE-002, FE-003, FE-006)
**Medium:** 3 (FE-004, FE-005, FE-007)

**Production Blockers:**
- FE-001: Toast class undefined — Components fail to load
- FE-002: 20+ non-existent API endpoints — Frontend features fail
- FE-003: Dashboard analytics endpoints — Dashboard fails to load
- FE-006: Dashboard analytics endpoints — Dashboard features fail

**Recommended Fix Order:**
1. FE-001 (Toast class) — Immediate JavaScript error
2. FE-002 (Non-existent endpoints) — Remove or implement 20+ endpoints
3. FE-003 (Dashboard analytics) — Implement or change dashboard
4. FE-006 (Dashboard analytics) — Same as FE-003
5. FE-004 (priority_score field) — Change frontend or add column
6. FE-005 (Dynamic method names) — Add validation
7. FE-007 (Form endpoints) — Add validation

---

## API ENDPOINT MISMATCH SUMMARY

**Frontend API Methods (api.js): 33 methods**
**Backend Routes (router.php): Standard CRUD only**

**Frontend methods that EXIST in backend:**
- companies (GET, POST, PUT, DELETE)
- jobs (GET, POST, PUT, DELETE)
- prospects (GET, POST, PUT, DELETE)
- recruiters (GET, POST, PUT, DELETE)
- applications (GET, POST, PUT, DELETE)
- interviews (GET, POST, PUT, DELETE)
- documents (GET, POST, PUT, DELETE)
- resumes (GET, POST, PUT, DELETE)
- cover-letters (GET, POST, PUT, DELETE)
- conversations (GET, POST, PUT, DELETE)
- health (GET)

**Frontend methods that DON'T EXIST in backend (20+):**
- companies/stats
- companies/bulk-status
- jobs/stats
- prospects/company/{id}
- applications/pipeline
- applications/stage
- interviews/upcoming
- interviews/calendar
- documents/type
- documents/active
- resumes/active
- cover-letters/job
- conversations/type
- conversations/follow-ups
- conversations/thread
- analytics/summary
- analytics/dashboard
- analytics/applications
- analytics/companies
- analytics/activity
- admin/system-health
- admin/tables
- admin/logs
- admin/clear-logs
- admin/backup

---

**END OF AUDIT REPORT**
