# AUDIT REPORT: Subsystem 04 — CONTROLLERS

**Audit Date:** 2026-07-24
**Auditor:** Repository Auditor
**Subsystem:** CONTROLLERS

---

## CONTROLLER INVENTORY

**Controllers Found (22 files):**
1. BaseController.php (base class)
2. CompaniesController.php
3. ApplicationsController.php
4. JobsController.php
5. ProspectsController.php
6. RecruitersController.php
7. ConversationsController.php
8. DocumentsController.php
9. InterviewsController.php
10. CoverLettersController.php
11. ResumesController.php
12. AdminController.php
13. AnalyticsController.php
14. ExportController.php
15. HealthController.php
16. LinkedinStatsController.php
17. ProspectController.php (duplicate)
18. SavedSearchesController.php
19. SettingsController.php
20. TailoredResumesController.php
21. TargetCompaniesController.php
22. UsersController.php

---

## ISSUES FOUND

### CTRL-001: AdminController Missing Service Dependency

**Issue ID:** CTRL-001
**Priority:** CRITICAL
**Status:** OPEN
**Description:** AdminController extends BaseController but doesn't initialize service, calls undefined $this->method property and $this->success() method

**Evidence:**
```php
// api/controllers/AdminController.php:2-6
require_once __DIR__ . '/BaseController.php';
class AdminController extends BaseController {
    public function handle(): void {
        $this->success(['message' => 'AdminController ready', 'method' => $this->method]);
    }
}
```

**File path:** `api/controllers/AdminController.php`
**Line numbers:** 2-6

**Function/class:** AdminController::handle()

**Dependency trace:**
- GET /api/admin → AdminController::handle()
- $this->method property doesn't exist in BaseController
- $this->success() method doesn't exist in BaseController
- Fatal error: "Undefined property: BaseController::$method"
- Fatal error: "Call to undefined method BaseController::success()"

**Root cause:** AdminController is incomplete/stub, not properly implemented

**Production impact:** CRITICAL — GET /api/admin will fail with fatal error

**Allowed files to modify:**
- api/controllers/AdminController.php

**Required fix:** Either remove AdminController or implement properly with service dependency and correct method calls

**Acceptance test:**
1. GET /api/admin returns success or 404 if removed

**Regression test:**
1. Verify no breaking changes to other endpoints

---

### CTRL-002: HealthController Calls Undefined Method

**Issue ID:** CTRL-002
**Priority:** CRITICAL
**Status:** OPEN
**Description:** HealthController calls $this->success() method which doesn't exist in BaseController

**Evidence:**
```php
// api/controllers/HealthController.php:26
$this->success($health, 'System healthy');

// BaseController has no success() method
// BaseController methods: list(), get(), create(), update(), delete()
```

**File path:** `api/controllers/HealthController.php`
**Line numbers:** 26

**Function/class:** HealthController::handle()

**Dependency trace:**
- GET /api/health → HealthController::handle()
- handle() calls $this->success()
- success() method doesn't exist in BaseController
- Fatal error: "Call to undefined method BaseController::success()"

**Root cause:** HealthController copied from different codebase with different base class

**Production impact:** CRITICAL — GET /api/health will fail with fatal error

**Allowed files to modify:**
- api/controllers/HealthController.php

**Required fix:** Replace $this->success() with Response::json(Response::success($health))

**Acceptance test:**
1. GET /api/health returns success
2. Response format matches expected structure

**Regression test:**
1. Verify health endpoint works
2. Verify database connection check works

---

### CTRL-003: HealthController Uses SQLite Query on MySQL Database

**Issue ID:** CTRL-003
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
- handle() → checkDatabase()
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

### CTRL-004: ProspectController Missing Service Dependency

**Issue ID:** CTRL-004
**Priority:** CRITICAL
**Status:** OPEN
**Description:** ProspectController extends BaseController but doesn't initialize service, calls undefined $this->method property and $this->success() method

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
- $this->success() method doesn't exist in BaseController
- Fatal error: "Undefined property: BaseController::$method"
- Fatal error: "Call to undefined method BaseController::success()"

