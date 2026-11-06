# AUDIT REPORT: Subsystem 02 — DATABASE

**Audit Date:** 2026-07-24
**Auditor:** Repository Auditor
**Subsystem:** DATABASE

---

## FILE INVENTORY

### Configuration
- `config.php` — Database connection credentials

### Schema Files
- `database/schema.sql` — Main schema (15 tables)
- `database/schema_gaps.sql` — Missing columns and tables
- `database/migrations/001_initial_schema.sql` — Baseline migration
- `database/migrations/002_add_master_resume_and_api_keys.sql` — Add master_resume, api_keys to settings

### Database Utils
- `api/utils/DB.php` — PDO singleton

### Services (Database Access Layer)
- 19 service files accessing database

---

## ISSUES FOUND

### DB-001: Missing Tables in Production Schema

**Issue ID:** DB-001
**Priority:** CRITICAL
**Status:** OPEN
**Description:** Two tables referenced by services do not exist in database/schema.sql: target_companies and linkedin_stats

**Evidence:**
```sql
-- database/schema.sql has no target_companies table
-- database/schema.sql has no linkedin_stats table

-- database/schema_gaps.sql:24-32 creates target_companies
CREATE TABLE IF NOT EXISTS target_companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company VARCHAR(255) NOT NULL,
  target_role VARCHAR(255) NULL,
  hiring_manager VARCHAR(255) NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL
);

-- database/schema_gaps.sql:34-42 creates linkedin_stats
CREATE TABLE IF NOT EXISTS linkedin_stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  week_of DATE NOT NULL,
  connections INT DEFAULT 0,
  messages INT DEFAULT 0,
  responses INT DEFAULT 0,
  calls INT DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

**File path:** `database/schema.sql`
**Line numbers:** N/A (missing tables)

**Function/class:** TargetCompanyService, LinkedInStatService

**Dependency trace:**
- TargetCompanyService extends BaseService with $table = 'target_companies'
- LinkedInStatService extends BaseService with $table = 'linkedin_stats'
- Both services will fail when querying non-existent tables

**Root cause:** schema_gaps.sql contains CREATE TABLE statements that were never merged into schema.sql

**Production impact:** CRITICAL — Any API request to /api/target-companies or /api/linkedin-stats will fail with "Table doesn't exist" error

**Allowed files to modify:**
- database/schema.sql
- database/schema_gaps.sql

**Required fix:** Merge CREATE TABLE statements from schema_gaps.sql into schema.sql

**Acceptance test:**
1. GET /api/target-companies returns empty array (not error)
2. GET /api/linkedin-stats returns empty array (not error)
3. Both tables exist in database

**Regression test:**
1. Verify all existing tables still exist
2. Verify foreign key constraints still valid
3. Verify no duplicate table definitions

---

### DB-002: Missing Column in saved_searches Table

**Issue ID:** DB-002
**Priority:** HIGH
**Status:** OPEN
**Description:** saved_searches table missing deleted_at column for soft deletes, but SavedSearchService queries it

**Evidence:**
```sql
-- database/schema.sql:224-234 (no deleted_at column)
CREATE TABLE IF NOT EXISTS saved_searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    query TEXT,
    filters JSON,
    source VARCHAR(100),
    alert_frequency VARCHAR(50),
    user_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- database/schema_gaps.sql:5-7 adds deleted_at
ALTER TABLE saved_searches
  ADD COLUMN deleted_at DATETIME NULL AFTER updated_at;

