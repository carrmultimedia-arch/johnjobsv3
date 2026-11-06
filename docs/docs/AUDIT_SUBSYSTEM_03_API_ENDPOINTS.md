# AUDIT REPORT: Subsystem 03 — API ENDPOINTS

**Audit Date:** 2026-07-24
**Auditor:** Repository Auditor
**Subsystem:** API ENDPOINTS

---

## ROUTER ANALYSIS

**Router Pattern:** `/api/{resource}[/{id}[/{action}]]`

**HTTP Method Mapping:**
- GET /api/{resource} → controller->list()
- POST /api/{resource} → controller->create()
- GET /api/{resource}/{id} → controller->get(id)
- PUT /api/{resource}/{id} → controller->update(id)
- DELETE /api/{resource}/{id} → controller->delete(id)
- POST /api/{resource}/{action} → controller->{action}()
- POST /api/{resource}/{id}/{action} → controller->{action}(id)

---

## CONTROLLER INVENTORY

**Controllers Found (22 files):**
1. AdminController.php
2. AnalyticsController.php
3. ApplicationsController.php
4. BaseController.php
5. CompaniesController.php
6. ConversationsController.php
7. CoverLettersController.php
8. DocumentsController.php
9. ExportController.php
10. HealthController.php
11. InterviewsController.php
12. JobsController.php
13. LinkedinStatsController.php
14. ProspectController.php
15. ProspectsController.php
16. RecruitersController.php
17. ResumesController.php
18. SavedSearchesController.php
19. SettingsController.php
20. TailoredResumesController.php
21. TargetCompaniesController.php
22. UsersController.php

---

## ISSUES FOUND

### API-001: HealthController Uses SQLite Query on MySQL Database

**Issue ID:** API-001
**Priority:** CRITICAL
**Status:** OPEN
**Description:** HealthController::checkDatabase() uses SQLite-specific query (sqlite_master) but config.php specifies MySQL database

**Evidence:**
```php
// api/controllers/HealthController.php:32
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'");

// config.php:4-6
'db_host' => 'mysql.s413.sureserver.com',
'db_port' => 3306,
'db_name' => 'carrmulti_jobhunt',
```

**File path:** `api/controllers/HealthController.php`
**Line numbers:** 29-40

**Function/class:** HealthController::checkDatabase()

**Dependency trace:**
- GET /api/health → HealthController::handle()
- HealthController::handle() → checkDatabase()
- checkDatabase() → SQLite query on MySQL database
- Query fails with "Table 'sqlite_master' doesn't exist"

**Root cause:** HealthController copied from SQLite project, not updated for MySQL

**Production impact:** CRITICAL — GET /api/health will fail with database error

**Allowed files to modify:**
- api/controllers/HealthController.php

**Required fix:** Replace SQLite query with MySQL equivalent:
```sql
SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()
```

**Acceptance test:**
1. GET /api/health returns success
2. database.connected = true
3. database.driver = 'mysql'

**Regression test:**
1. Verify health endpoint works
2. Verify database connection check works

---

### API-002: AdminController Missing Service Dependency

**Issue ID:** API-002
**Priority:** HIGH
**Status:** OPEN
**Description:** AdminController extends BaseController but doesn't initialize service, calls undefined $this->method property

**Evidence:**
```php
// api/controllers/AdminController.php:3-6
require_once __DIR__ . '/BaseController.php';
class AdminController extends BaseController {
    public function handle(): void {
        $this->success(['message' => 'AdminController ready', 'method' => $this->method]);
    }
}
```

**File path:** `api/controllers/AdminController.php`
**Line numbers:** 3-6

**Function/class:** AdminController::handle()

**Dependency trace:**
- GET /api/admin → AdminController::handle()
- $this->method property doesn't exist in BaseController
- Fatal error: "Undefined property: BaseController::$method"

**Root cause:** AdminController is incomplete/stub, not properly implemented

**Production impact:** HIGH — GET /api/admin will fail with fatal error

**Allowed files to modify:**
- api/controllers/AdminController.php

**Required fix:** Either remove AdminController or implement properly with service dependency

**Acceptance test:**
1. GET /api/admin returns success or 404 if removed