**Root cause:** ProspectController is incomplete/stub, conflicts with ProspectsController

**Production impact:** CRITICAL — GET /api/prospect will fail with fatal error

**Allowed files to modify:**
- api/controllers/ProspectController.php

**Required fix:** Remove ProspectController.php (duplicate of ProspectsController)

**Acceptance test:**
1. GET /api/prospect returns 404 (controller not found)
2. GET /api/prospects still works

**Regression test:**
1. Verify ProspectsController still works
2. Verify no breaking changes

---

### CTRL-005: ApplicationsController Missing Service Include

**Issue ID:** CTRL-005
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

### CTRL-006: ProspectsController Missing Service Include

**Issue ID:** CTRL-006
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ProspectsController instantiates ProspectService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/ProspectsController.php:3-6
class ProspectsController extends BaseController {
    public function __construct() {
        $service = new ProspectService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/ProspectsController.php`
**Line numbers:** 3-6

**Function/class:** ProspectsController::__construct()

**Dependency trace:**
- GET /api/prospects → ProspectsController
- ProspectsController instantiates ProspectService
- ProspectService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/ProspectsController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/ProspectService.php';`

**Acceptance test:**
1. GET /api/prospects works
2. ProspectService loads correctly

**Regression test:**
1. Verify all prospect endpoints work
2. Verify no breaking changes

---

### CTRL-007: ConversationsController Missing Service Include

**Issue ID:** CTRL-007
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

### CTRL-008: DocumentsController Missing Service Include

**Issue ID:** CTRL-008
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

### CTRL-009: CoverLettersController Missing CoverLetterService Include

**Issue ID:** CTRL-009
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

### CTRL-010: ResumesController Missing ResumeService Include

**Issue ID:** CTRL-010
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ResumesController instantiates ResumeService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/ResumesController.php:8-11
public function __construct() {
    $service = new ResumeService();
    parent::__construct($service);
    $this->aiService = new AIService();
}
```

**File path:** `api/controllers/ResumesController.php`
**Line numbers:** 8-11

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

### CTRL-011: ExportController Missing Service Include

**Issue ID:** CTRL-011
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

### CTRL-012: LinkedinStatsController Missing Service Include

**Issue ID:** CTRL-012
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

### CTRL-013: SavedSearchesController Missing Service Include

**Issue ID:** CTRL-013
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

**Function/class:** SavedSearchesController::__construct()

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

### CTRL-014: SettingsController Missing Service Include

**Issue ID:** CTRL-014
**Priority:** MEDIUM
**Status:** OPEN
**Description:** SettingsController instantiates SettingsService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/SettingsController.php:3-6
class SettingsController extends BaseController {
    public function __construct() {
        $service = new SettingsService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/SettingsController.php`
**Line numbers:** 3-6

**Function/class:** SettingsController::__construct()

**Dependency trace:**
- GET /api/settings → SettingsController
- SettingsController instantiates SettingsService
- SettingsService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/SettingsController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/SettingsService.php';`

**Acceptance test:**
1. GET /api/settings works
2. SettingsService loads correctly

**Regression test:**
1. Verify all settings endpoints work
2. Verify no breaking changes

---

### CTRL-015: TailoredResumesController Missing Service Include

**Issue ID:** CTRL-015
**Priority:** MEDIUM
**Status:** OPEN
**Description:** TailoredResumesController instantiates ResumeService but doesn't include it explicitly (relies on autoloader)

**Evidence:**
```php
// api/controllers/TailoredResumesController.php:3-6
class TailoredResumesController extends BaseController {
    public function __construct() {
        $service = new ResumeService();
        parent::__construct($service);
    }
}
```

**File path:** `api/controllers/TailoredResumesController.php`
**Line numbers:** 3-6

**Function/class:** TailoredResumesController::__construct()

**Dependency trace:**
- GET /api/tailored-resumes → TailoredResumesController
- TailoredResumesController instantiates ResumeService
- ResumeService loaded via autoloader

**Root cause:** Inconsistent with other controllers that explicitly include services

**Production impact:** LOW — Autoloader handles it, but inconsistent code style

**Allowed files to modify:**
- api/controllers/TailoredResumesController.php

**Required fix:** Add explicit include: `require_once ROOT_PATH . '/api/services/ResumeService.php';`

**Acceptance test:**
1. GET /api/tailored-resumes works
2. ResumeService loads correctly

**Regression test:**
1. Verify all tailored-resumes endpoints work
2. Verify no breaking changes

---

### CTRL-016: TargetCompaniesController Missing Service Include

**Issue ID:** CTRL-016
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

### CTRL-017: ProspectsController batchDiscard() Missing Response on Error

**Issue ID:** CTRL-017
**Priority:** LOW
**Status:** OPEN
**Description:** ProspectsController::batchDiscard() returns error response but doesn't exit, execution continues

**Evidence:**
```php
// api/controllers/ProspectsController.php:9-18
public function batchDiscard() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['ids'])) {
        Response::json(Response::error('Invalid input: ids required'));
    }
    $result = $this->service->batchDiscard($data['ids']);
    if (!$result) {
        Response::json(Response::error('Failed to discard prospects'));
    }
    Response::json(Response::success(['discarded' => true]));
}
```

**File path:** `api/controllers/ProspectsController.php`
**Line numbers:** 9-18

**Function/class:** ProspectsController::batchDiscard()

**Dependency trace:**
- POST /api/prospects/batch-discard → ProspectsController::batchDiscard()
- If validation fails, error response sent but execution continues
- May send second response (success or error)

**Root cause:** Missing return statement after error response

**Production impact:** LOW — May cause "Headers already sent" or duplicate response errors

**Allowed files to modify:**
- api/controllers/ProspectsController.php

**Required fix:** Add return statements after error responses:
```php
if (!$data || !isset($data['ids'])) {
    Response::json(Response::error('Invalid input: ids required'));
    return;
}
```

**Acceptance test:**
1. POST /api/prospects/batch-discard with invalid data returns error
2. No duplicate responses
3. Valid data processes correctly

**Regression test:**
1. Verify batch discard works
2. Verify error handling works
3. Verify no breaking changes

---

### CTRL-018: ProspectsController review() Parameter Mismatch

**Issue ID:** CTRL-018
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

### CTRL-019: ExportController list() Overridden to Create Export

**Issue ID:** CTRL-019
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

**Total Issues:** 19
**Critical:** 4 (CTRL-001, CTRL-002, CTRL-003, CTRL-004)
**Medium:** 12 (CTRL-005 through CTRL-016)
**Low:** 3 (CTRL-017, CTRL-018, CTRL-019)

**Production Blockers:**
- CTRL-001: AdminController missing service — Blocks /api/admin
- CTRL-002: HealthController calls undefined method — Blocks /api/health
- CTRL-003: HealthController SQLite query on MySQL — Blocks /api/health
- CTRL-004: ProspectController missing service — Blocks /api/prospect

**Recommended Fix Order:**
1. CTRL-001 (AdminController) — Blocks admin endpoint
2. CTRL-002 (HealthController method) — Blocks health endpoint
3. CTRL-003 (HealthController SQLite) — Blocks health endpoint
4. CTRL-004 (ProspectController) — Blocks prospect endpoint
5. CTRL-005 through CTRL-016 (Missing includes) — Code quality
6. CTRL-017 (batchDiscard return) — Code quality
7. CTRL-018 (review() parameter) — Code quality
8. CTRL-019 (ExportController list()) — Code quality

---

## CONTROLLERS WITH PROPER INCLUDES

**Controllers with explicit includes (working correctly):**
- CompaniesController.php ✓
- JobsController.php ✓
- RecruitersController.php ✓
- InterviewsController.php ✓
- AnalyticsController.php ✓
- UsersController.php ✓

**Controllers missing includes (need fixing):**
- ApplicationsController.php
- ProspectsController.php
- ConversationsController.php
- DocumentsController.php
- CoverLettersController.php
- ResumesController.php
- ExportController.php
- LinkedinStatsController.php
- SavedSearchesController.php
- SettingsController.php
- TailoredResumesController.php
- TargetCompaniesController.php

---

**END OF AUDIT REPORT**