-- api/services/SavedSearchService.php:7-10 queries deleted_at
public function getAll() {
    $sql = "SELECT *, query as url FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}
```

**File path:** `database/schema.sql`
**Line numbers:** 224-234

**Function/class:** SavedSearchService::getAll()

**Dependency trace:**
- SavedSearchService::getAll() queries WHERE deleted_at IS NULL
- Column does not exist in schema.sql
- Query will fail with "Unknown column 'deleted_at'"

**Root cause:** schema_gaps.sql ALTER TABLE statement not applied to schema.sql

**Production impact:** HIGH — GET /api/saved-searches will fail with "Unknown column 'deleted_at'" error

**Allowed files to modify:**
- database/schema.sql
- database/schema_gaps.sql

**Required fix:** Add deleted_at column to saved_searches table in schema.sql

**Acceptance test:**
1. GET /api/saved-searches returns data without error
2. deleted_at column exists in saved_searches table

**Regression test:**
1. Verify saved_searches queries work
2. Verify soft delete functionality works

---

### DB-003: Missing Columns in prospects Table

**Issue ID:** DB-003
**Priority:** HIGH
**Status:** OPEN
**Description:** prospects table missing status column for workflow tracking, but ProspectService queries it

**Evidence:**
```sql
-- database/schema.sql:53-73 (no status column)
CREATE TABLE IF NOT EXISTS prospects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    linkedin VARCHAR(255),
    company VARCHAR(255),
    notes TEXT,
    source VARCHAR(100),
    role_type VARCHAR(100),
    priority_score INT DEFAULT 0,
    rating INT DEFAULT 0,
    url TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    INDEX company_id (company_id),
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- database/schema_gaps.sql:2-3 adds status
ALTER TABLE prospects
  ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'new' AFTER rating;

-- api/services/ProspectService.php:23-27 queries status
public function review($id) {
    $sql = "UPDATE {$this->table} SET status = 'reviewed' WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
}
```

**File path:** `database/schema.sql`
**Line numbers:** 53-73

**Function/class:** ProspectService::review()

**Dependency trace:**
- ProspectService::review() updates status column
- Column does not exist in schema.sql
- Query will fail with "Unknown column 'status'"

**Root cause:** schema_gaps.sql ALTER TABLE statement not applied to schema.sql

**Production impact:** HIGH — Prospect review workflow will fail with "Unknown column 'status'" error

**Allowed files to modify:**
- database/schema.sql
- database/schema_gaps.sql

**Required fix:** Add status column to prospects table in schema.sql

**Acceptance test:**
1. POST /api/prospects/{id}/review succeeds
2. status column exists in prospects table

**Regression test:**
1. Verify prospects queries work
2. Verify workflow status tracking works

---

### DB-004: Missing Columns in settings Table

**Issue ID:** DB-004
**Priority:** MEDIUM
**Status:** OPEN
**Description:** settings table missing target_location and bio columns needed by frontend Settings tab

**Evidence:**
```sql
-- database/schema.sql:194-210 (missing target_location, bio)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100),
    value TEXT,
    category VARCHAR(100),
    user_id INT,
    name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    linkedin VARCHAR(255),
    portfolio VARCHAR(255),
    salary VARCHAR(100),
    arrangement VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- database/schema_gaps.sql:10-12 adds target_location, bio
ALTER TABLE settings
  ADD COLUMN target_location VARCHAR(255) NULL AFTER arrangement,
  ADD COLUMN bio TEXT NULL AFTER target_location,
  ADD COLUMN master_resume LONGTEXT NULL AFTER bio;

-- database/migrations/002_add_master_resume_and_api_keys.sql:5-6 adds master_resume, api_keys
ALTER TABLE settings ADD COLUMN master_resume LONGTEXT AFTER arrangement;
ALTER TABLE settings ADD COLUMN api_keys JSON AFTER master_resume;
```

**File path:** `database/schema.sql`
**Line numbers:** 194-210

**Function/class:** SettingsService

**Dependency trace:**
- Frontend Settings tab expects target_location and bio fields
- Columns do not exist in schema.sql
- Migration 002 adds master_resume but not target_location/bio

**Root cause:** schema_gaps.sql ALTER TABLE statements not applied to schema.sql

**Production impact:** MEDIUM — Settings tab will fail to save target_location and bio data

**Allowed files to modify:**
- database/schema.sql
- database/schema_gaps.sql
- database/migrations/002_add_master_resume_and_api_keys.sql

**Required fix:** Add target_location and bio columns to settings table in schema.sql

**Acceptance test:**
1. POST /api/settings with target_location and bio succeeds
2. Columns exist in settings table

**Regression test:**
1. Verify settings queries work
2. Verify all settings fields save correctly

---

### DB-005: Migration 002 Column Conflict

**Issue ID:** DB-005
**Priority:** MEDIUM
**Status:** OPEN
**Description:** Migration 002 adds master_resume column AFTER arrangement, but schema_gaps.sql adds it AFTER bio (after adding bio column)

**Evidence:**
```sql
-- database/migrations/002_add_master_resume_and_api_keys.sql:5
ALTER TABLE settings ADD COLUMN master_resume LONGTEXT AFTER arrangement;