**Regression test:**
1. Verify no breaking changes to other endpoints

---

### API-003: ProspectController Missing Service Dependency

**Issue ID:** API-003
**Priority:** HIGH
**Status:** OPEN
**Description:** ProspectController extends BaseController but doesn't initialize service, calls undefined $this->method property

**Evidence:**
```php
// api/controllers/ProspectController.php:2-6
require_once __DIR__ . '/BaseController.php';
class ProspectController extends BaseController {
    public function handle(): void {
        $this->success(['message' => 'ProspectController ready', 'method' => $this->method]);
    }
}
```

**File path:** `api/controllers/ProspectController.php`
**Line numbers:** 2-6

**Function/class:** ProspectController::handle()

**Dependency trace:**
- GET /api/prospect → ProspectController::handle()
- $this->method property doesn't exist in BaseController
- Fatal error: "Undefined property: BaseController::$method"

**Root cause:** ProspectController is incomplete/stub, conflicts with ProspectsController

**Production impact:** HIGH — GET /api/prospect will fail with fatal error

**Allowed files to modify:**
- api/controllers/ProspectController.php

**Required fix:** Remove ProspectController (duplicate of ProspectsController)

**Acceptance test:**
1. GET /api/prospect returns 404 (controller not found)
2. GET /api/prospects still works

**Regression test:**
1. Verify ProspectsController still works
2. Verify no breaking changes

---

### API-004: ProspectController Conflicts with ProspectsController

**Issue ID:** API-004
**Priority:** HIGH
**Status:** OPEN
**Description:** Two controllers for same resource: ProspectController (singular) and ProspectsController (plural). Router will route /api/prospect to ProspectController, /api/prospects to ProspectsController

**Evidence:**
```php
// api/controllers/ProspectController.php (singular, broken)
// api/controllers/ProspectsController.php (plural, working)

// Router pattern: /api/{resource}
// /api/prospect → ProspectController
// /api/prospects → ProspectsController
```

**File path:** `api/controllers/ProspectController.php`
**Line numbers:** 1-7

**Function/class:** ProspectController

**Dependency trace:**
- Frontend calls /api/prospects (plural)
- Router routes to ProspectsController (correct)
- ProspectController (singular) is unused and broken

**Root cause:** Duplicate controller created, one not cleaned up

**Production impact:** MEDIUM — No direct impact if frontend uses plural, but confusing and error-prone

**Allowed files to modify:**
- api/controllers/ProspectController.php

**Required fix:** Remove ProspectController.php

**Acceptance test:**
1. GET /api/prospects still works
2. GET /api/prospect returns 404

**Regression test:**
1. Verify all prospect endpoints work
2. Verify no breaking changes

---

### API-005: TailoredResumesController Uses ResumeService

**Issue ID:** API-005
**Priority:** MEDIUM
**Status:** OPEN
**Description:** TailoredResumesController uses ResumeService instead of a dedicated TailoredResumeService, creating confusion about resource mapping

**Evidence:**
```php
// api/controllers/TailoredResumesController.php:4-6
class TailoredResumesController extends BaseController {
    public function __construct() {
        $service = new ResumeService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/TailoredResumesController.php`
**Line numbers:** 4-6

**Function/class:** TailoredResumesController

**Dependency trace:**
- GET /api/tailored-resumes → TailoredResumesController
- TailoredResumesController uses ResumeService
- ResumeService queries 'resumes' table
- Frontend expects tailored-resumes to be separate resource

**Root cause:** TailoredResumesController is alias for ResumeService, not separate resource

**Production impact:** LOW — Works but confusing, may cause data mixing

**Allowed files to modify:**
- api/controllers/TailoredResumesController.php
- api/services/ResumeService.php

**Required fix:** Either remove TailoredResumesController or create dedicated TailoredResumeService with separate table

**Acceptance test:**
1. GET /api/tailored-resumes returns data
2. Data is correctly scoped to tailored resumes only

**Regression test:**
1. Verify no breaking changes to resumes
2. Verify frontend still works

---

### API-006: ApplicationsController Missing Service Include

