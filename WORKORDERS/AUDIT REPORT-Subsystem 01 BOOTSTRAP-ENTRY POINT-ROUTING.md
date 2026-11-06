# AUDIT REPORT: Subsystem 01 — BOOTSTRAP, ENTRY POINT, ROUTING

**Audit Date:** 2026-07-24
**Auditor:** Repository Auditor
**Subsystem:** BOOTSTRAP, ENTRY POINT, ROUTING

---

## FILE INVENTORY

### Entry Points
- `index.php` (root) — Main application entry point
- `public/index.php` — Frontend SPA entry point

### Bootstrap
- `api/bootstrap.php` — API bootstrap
- `error_boundary.php` — Global error handling

### Routing
- `api/router.php` — API router

### Utilities
- `api/utils/Response.php` — Response formatting
- `api/utils/DB.php` — Database singleton

### Base Classes
- `api/controllers/BaseController.php` — Base controller
- `api/services/BaseService.php` — Base service
- `api/models/Model.php` — Base model

### Controllers (22 files)
- AdminController.php
- AnalyticsController.php
- ApplicationsController.php
- BaseController.php
- CompaniesController.php
- ConversationsController.php
- CoverLettersController.php
- DocumentsController.php
- ExportController.php
- HealthController.php
- InterviewsController.php
- JobsController.php
- LinkedinStatsController.php
- ProspectController.php
- ProspectsController.php
- RecruitersController.php
- ResumesController.php
- SavedSearchesController.php
- SettingsController.php
- TailoredResumesController.php
- TargetCompaniesController.php
- UsersController.php

### Services (19 files)
- AIService.php
- AnalyticsService.php
- ApplicationService.php
- BaseService.php
- CompanyService.php
- ConversationService.php
- CoverLetterService.php
- DocumentService.php
- ExportService.php
- InterviewService.php
- JobService.php
- LinkedInStatService.php
- ProspectService.php
- RecruiterService.php
- ResumeService.php
- SavedSearchService.php
- SettingsService.php
- TargetCompanyService.php
- UserService.php

### Models (29 files)
- AnalyticsModel.php
- Application.php
- ApplicationModel.php
- Company.php
- CompanyModel.php
- Conversation.php
- ConversationModel.php
- CoverLetter.php
- CoverLetterModel.php
- Document.php
- DocumentModel.php
- ExportModel.php
- Interview.php
- InterviewModel.php
- Job.php
- JobModel.php
- LinkedInStatModel.php
- Model.php
- Prospect.php
- ProspectModel.php
- Recruiter.php
- RecruiterModel.php
- Resume.php
- ResumeModel.php
- SavedSearchModel.php
- SettingsModel.php
- TargetCompanyModel.php
- User.php
- UserModel.php

---

## ISSUES FOUND

### BOOT-001: BaseService Constructor Parameter Mismatch

**Issue ID:** BOOT-001
**Priority:** CRITICAL
**Status:** OPEN
**Description:** BaseService::__construct() accepts no parameters, but multiple child services pass parameters to parent::__construct()

**Evidence:**
```php
// api/services/BaseService.php:7-9
public function __construct() {
    $this->db = DB::getInstance();
}

// api/services/CompanyService.php:10-14
public function __construct()
{
    $this->model = new CompanyModel();
    parent::__construct($this->model->getTable());  // PASSING PARAMETER
}
```

**File:** `api/services/BaseService.php`
**Line Numbers:** 7-9

**Dependency Trace:**
- CompanyService extends BaseService
- CompanyService::__construct() calls parent::__construct($this->model->getTable())
- BaseService::__construct() accepts zero parameters
- Same pattern in: JobService, UserService, InterviewService, RecruiterService, AnalyticsService

**Root Cause:** BaseService constructor signature does not match the calling pattern used by child services. This should cause a fatal "too many arguments" error, but production reports a different error ("Call to undefined method CompanyModel::getTable()"), suggesting either:
1. The local code differs from production
2. PHP version differences in error handling
3. The error is being caught/suppressed

**Files Allowed To Modify:**
- api/services/BaseService.php
- api/services/CompanyService.php
- api/services/JobService.php
- api/services/UserService.php
- api/services/InterviewService.php
- api/services/RecruiterService.php
- api/services/AnalyticsService.php

**Required Fix:** 
Option A: Update BaseService::__construct() to accept optional $table parameter
Option B: Remove parameter passing from all child services and let them set $this->table directly

**Acceptance Test:**
1. Instantiate CompanyService without fatal error
2. Call CompanyService::getAll() without error
3. Verify $this->table is set correctly in service

**Regression Test:**
1. Test all services that extend BaseService
2. Verify all CRUD operations work
3. Verify no breaking changes to services that don't pass parameters

---

### BOOT-002: Model Property Type Conflict

**Issue ID:** BOOT-002
**Priority:** CRITICAL
**Status:** OPEN
**Description:** Base Model declares untyped $table property, but child models declare typed $table property, causing potential fatal "must not be defined" error

**Evidence:**
```php
// api/models/Model.php:5
protected $table;  // UNTYPED

// api/models/CompanyModel.php:7
protected string $table = 'companies';  // TYPED
```

**File:** `api/models/Model.php`
**Line Numbers:** 5

**Dependency Trace:**
- Model.php declares protected $table (untyped)
- CompanyModel, JobModel, UserModel, InterviewModel, RecruiterModel declare protected string $table (typed)
- PHP 8.0+ throws fatal error when child class redeclares typed property with different type

**Root Cause:** Inconsistent property typing between base and child classes. PHP 8.0+ strict typing rules cause fatal errors when property types don't match exactly.

**Files Allowed To Modify:**
- api/models/Model.php
- api/models/CompanyModel.php
- api/models/JobModel.php
- api/models/UserModel.php
- api/models/InterviewModel.php
- api/models/RecruiterModel.php