-- database/schema_gaps.sql:10-13
ALTER TABLE settings
  ADD COLUMN target_location VARCHAR(255) NULL AFTER arrangement,
  ADD COLUMN bio TEXT NULL AFTER target_location,
  ADD COLUMN master_resume LONGTEXT NULL AFTER bio;
```

**File path:** `database/migrations/002_add_master_resume_and_api_keys.sql`
**Line numbers:** 5

**Function/class:** N/A (migration file)

**Dependency trace:**
- Migration 002 adds master_resume after arrangement
- schema_gaps.sql adds target_location, bio, then master_resume
- Column order conflict if both run

**Root cause:** Two different migration paths for same columns

**Production impact:** MEDIUM — Column order inconsistency, may cause confusion

**Allowed files to modify:**
- database/migrations/002_add_master_resume_and_api_keys.sql
- database/schema_gaps.sql

**Required fix:** Consolidate migration to add all three columns (target_location, bio, master_resume, api_keys) in correct order

**Acceptance test:**
1. Migration runs without error
2. All four columns exist in correct order

**Regression test:**
1. Verify migration is idempotent
2. Verify column order matches schema.sql

---

### DB-006: ResumeService Metadata Fields Not in Schema

**Issue ID:** DB-006
**Priority:** LOW
**Status:** OPEN
**Description:** ResumeService stores target_role and company in metadata JSON, but ResumesController generate() uses them as direct fields

**Evidence:**
```php
-- api/services/ResumeService.php:15-22 stores target_role, company in metadata
if (isset($data['target_role'])) {
    $metadata['target_role'] = $data['target_role'];
    unset($data['target_role']);
}
if (isset($data['company'])) {
    $metadata['company'] = $data['company'];
    unset($data['company']);
}

-- api/controllers/ResumesController.php:45-50 uses target_role, company as direct fields
$resumeData = [
    'title' => ($targetRole ? $targetRole . ' - ' : '') . ($company ? $company : 'Optimized Resume'),
    'content' => $aiResult['output'] ?? $aiResult['rendered_prompt'],
    'target_role' => $targetRole,
    'company' => $company,
    'version' => 1
];
```

**File path:** `api/controllers/ResumesController.php`
**Line numbers:** 45-50

**Function/class:** ResumesController::generate()

**Dependency trace:**
- ResumesController passes target_role and company as direct fields
- ResumeService moves them to metadata JSON
- Schema.sql has only metadata JSON column (no direct target_role/company columns)

**Root cause:** Inconsistent data handling between controller and service

**Production impact:** LOW — Data will be stored in metadata JSON, but controller expects direct fields

**Allowed files to modify:**
- api/controllers/ResumesController.php
- api/services/ResumeService.php

**Required fix:** Either add target_role and company columns to schema.sql, or ensure controller doesn't pass them as direct fields

**Acceptance test:**
1. POST /api/resumes/generate succeeds
2. Generated resume includes target_role and company data
3. Data is retrievable via GET /api/resumes/{id}

**Regression test:**
1. Verify existing resumes still load
2. Verify metadata JSON structure is consistent

---

### DB-007: SettingsService Assumes Single Row

**Issue ID:** DB-007
**Priority:** LOW
**Status:** OPEN
**Description:** SettingsService hardcodes WHERE id = 1 for all operations, assumes single-row settings table

**Evidence:**
```php
-- api/services/SettingsService.php:7-12
public function getAll() {
    $sql = "SELECT * FROM {$this->table} WHERE id = 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $item = $stmt->fetch();
    return $item ? [$item] : [];
}

-- api/services/SettingsService.php:14-16
public function getById($id) {
    return $this->getAll()[0] ?? null;
}