**Issue ID:** API-006
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ApplicationsController instantiates ApplicationService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/ApplicationsController.php:3-6
class ApplicationsController extends BaseController {
    public function __construct() {
        $service = new ApplicationService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/ApplicationsController.php`
**Line numbers:** 3-6

**Function/class:** ApplicationsController::__construct()

**Dependency trace:**
- GET /api/applications → ApplicationsController
- ApplicationsController instantiates ApplicationService
- ApplicationService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/ApplicationsController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/ApplicationService.php';`

**Acceptance test:**
1. GET /api/applications works
2. ApplicationService loads correctly

**Regression test:**
1. Verify all application endpoints work
2. Verify no breaking changes

---

### API-007: ConversationsController Missing Service Include

**Issue ID:** API-007
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ConversationsController instantiates ConversationService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/ConversationsController.php:3-6
class ConversationsController extends BaseController {
    public function __construct() {
        $service = new ConversationService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/ConversationsController.php`
**Line numbers:** 3-6

**Function/class:** ConversationsController::__construct()

**Dependency trace:**
- GET /api/conversations → ConversationsController
- ConversationsController instantiates ConversationService
- ConversationService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/ConversationsController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/ConversationService.php';`

**Acceptance test:**
1. GET /api/conversations works
2. ConversationService loads correctly

**Regression test:**
1. Verify all conversation endpoints work
2. Verify no breaking changes

---

### API-008: DocumentsController Missing Service Include

**Issue ID:** API-008
**Priority:** MEDIUM
**Status:** OPEN
**Description:** DocumentsController instantiates DocumentService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/DocumentsController.php:3-6
class DocumentsController extends BaseController {
    public function __construct() {
        $service = new DocumentService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/DocumentsController.php`
**Line numbers:** 3-6

**Function/class:** DocumentsController::__construct()

**Dependency trace:**
- GET /api/documents → DocumentsController
- DocumentsController instantiates DocumentService
- DocumentService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/DocumentsController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/DocumentService.php';`

**Acceptance test:**
1. GET /api/documents works
2. DocumentService loads correctly

**Regression test:**
1. Verify all document endpoints work
2. Verify no breaking changes

---

### API-009: ExportController Missing Service Include

**Issue ID:** API-009
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ExportController instantiates ExportService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/ExportController.php:3-6
class ExportController extends BaseController {
    public function __construct() {
        $service = new ExportService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/ExportController.php`
**Line numbers:** 3-6

**Function/class:** ExportController::__construct()

**Dependency trace:**
- GET /api/export → ExportController
- ExportController instantiates ExportService
- ExportService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/ExportController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/ExportService.php';`

**Acceptance test:**
1. GET /api/export works
2. ExportService loads correctly

**Regression test:**
1. Verify export functionality works
2. Verify no breaking changes

---

### API-010: LinkedinStatsController Missing Service Include

**Issue ID:** API-010
**Priority:** MEDIUM
**Status:** OPEN
**Description:** LinkedinStatsController instantiates LinkedInStatService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/LinkedinStatsController.php:3-6
class LinkedinStatsController extends BaseController {
    public function __construct() {
        $service = new LinkedInStatService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/LinkedinStatsController.php`
**Line numbers:** 3-6

**Function/class:** LinkedinStatsController::__construct()

**Dependency trace:**
- GET /api/linkedin-stats → LinkedinStatsController
- LinkedinStatsController instantiates LinkedInStatService
- LinkedInStatService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/LinkedinStatsController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/LinkedInStatService.php';`

**Acceptance test:**
1. GET /api/linkedin-stats works
2. LinkedInStatService loads correctly

**Regression test:**
1. Verify all linkedin-stats endpoints work
2. Verify no breaking changes

---

### API-011: SavedSearchesController Missing Service Include

**Issue ID:** API-011
**Priority:** MEDIUM
**Status:** OPEN
**Description:** SavedSearchesController instantiates SavedSearchService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/SavedSearchesController.php:3-6
class SavedSearchesController extends BaseController {
    public function __construct() {
        $service = new SavedSearchService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/SavedSearchesController.php`
**Line numbers:** 3-6

**TargetCompaniesController::__construct()**

**Dependency trace:**
- GET /api/saved-searches → SavedSearchesController
- SavedSearchesController instantiates SavedSearchService
- SavedSearchService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/SavedSearchesController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/SavedSearchService.php';`

**Acceptance test:**
1. GET /api/saved-searches works
2. SavedSearchService loads correctly

**Regression test:**
1. Verify all saved-searches endpoints work
2. Verify no breaking changes

---

### API-012: TargetCompaniesController Missing Service Include

**Issue ID:** API-012
**Priority:** MEDIUM
**Status:** OPEN
**Description:** TargetCompaniesController instantiates TargetCompanyService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/TargetCompaniesController.php:3-6
class TargetCompaniesController extends BaseController {
    public function __construct() {
        $service = new TargetCompanyService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/TargetCompaniesController.php`
**Line numbers:** 3-6

**Function/class:** TargetCompaniesController::__construct()

**Dependency trace:**
- GET /api/target-companies → TargetCompaniesController
- TargetCompaniesController instantiates TargetCompanyService
- TargetCompanyService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/TargetCompaniesController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/TargetCompanyService.php';`

**Acceptance test:**
1. GET /api/target-companies works
2. TargetCompanyService loads correctly

**Regression test:**
1. Verify all target-companies endpoints work
2. Verify no breaking changes

---

### API-013: ResumesController Missing Service Include

**Issue ID:** API-013
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ResumesController instantiates ResumeService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/ResumesController.php:8-10
public function __construct() {
    $service = new ResumeService();
    parent::__construct($service);
    $this->aiService = new AIService();
}
```

**File path:** `api/controllers/ResumesController.php`
**Line numbers:** 8-10

**Function/class:** ResumesController::__construct()

**Dependency trace:**
- GET /api/resumes → ResumesController
- ResumesController instantiates ResumeService and AIService
- Both loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/ResumesController.php

**Required fix:** Add explicit includes:
```php
require_once ROOT_PATH . '/api/services/ResumeService.php';
require_once ROOT_PATH . '/api/services/AIService.php';
```

**Acceptance test:**
1. GET /api/resumes works
2. POST /api/resumes/generate works
3. Services load correctly

**Regression test:**
1. Verify all resume endpoints work
2. Verify AI generation works
3. Verify no breaking changes

---

### API-014: CoverLettersController Missing Service Include

**Issue ID:** API-014
**Priority:** MEDIUM
**Status:** OPEN
**Description:** CoverLettersController instantiates CoverLetterService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/CoverLettersController.php:8-11
public function __construct() {
    $service = new CoverLetterService();
    parent::__construct($service);
    $this->aiService = new AIService();
}
```

**File path:** `api/controllers/CoverLettersController.php`
**Line numbers:** 8-11

**Function/class:** CoverLettersController::__construct()

**Dependency trace:**
- GET /api/cover-letters → CoverLettersController
- CoverLettersController instantiates CoverLetterService and AIService
- Both loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/CoverLettersController.php

**Required fix:** Add explicit includes:
```php
require_once ROOT_PATH . '/api/services/CoverLetterService.php';
require_once ROOT_PATH . '/api/services/AIService.php';
```

**Acceptance test:**
1. GET /api/cover-letters works
2. POST /api/cover-letters/generate works
3. Services load correctly

**Regression test:**
1. Verify all cover-letter endpoints work
2. Verify AI generation works
3. Verify no breaking changes

---

### API-015: ProspectsController review() Action Missing ID Parameter

**Issue ID:** API-015
**Priority:** LOW
**Status:** OPEN
**Description:** ProspectsController::review($id) expects $id parameter but router passes it as action, not parameter

**Evidence:**
```php
// api/controllers/ProspectsController.php:21-26
public function review($id) {
    $result = $this->service->review($id);
    if (!$result) {
        Response::json(Response::error('Failed to mark as reviewed'));
    }
    Response::json(Response::success($this->service->getById($id)));
}

// Frontend call (public/index.php:1698)
await apiPost('prospects/review', { id: currentProspectId });
```

**File path:** `api/controllers/ProspectsController.php`
**Line numbers:** 21-26

**Function/class:** ProspectsController::review()

**Dependency trace:**
- Frontend: POST /api/prospects/review with { id: currentProspectId }
- Router: /api/prospects/review → ProspectsController::review()
- Controller expects review($id) but router calls review() with no parameters
- ID is in request body, not URL

**Root cause:** Controller expects URL parameter but frontend sends in request body

**Production impact:** LOW — Works because controller reads from request body, not parameter

**Allowed files to modify:**
- api/controllers/ProspectsController.php

**Required fix:** Change review() to read ID from request body:
```php
public function review() {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    // ... rest of method
}
```

**Acceptance test:**
1. POST /api/prospects/review with { id: 1 } works
2. Prospect marked as reviewed

**Regression test:**
1. Verify prospect review workflow works
2. Verify no breaking changes

---

### API-016: ExportController list() Overridden to Create Export

**Issue ID:** API-016
**Priority:** LOW
**Status:** OPEN
**Description:** ExportController::list() is overridden to create export instead of listing exports, breaking standard CRUD pattern

**Evidence:**
```php
// api/controllers/ExportController.php:9-40
public function list() {
    $data = $this->service->exportAll();
    // ... creates export and saves to database
}

// Standard BaseController::list() would call $this->service->getAll()
```

**File path:** `api/controllers/ExportController.php`
**Line numbers:** 9-40

**Function/class:** ExportController::list()

**Dependency trace:**
- GET /api/export → ExportController::list()
- list() creates export instead of listing
- Breaks expected RESTful pattern

**Root cause:** ExportController repurposes list() for create operation

**Production impact:** LOW — Works but non-standard, confusing

**Allowed files to modify:**
- api/controllers/ExportController.php

**Required fix:** Rename list() to create() or add dedicated export() action

**Acceptance test:**
1. GET /api/export creates export
2. Or POST /api/export creates export (if renamed)

**Regression test:**
1. Verify export functionality works
2. Verify frontend still calls correct endpoint

---

## SUMMARY

**Total Issues:** 16
**Critical:** 1 (API-001)
**High:** 3 (API-002, API-003, API-004)
**Medium:** 11 (API-005 through API-014)
**Low:** 2 (API-015, API-016)

**Production Blockers:**
- API-001: HealthController SQLite query on MySQL — Blocks health endpoint
- API-002: AdminController missing service — Blocks admin endpoint
- API-003: ProspectController missing service — Blocks prospect endpoint

**Recommended Fix Order:**
1. API-001 (HealthController SQLite) — Blocks health check
2. API-002 (AdminController) — Blocks admin endpoint
3. API-003 (ProspectController) — Blocks prospect endpoint
4. API-004 (Remove ProspectController) — Cleanup
5. API-005 through API-014 (Missing includes) — Code quality
6. API-015 (review() parameter) — Code quality
7. API-016 (ExportController list()) — Code quality

---

## FRONTEND API CALLS MAPPING

**Frontend calls found in public/index.php:**
- apiGet('applications') → /api/applications ✓
- apiGet('prospects') → /api/prospects ✓
- apiGet('documents') → /api/documents ✓
- apiGet('conversations') → /api/conversations ✓
- apiGet('tailored-resumes') → /api/tailored-resumes ✓
- apiGet('cover-letters') → /api/cover-letters ✓
- apiGet('saved-searches') → /api/saved-searches ✓
- apiGet('target-companies') → /api/target-companies ✓
- apiGet('settings') → /api/settings ✓
- apiPost('applications') → /api/applications ✓
- apiPut('applications/{id}') → /api/applications/{id} ✓
- apiDelete('applications/{id}') → /api/applications/{id} ✓
- apiPost('prospects/batch-discard') → /api/prospects/batch-discard ✓
- apiPost('prospects/review') → /api/prospects/review ✓
- apiPut('prospects/{id}') → /api/prospects/{id} ✓
- apiDelete('prospects/{id}') → /api/prospects/{id} ✓
- apiPut('documents/{id}') → /api/documents/{id} ✓
- apiDelete('documents/{id}') → /api/documents/{id} ✓
- apiPost('settings') → /api/settings ✓

**All frontend API calls have matching controllers.**

---

**END OF AUDIT REPORT**
