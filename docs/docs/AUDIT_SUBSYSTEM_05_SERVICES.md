# AUDIT REPORT: Subsystem 05 — SERVICES

**Audit Date:** 2026-07-24
**Auditor:** Repository Auditor
**Subsystem:** SERVICES

---

## SERVICE INVENTORY

**Services Found (19 files):**
1. BaseService.php (base class)
2. CompanyService.php
3. JobService.php
4. UserService.php
5. InterviewService.php
6. RecruiterService.php
7. AnalyticsService.php
8. ApplicationService.php
9. ResumeService.php
10. SettingsService.php
11. CoverLetterService.php
12. ConversationService.php
13. DocumentService.php
14. ProspectService.php
15. SavedSearchService.php
16. TargetCompanyService.php
17. LinkedInStatService.php
18. ExportService.php
19. AIService.php

---

## ISSUES FOUND

### SVC-001: BaseService Constructor Parameter Mismatch

**Issue ID:** SVC-001
**Priority:** CRITICAL
**Status:** OPEN
**Description:** BaseService::__construct() accepts zero parameters, but 6 child services pass parameters to parent constructor

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
    parent::__construct($this->model->getTable());
}

// Same pattern in: JobService, UserService, InterviewService, RecruiterService, AnalyticsService
```

**File path:** `api/services/BaseService.php`
**Line numbers:** 7-9

**Function/class:** BaseService::__construct()

**Dependency trace:**
- CompanyService, JobService, UserService, InterviewService, RecruiterService, AnalyticsService all call parent::__construct($this->model->getTable())
- BaseService::__construct() accepts zero parameters
- PHP fatal error: "Too many arguments passed to BaseService::__construct()"

**Root cause:** BaseService constructor signature doesn't match child service expectations

**Production impact:** CRITICAL — Any API request using these services will fail with fatal error

**Allowed files to modify:**
- api/services/BaseService.php
- api/services/CompanyService.php
- api/services/JobService.php
- api/services/UserService.php
- api/services/InterviewService.php
- api/services/RecruiterService.php
- api/services/AnalyticsService.php

**Required fix:** Option 1: Add optional parameter to BaseService constructor:
```php
public function __construct($table = null) {
    $this->db = DB::getInstance();
    if ($table) {
        $this->table = $table;
    }
}
```

Option 2: Remove parameter passing from child services and set $table property directly

**Acceptance test:**
1. GET /api/companies returns data without error
2. GET /api/jobs returns data without error
3. All affected services work correctly

**Regression test:**
1. Verify all CRUD operations work
2. Verify no breaking changes to other services

---

### SVC-002: CompanyService Constructor Calls Parent with Parameter

**Issue ID:** SVC-002
**Priority:** CRITICAL
**Status:** OPEN
**Description:** CompanyService passes parameter to parent::__construct() but BaseService accepts no parameters

**Evidence:**
```php
// api/services/CompanyService.php:10-14
public function __construct()
{
    $this->model = new CompanyModel();
    parent::__construct($this->model->getTable());
}
```

**File path:** `api/services/CompanyService.php`
**Line numbers:** 10-14

**Function/class:** CompanyService::__construct()

**Dependency trace:**
- CompaniesController instantiates CompanyService
- CompanyService passes parameter to parent constructor
- BaseService constructor rejects parameter
- Fatal error: "Too many arguments passed to BaseService::__construct()"

**Root cause:** SVC-001 - BaseService constructor signature mismatch

**Production impact:** CRITICAL — GET /api/companies fails with fatal error

**Allowed files to modify:**
- api/services/CompanyService.php

**Required fix:** Remove parameter passing after fixing BaseService constructor (SVC-001)

**Acceptance test:**
1. GET /api/companies returns data
2. CompanyService instantiates correctly

**Regression test:**
1. Verify company CRUD operations work
2. Verify no breaking changes

---

### SVC-003: JobService Constructor Calls Parent with Parameter

**Issue ID:** SVC-003
**Priority:** CRITICAL
**Status:** OPEN
**Description:** JobService passes parameter to parent::__construct() but BaseService accepts no parameters

**Evidence:**
```php
// api/services/JobService.php:10-14
public function __construct()
{
    $this->model = new JobModel();
    parent::__construct($this->model->getTable());
}
```

**File path:** `api/services/JobService.php`
**Line numbers:** 10-14

**Function/class:** JobService::__construct()

**Dependency trace:**
- JobsController instantiates JobService
- JobService passes parameter to parent constructor
- BaseService constructor rejects parameter
- Fatal error: "Too many arguments passed to BaseService::__construct()"

**Root cause:** SVC-001 - BaseService constructor signature mismatch

**Production impact:** CRITICAL — GET /api/jobs fails with fatal error

**Allowed files to modify:**
- api/services/JobService.php

**Required fix:** Remove parameter passing after fixing BaseService constructor (SVC-001)

**Acceptance test:**
1. GET /api/jobs returns data
2. JobService instantiates correctly

**Regression test:**
1. Verify job CRUD operations work
2. Verify no breaking changes

---

### SVC-004: UserService Constructor Calls Parent with Parameter

**Issue ID:** SVC-004
**Priority:** CRITICAL
**Status:** OPEN
**Description:** UserService passes parameter to parent::__construct() but BaseService accepts no parameters

**Evidence:**
```php
// api/services/UserService.php:10-14
public function __construct()
{
    $this->model = new UserModel();
    parent::__construct($this->model->getTable());
}
```

**File path:** `api/services/UserService.php`
**Line numbers:** 10-14

**Function/class:** UserService::__construct()

**Dependency trace:**
- UsersController instantiates UserService
- UserService passes parameter to parent constructor
- BaseService constructor rejects parameter
- Fatal error: "Too many arguments passed to BaseService::__construct()"

**Root cause:** SVC-001 - BaseService constructor signature mismatch

**Production impact:** CRITICAL — GET /api/users fails with fatal error

**Allowed files to modify:**
- api/services/UserService.php

**Required fix:** Remove parameter passing after fixing BaseService constructor (SVC-001)

**Acceptance test:**
1. GET /api/users returns data
2. UserService instantiates correctly

**Regression test:**
1. Verify user CRUD operations work
2. Verify no breaking changes

---

### SVC-005: InterviewService Constructor Calls Parent with Parameter

**Issue ID:** SVC-005
**Priority:** CRITICAL
**Status:** OPEN
**Description:** InterviewService passes parameter to parent::__construct() but BaseService accepts no parameters

**Evidence:**
```php
// api/services/InterviewService.php:10-14
public function __construct()
{
    $this->model = new InterviewModel();
    parent::__construct($this->model->getTable());
}
```

**File path:** `api/services/InterviewService.php`
**Line numbers:** 10-14

**Function/class:** InterviewService::__construct()

**Dependency trace:**
- InterviewsController instantiates InterviewService
- InterviewService passes parameter to parent constructor
- BaseService constructor rejects parameter
- Fatal error: "Too many arguments passed to BaseService::__construct()"

**Root cause:** SVC-001 - BaseService constructor signature mismatch

**Production impact:** CRITICAL — GET /api/interviews fails with fatal error

**Allowed files to modify:**
- api/services/InterviewService.php

**Required fix:** Remove parameter passing after fixing BaseService constructor (SVC-001)

**Acceptance test:**
1. GET /api/interviews returns data
2. InterviewService instantiates correctly

**Regression test:**
1. Verify interview CRUD operations work
2. Verify no breaking changes

---

### SVC-006: RecruiterService Constructor Calls Parent with Parameter

**Issue ID:** SVC-006
**Priority:** CRITICAL
**Status:** OPEN
**Description:** RecruiterService passes parameter to parent::__construct() but BaseService accepts no parameters

**Evidence:**
```php
// api/services/RecruiterService.php:10-14
public function __construct()
{
    $this->model = new RecruiterModel();
    parent::__construct($this->model->getTable());
}
```

**File path:** `api/services/RecruiterService.php`
**Line numbers:** 10-14

**Function/class:** RecruiterService::__construct()

**Dependency trace:**
- RecruitersController instantiates RecruiterService
- RecruiterService passes parameter to parent constructor
- BaseService constructor rejects parameter
- Fatal error: "Too many arguments passed to BaseService::__construct()"

**Root cause:** SVC-001 - BaseService constructor signature mismatch

**Production impact:** CRITICAL — GET /api/recruiters fails with fatal error

**Allowed files to modify:**
- api/services/RecruiterService.php

**Required fix:** Remove parameter passing after fixing BaseService constructor (SVC-001)

**Acceptance test:**
1. GET /api/recruiters returns data
2. RecruiterService instantiates correctly

**Regression test:**
1. Verify recruiter CRUD operations work
2. Verify no breaking changes

---

### SVC-007: AnalyticsService Constructor Calls Parent with Parameter

**Issue ID:** SVC-007
**Priority:** CRITICAL
**Status:** OPEN
**Description:** AnalyticsService passes parameter to parent::__construct() but BaseService accepts no parameters

**Evidence:**
```php
// api/services/AnalyticsService.php:10-14
public function __construct()
{
    $this->model = new AnalyticsModel();
    parent::__construct($this->model->getTable());
}
```

**File path:** `api/services/AnalyticsService.php`
**Line numbers:** 10-14

**Function/class:** AnalyticsService::__construct()

**Dependency trace:**
- AnalyticsController instantiates AnalyticsService
- AnalyticsService passes parameter to parent constructor
- BaseService constructor rejects parameter
- Fatal error: "Too many arguments passed to BaseService::__construct()"

**Root cause:** SVC-001 - BaseService constructor signature mismatch

**Production impact:** CRITICAL — GET /api/analytics/funnel fails with fatal error

**Allowed files to modify:**
- api/services/AnalyticsService.php

**Required fix:** Remove parameter passing after fixing BaseService constructor (SVC-001)

**Acceptance test:**
1. GET /api/analytics/funnel returns data
2. AnalyticsService instantiates correctly

**Regression test:**
1. Verify analytics endpoints work
2. Verify no breaking changes

---

### SVC-008: ApplicationService Missing getById Return Null Check

**Issue ID:** SVC-008
**Priority:** LOW
**Status:** OPEN
**Description:** ApplicationService::getById() doesn't return null if not found, unlike other services

**Evidence:**
```php
// api/services/ApplicationService.php:13-18
public function getById($id) {
    $sql = "SELECT *, applied_at as date_applied FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Compare to CompanyService.php:22-29
public function getById($id)
{
    $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    return $result ?: null;
}
```

**File path:** `api/services/ApplicationService.php`
**Line numbers:** 13-18

**Function/class:** ApplicationService::getById()

**Dependency trace:**
- BaseController::get() calls service->getById()
- ApplicationService returns false if not found
- Other services return null if not found
- Inconsistent behavior

**Root cause:** Inconsistent error handling pattern

**Production impact:** LOW — Works but inconsistent with other services

**Allowed files to modify:**
- api/services/ApplicationService.php

**Required fix:** Add null check:
```php
$result = $stmt->fetch();
return $result ?: null;
```

**Acceptance test:**
1. GET /api/applications/{nonexistent_id} returns error
2. getById() returns null for non-existent ID

**Regression test:**
1. Verify application CRUD operations work
2. Verify no breaking changes

---

### SVC-009: ResumeService Missing getById Method

**Issue ID:** SVC-009
**Priority:** LOW
**Status:** OPEN
**Description:** ResumeService doesn't override getById() method, relies on BaseService which may not handle metadata correctly

**Evidence:**
```php
// api/services/ResumeService.php has no getById() method
// Relies on BaseService::getById() which returns raw row
// ResumeService stores target_role, company in metadata JSON
// getById() should decode metadata for consistency
```

**File path:** `api/services/ResumeService.php`
**Line numbers:** N/A (missing method)

**Function/class:** ResumeService

**Dependency trace:**
- BaseController::get() calls service->getById()
- ResumeService uses BaseService::getById()
- Metadata not decoded on retrieval
- Frontend may expect decoded metadata

**Root cause:** Incomplete implementation

**Production impact:** LOW — Works but metadata not decoded on retrieval

**Allowed files to modify:**
- api/services/ResumeService.php

**Required fix:** Add getById() method that decodes metadata:
```php
public function getById($id) {
    $item = parent::getById($id);
    if ($item && isset($item['metadata'])) {
        $item['metadata'] = json_decode($item['metadata'], true);
    }
    return $item;
}
```

**Acceptance test:**
1. GET /api/resumes/{id} returns decoded metadata
2. Metadata fields accessible directly

**Regression test:**
1. Verify resume CRUD operations work
2. Verify no breaking changes

---

### SVC-010: CoverLetterService Missing getById Method

**Issue ID:** SVC-010
**Priority:** LOW
**Status:** OPEN
**Description:** CoverLetterService doesn't override getById() method, relies on BaseService which may not handle metadata correctly

**Evidence:**
```php
// api/services/CoverLetterService.php has no getById() method
// Relies on BaseService::getById() which returns raw row
// CoverLetterService stores hiring_manager, tone, jd_text in metadata JSON
// getById() should decode metadata for consistency
```

**File path:** `api/services/CoverLetterService.php`
**Line numbers:** N/A (missing method)

**Function/class:** CoverLetterService

**Dependency trace:**
- BaseController::get() calls service->getById()
- CoverLetterService uses BaseService::getById()
- Metadata not decoded on retrieval
- Frontend may expect decoded metadata

**Root cause:** Incomplete implementation

**Production impact:** LOW — Works but metadata not decoded on retrieval

**Allowed files to modify:**
- api/services/CoverLetterService.php

**Required fix:** Add getById() method that decodes metadata:
```php
public function getById($id) {
    $item = parent::getById($id);
    if ($item && isset($item['metadata'])) {
        $item['metadata'] = json_decode($item['metadata'], true);
    }
    return $item;
}
```

**Acceptance test:**
1. GET /api/cover-letters/{id} returns decoded metadata
2. Metadata fields accessible directly

**Regression test:**
1. Verify cover-letter CRUD operations work
2. Verify no breaking changes

---

### SVC-011: ProspectService review() Updates Status Column

**Issue ID:** SVC-011
**Priority:** HIGH
**Status:** OPEN
**Description:** ProspectService::review() updates status column but prospects table doesn't have status column in schema.sql

**Evidence:**
```php
// api/services/ProspectService.php:23-27
public function review($id) {
    $sql = "UPDATE {$this->table} SET status = 'reviewed' WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
}

// database/schema.sql:53-73 (no status column)
// database/schema_gaps.sql:2-3 adds status column
```

**File path:** `api/services/ProspectService.php`
**Line numbers:** 23-27

**Function/class:** ProspectService::review()

**Dependency trace:**
- POST /api/prospects/review → ProspectService::review()
- review() updates status column
- Column doesn't exist in schema.sql
- Query fails with "Unknown column 'status'"

**Root cause:** DB-003 - status column missing from schema.sql

**Production impact:** HIGH — Prospect review workflow fails until DB-003 fixed

**Allowed files to modify:**
- database/schema.sql (add status column)

**Required fix:** Add status column to prospects table in schema.sql (see DB-003)

**Acceptance test:**
1. POST /api/prospects/review succeeds
2. status column exists in prospects table

**Regression test:**
1. Verify prospect review workflow works
2. Verify no breaking changes

---

### SVC-012: LinkedInStatService Uses Hard Delete

**Issue ID:** SVC-012
**Priority:** LOW
**Status:** OPEN
**Description:** LinkedInStatService::delete() uses hard DELETE instead of soft delete pattern used by other services

**Evidence:**
```php
// api/services/LinkedInStatService.php:20-24
public function delete($id) {
    $sql = "DELETE FROM {$this->table} WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
}

// BaseService::delete() uses soft delete
// api/services/BaseService.php:44-48
public function delete($id) {
    $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
}
```

**File path:** `api/services/LinkedInStatService.php`
**Line numbers:** 20-24

**Function/class:** LinkedInStatService::delete()

**Dependency trace:**
- DELETE /api/linkedin-stats/{id} → LinkedInStatService::delete()
- Hard delete permanently removes data
- Other services use soft delete
- Inconsistent behavior

**Root cause:** LinkedIn stats table has no deleted_at column (DB-008)

**Production impact:** LOW — Data permanently deleted instead of soft deleted

**Allowed files to modify:**
- api/services/LinkedInStatService.php
- database/schema_gaps.sql

**Required fix:** Either add deleted_at column to linkedin_stats table and use soft delete, or document hard delete as intentional

**Acceptance test:**
1. DELETE /api/linkedin-stats/{id} succeeds
2. Data handling matches documented behavior

**Regression test:**
1. Verify delete operation works
2. Verify no breaking changes

---

### SVC-013: ExportService Assumes deleted_at Column on All Tables

**Issue ID:** SVC-013
**Priority:** LOW
**Status:** OPEN
**Description:** ExportService::exportAll() queries WHERE deleted_at IS NULL on all tables except linkedin_stats and exports, but saved_searches doesn't have deleted_at in schema.sql

**Evidence:**
```php
// api/services/ExportService.php:22-33
public function exportAll() {
    $data = [];
    
    foreach ($this->tables as $table) {
        $sql = "SELECT * FROM {$table}";
        if ($table !== 'linkedin_stats' && $table !== 'exports') {
            $sql .= " WHERE deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $data[$table] = $stmt->fetchAll();
    }
    
    return $data;
}
```

**File path:** `api/services/ExportService.php`
**Line numbers:** 22-33

**Function/class:** ExportService::exportAll()

**Dependency trace:**
- GET /api/export → ExportController::list() → ExportService::exportAll()
- exportAll() assumes all tables except linkedin_stats and exports have deleted_at
- saved_searches doesn't have deleted_at in schema.sql (DB-002)
- Query will fail for saved_searches

**Root cause:** DB-002 - deleted_at column missing from saved_searches

**Production impact:** LOW — Export functionality fails for saved_searches until DB-002 fixed

**Allowed files to modify:**
- api/services/ExportService.php

**Required fix:** Add saved_searches to exception list, or fix DB-002 first

**Acceptance test:**
1. GET /api/admin/export succeeds
2. All tables export without error

**Regression test:**
1. Verify export functionality works
2. Verify no breaking changes

---

### SVC-014: SettingsService Ignores $id Parameter

**Issue ID:** SVC-014
**Priority:** LOW
**Status:** OPEN
**Description:** SettingsService::getById() ignores $id parameter and always returns first row

**Evidence:**
```php
// api/services/SettingsService.php:14-16
public function getById($id) {
    return $this->getAll()[0] ?? null;
}

// update() also ignores $id parameter
// api/services/SettingsService.php:22-35
public function update($id, $data) {
    $mappedData = [];
    foreach ($data as $key => $value) {
        $cleanKey = preg_replace('/^set-/', '', $key);
        $mappedData[$cleanKey] = $value;
    }
    
    $columns = array_keys($mappedData);
    $setClauses = array_map(function($col) { return "$col = ?"; }, $columns);
    $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(array_values($mappedData));
    return $this->getById(1);
}
```

**File path:** `api/services/SettingsService.php`
**Line numbers:** 14-16, 22-35

**Function/class:** SettingsService::getById(), SettingsService::update()

**Dependency trace:**
- SettingsService assumes single-row settings table
- Ignores passed $id parameter
- Hardcoded WHERE id = 1

**Root cause:** Design decision to use single-row settings table, but implementation is brittle

**Production impact:** LOW — Works if id=1 row exists, fails otherwise

**Allowed files to modify:**
- api/services/SettingsService.php

**Required fix:** Either remove hardcoded id=1 and use actual $id parameter, or document the single-row assumption clearly

**Acceptance test:**
1. GET /api/settings returns settings row
2. POST /api/settings updates settings row
3. Works regardless of row id

**Regression test:**
1. Verify settings operations work
2. Verify no breaking changes

---

### SVC-015: AIService Extends BaseService but Doesn't Use $table

**Issue ID:** SVC-015
**Priority:** LOW
**Status:** OPEN
**Description:** AIService extends BaseService but doesn't use $table property, extends BaseService unnecessarily

**Evidence:**
```php
// api/services/AIService.php:10-16
class AIService extends BaseService {
    private array $prompts = [];

    public function __construct() {
        parent::__construct();
        $this->loadPrompts();
    }

// AIService doesn't use $table, doesn't need database CRUD
// Extending BaseService is unnecessary
```

**File path:** `api/services/AIService.php`
**Line numbers:** 10-16

**Function/class:** AIService

**Dependency trace:**
- AIService extends BaseService for no reason
- Doesn't use any BaseService methods
- Doesn't use $table property
- Unnecessary inheritance

**Root cause:** AIService copied from service template without removing unnecessary inheritance

**Production impact:** LOW — Works but confusing architecture

**Allowed files to modify:**
- api/services/AIService.php

**Required fix:** Remove BaseService inheritance, make AIService standalone class

**Acceptance test:**
1. POST /api/resumes/generate works
2. POST /api/cover-letters/generate works
3. AI generation works correctly

**Regression test:**
1. Verify AI generation works
2. Verify no breaking changes

---

## SUMMARY

**Total Issues:** 15
**Critical:** 7 (SVC-001 through SVC-007)
**High:** 1 (SVC-011)
**Low:** 7 (SVC-008, SVC-009, SVC-010, SVC-012, SVC-013, SVC-014, SVC-015)

**Production Blockers:**
- SVC-001: BaseService constructor parameter mismatch — Blocks all services that pass parameters
- SVC-002 through SVC-007: Individual service constructor calls — Blocked by SVC-001
- SVC-011: ProspectService review() status column — Blocked by DB-003

**Recommended Fix Order:**
1. SVC-001 (BaseService constructor) — Blocks 6 services
2. SVC-002 through SVC-007 (Remove parameter passing) — Dependent on SVC-001
3. SVC-011 (ProspectService review) — Blocked by DB-003
4. SVC-008 through SVC-015 (Code quality) — Low priority

---

## SERVICE PATTERNS

**Pattern A: Model-based with parameter passing (BROKEN):**
- CompanyService, JobService, UserService, InterviewService, RecruiterService, AnalyticsService
- Instantiate model, call parent::__construct($this->model->getTable())
- BROKEN due to SVC-001

**Pattern B: Direct $table property (WORKING):**
- ApplicationService, ResumeService, SettingsService, CoverLetterService, ConversationService, DocumentService, ProspectService, SavedSearchService, TargetCompanyService, LinkedInStatService, ExportService
- Set protected $table = 'table_name' directly
- WORKING but inconsistent

**Pattern C: No database needed (MISARCHITECTED):**
- AIService extends BaseService unnecessarily
- Doesn't use database or $table
- Should be standalone class

---

**END OF AUDIT REPORT**