**Required Fix:**
Option A: Remove $table property from Model.php, only keep getTable() method
Option B: Add typed property to Model.php (protected string $table) and update all child models to match

**Acceptance Test:**
1. Instantiate CompanyModel without fatal error
2. Call CompanyModel::getTable() returns 'companies'
3. Verify all models with typed $table work correctly

**Regression Test:**
1. Test all 29 model files
2. Verify all services that use models work
3. Verify no breaking changes to models without typed $table

---

### BOOT-003: Missing ResumeService Include in ResumesController

**Issue ID:** BOOT-003
**Priority:** HIGH
**Status:** OPEN
**Description:** ResumesController extends BaseController and calls $this->service->create(), but ResumeService is not explicitly included

**Evidence:**
```php
// api/controllers/ResumesController.php:5-12
require_once ROOT_PATH . '/api/services/AIService.php';

class ResumesController extends BaseController {
    private $aiService;

    public function __construct() {
        $service = new ResumeService();  // ResumeService NOT INCLUDED
        parent::__construct($service);
        $this->aiService = new AIService();
    }
```

**File:** `api/controllers/ResumesController.php`
**Line Numbers:** 5-12

**Dependency Trace:**
- ResumesController instantiates ResumeService on line 9
- ResumeService is not included via require_once
- Relies on autoloader to find ResumeService
- Autoloader path: ROOT_PATH . '/api/services/'

**Root Cause:** Missing explicit include. While autoloader should handle this, it's inconsistent with other controllers (e.g., CompaniesController explicitly includes CompanyService).

**Files Allowed To Modify:**
- api/controllers/ResumesController.php

**Required Fix:** Add `require_once ROOT_PATH . '/api/services/ResumeService.php';` after line 3

**Acceptance Test:**
1. Instantiate ResumesController without error
2. Call ResumesController::generate() without error
3. Verify ResumeService is loaded correctly

**Regression Test:**
1. Test all resume-related endpoints
2. Verify generate() action works
3. Verify no breaking changes to other controllers

---

### BOOT-004: Duplicate Health Endpoint Logic

**Issue ID:** BOOT-004
**Priority:** LOW
**Status:** OPEN
**Description:** Health endpoint is handled in both index.php and router.php, creating redundancy

**Evidence:**
```php
// index.php:15-19
if ($path === '/api/health' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => ['status' => 'ok'], 'error' => null]);
    exit;
}

// api/router.php:10-13
if ($path === '/api/health' && $requestMethod === 'GET') {
    Response::json(Response::success(['status' => 'ok']));
    exit;
}
```

**File:** `index.php`
**Line Numbers:** 15-19

**Dependency Trace:**
- index.php handles /api/health before routing to api/bootstrap.php
- api/router.php also handles /api/health
- index.php check happens first, so router.php code is unreachable

**Root Cause**: Health endpoint was added to both entry points during development, creating dead code in router.php.

**Files Allowed To Modify:**
- index.php
- api/router.php

**Required Fix:** Remove health endpoint from one location (recommend keeping in router.php for consistency with other API endpoints)

**Acceptance Test:**
1. GET /api/health returns success
2. Response format matches expected structure
3. No duplicate code execution

**Regression Test:**
1. Verify health endpoint still works
2. Verify no other endpoints affected
3. Verify response format unchanged

---

### BOOT-005: BaseController Constructor Parameter Not Used

**Issue ID:** BOOT-005
**Priority:** LOW
**Status:** OPEN
**Description:** BaseController::__construct() accepts $service parameter but doesn't validate it or enforce type

**Evidence:**
```php
// api/controllers/BaseController.php:6-8
public function __construct($service) {
    $this->service = $service;
}
```

**File:** `api/controllers/BaseController.php`
**Line Numbers:** 6-8

**Dependency Trace:**
- All controllers pass service instance to BaseController
- No type checking or validation
- No null check

**Root Cause:** Missing type safety. Could pass null or wrong type without error until method call.

**Files Allowed To Modify:**
- api/controllers/BaseController.php

**Required Fix:** Add type hint: `public function __construct(BaseService $service)`

**Acceptance Test:**
1. Passing non-BaseService object throws type error
2. Passing null throws type error
3. Passing valid BaseService works

**Regression Test:**
1. Test all controllers
2. Verify no breaking changes
3. Verify all controllers still work

---

## SUMMARY

**Total Issues:** 5
**Critical:** 2 (BOOT-001, BOOT-002)
**High:** 1 (BOOT-003)
**Low:** 2 (BOOT-004, BOOT-005)

**Production Blockers:**
- BOOT-001: BaseService constructor mismatch — likely causing production errors
- BOOT-002: Model property type conflict — likely causing production errors

**Recommended Fix Order:**
1. BOOT-001 (Service constructor) — Blocks all API endpoints
2. BOOT-002 (Model property types) — Blocks model instantiation
3. BOOT-003 (Missing include) — Code quality
4. BOOT-004 (Duplicate health) — Code cleanup
5. BOOT-005 (Type safety) — Code quality

---

## DEPENDENCY GRAPH

```
index.php
├── error_boundary.php
├── public/index.php (SPA)
└── api/bootstrap.php
    ├── config.php
    ├── api/router.php
    │   ├── Response.php
    │   └── Controllers (22 files)
    │       └── Services (19 files)
    │           └── Models (29 files)
    └── Autoloader (controllers/, services/, models/, utils/)
```

---

## FILES REQUIRING IMMEDIATE ATTENTION

1. `api/services/BaseService.php` — Constructor signature
2. `api/models/Model.php` — Property typing
3. `api/controllers/ResumesController.php` — Missing include

---

**END OF AUDIT REPORT**