-- api/services/SettingsService.php:31
$sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = 1";
```

**File path:** `api/services/SettingsService.php`
**Line numbers:** 7-12, 14-16, 31

**Function/class:** SettingsService

**Dependency trace:**
- SettingsService always queries WHERE id = 1
- Ignores passed $id parameter in getById()
- Assumes single-row settings table

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
2. Verify no breaking changes to frontend

---

### DB-008: LinkedInStatService Hard Delete Instead of Soft Delete

**Issue ID:** DB-008
**Priority:** LOW
**Status:** OPEN
**Description:** LinkedInStatService uses DELETE FROM instead of soft delete pattern used by other services

**Evidence:**
```php
-- api/services/LinkedInStatService.php:20-24
public function delete($id) {
    $sql = "DELETE FROM {$this->table} WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id]);
}

-- Compare to BaseService::delete() (soft delete)
-- api/services/BaseService.php:44-48
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
- LinkedInStatService overrides BaseService::delete()
- Uses hard DELETE instead of soft delete
- Inconsistent with other services

**Root cause:** LinkedIn stats table has no deleted_at column (schema_gaps.sql doesn't add it)

**Production impact:** LOW — Data permanently deleted instead of soft deleted

**Allowed files to modify:**
- database/schema_gaps.sql
- api/services/LinkedInStatService.php

**Required fix:** Either add deleted_at column to linkedin_stats table and use soft delete, or document hard delete as intentional

**Acceptance test:**
1. DELETE /api/linkedin-stats/{id} succeeds
2. Data handling matches documented behavior

**Regression test:**
1. Verify delete operation works
2. Verify no breaking changes

---

### DB-009: ExportService Assumes deleted_at Column on All Tables

**Issue ID:** DB-009
**Priority:** LOW
**Status:** OPEN
**Description:** ExportService queries WHERE deleted_at IS NULL on all tables except linkedin_stats and exports, but saved_searches doesn't have deleted_at in schema.sql

**Evidence:**
```php
-- api/services/ExportService.php:22-33
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
- ExportService assumes all tables except linkedin_stats and exports have deleted_at
- saved_searches doesn't have deleted_at in schema.sql (DB-002)
- Query will fail for saved_searches

**Root cause:** Assumption about table schema not validated

**Production impact:** LOW — Export functionality will fail for saved_searches until DB-002 fixed

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

## SUMMARY

**Total Issues:** 9
**Critical:** 1 (DB-001)
**High:** 2 (DB-002, DB-003)
**Medium:** 2 (DB-004, DB-005)
**Low:** 4 (DB-006, DB-007, DB-008, DB-009)

**Production Blockers:**
- DB-001: Missing target_companies and linkedin_stats tables — Blocks Network tab and LinkedIn stats
- DB-002: Missing deleted_at in saved_searches — Blocks saved searches API
- DB-003: Missing status in prospects — Blocks prospect review workflow

**Recommended Fix Order:**
1. DB-001 (Missing tables) — Blocks entire features
2. DB-002 (Missing deleted_at) — Blocks saved searches
3. DB-003 (Missing status) — Blocks prospect workflow
4. DB-004 (Missing settings columns) — Blocks settings fields
5. DB-005 (Migration conflict) — Prevents clean deployment
6. DB-006 (Resume metadata) — Data handling inconsistency
7. DB-007 (Settings single-row) — Brittleness
8. DB-008 (Hard delete) — Inconsistency
9. DB-009 (Export assumptions) — Depends on DB-002

---

## DEPENDENCY GRAPH

```
config.php
├── DB.php (PDO singleton)
├── Services (19 files)
│   ├── BaseService
│   ├── CompanyService → companies table
│   ├── TargetCompanyService → target_companies table (MISSING)
│   ├── LinkedInStatService → linkedin_stats table (MISSING)
│   ├── SavedSearchService → saved_searches table (missing deleted_at)
│   ├── ProspectService → prospects table (missing status)
│   ├── SettingsService → settings table (missing columns)
│   └── ResumeService → resumes table (metadata handling)
└── Schema Files
    ├── schema.sql (incomplete)
    ├── schema_gaps.sql (missing tables/columns)
    └── migrations/002_add_master_resume_and_api_keys.sql (column conflict)
```

---

## FILES REQUIRING IMMEDIATE ATTENTION

1. `database/schema.sql` — Add missing tables and columns
2. `database/schema_gaps.sql` — Merge into schema.sql
3. `database/migrations/002_add_master_resume_and_api_keys.sql` — Fix column order

---

**END OF AUDIT REPORT**
