# AUDIT REPORT: Subsystem 06 — MODELS

**Audit Date:** 2026-07-24
**Auditor:** Repository Auditor
**Subsystem:** MODELS

---

## MODEL INVENTORY

**Models Found (29 files):**

**Base Model:**
1. Model.php (base class)

**Model Suffix Files (Model.php naming):**
2. AnalyticsModel.php
3. ApplicationModel.php
4. CompanyModel.php
5. ConversationModel.php
6. CoverLetterModel.php
7. DocumentModel.php
8. ExportModel.php
9. InterviewModel.php
10. JobModel.php
11. LinkedInStatModel.php
12. Model.php (base)
13. ProspectModel.php
14. RecruiterModel.php
15. ResumeModel.php
16. SavedSearchModel.php
17. SettingsModel.php
18. TargetCompanyModel.php
19. UserModel.php

**No Suffix Files (plain naming):**
20. Application.php
21. Company.php
22. Conversation.php
23. CoverLetter.php
24. Document.php
25. Interview.php
26. Job.php
27. Prospect.php
28. Recruiter.php
29. Resume.php
30. User.php

---

## ISSUES FOUND

### MDL-001: Model Property Type Conflict Between Base and Child Classes

**Issue ID:** MDL-001
**Priority:** CRITICAL
**Status:** OPEN
**Description:** Base Model has untyped protected $table property, but 6 child models declare typed protected string $table properties, causing PHP 8+ fatal error

**Evidence:**
```php
// api/models/Model.php:5
protected $table;

// api/models/CompanyModel.php:7
protected string $table = 'companies';

// api/models/JobModel.php:7
protected string $table = 'jobs';

// api/models/UserModel.php:7
protected string $table = 'users';

// api/models/InterviewModel.php:7
protected string $table = 'interviews';

// api/models/RecruiterModel.php:7
protected string $table = 'recruiters';

// api/models/AnalyticsModel.php:7
protected string $table = 'analytics';
```

**File path:** `api/models/Model.php`
**Line numbers:** 5

**Function/class:** Model base class

**Dependency trace:**
- CompanyService, JobService, UserService, InterviewService, RecruiterService, AnalyticsService instantiate models
- Models have typed $table property conflicting with base Model's untyped $table
- PHP 8+ fatal error: "Property type mismatch"

**Root cause:** Base Model property not typed, child models typed

**Production impact:** CRITICAL — Any API request using these services fails with fatal error

**Allowed files to modify:**
- api/models/Model.php
- api/models/CompanyModel.php
- api/models/JobModel.php
- api/models/UserModel.php
- api/models/InterviewModel.php
- api/models/RecruiterModel.php
- api/models/AnalyticsModel.php

**Required fix:** Option 1: Add type to base Model:
```php
protected string $table = '';
```

Option 2: Remove type from child models:
```php
protected $table = 'companies';
```

**Acceptance test:**
1. GET /api/companies returns data without error
2. GET /api/jobs returns data without error
3. All affected models instantiate correctly

**Regression test:**
1. Verify all CRUD operations work
2. Verify no breaking changes

---

### MDL-002: AnalyticsModel References Non-Existent Table

**Issue ID:** MDL-002
**Priority:** HIGH
**Status:** OPEN
**Description:** AnalyticsModel declares $table = 'analytics' but no analytics table exists in schema.sql

**Evidence:**
```php
// api/models/AnalyticsModel.php:7
protected string $table = 'analytics';

// database/schema.sql has no analytics table
// AnalyticsService doesn't use $table property, uses direct queries
```

**File path:** `api/models/AnalyticsModel.php`
**Line numbers:** 7

**Function/class:** AnalyticsModel

**Dependency trace:**
- AnalyticsService instantiates AnalyticsModel
- AnalyticsModel declares $table = 'analytics'
- No analytics table in schema.sql
- AnalyticsService doesn't use model's $table anyway

**Root cause:** AnalyticsModel created but not needed, AnalyticsService uses direct queries

**Production impact:** HIGH — If AnalyticsService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/AnalyticsModel.php
- api/services/AnalyticsService.php

**Required fix:** Option 1: Remove AnalyticsModel (not used)
Option 2: Add analytics table to schema.sql (if needed)

**Acceptance test:**
1. GET /api/analytics/funnel works
2. Analytics endpoints work correctly

**Regression test:**
1. Verify analytics functionality works
2. Verify no breaking changes

---

### MDL-003: ApplicationModel Missing $table Property

**Issue ID:** MDL-003
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ApplicationModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/ApplicationModel.php:3-5
class ApplicationModel extends Model {
    
}

// ApplicationService sets $table directly: protected $table = 'applications';
// Model not used by ApplicationService
```

**File path:** `api/models/ApplicationModel.php`
**Line numbers:** 3-5

**Function/class:** ApplicationModel

**Dependency trace:**
- ApplicationModel exists but not used by ApplicationService
- ApplicationService sets $table property directly
- Model would return null from getTable()

**Root cause:** ApplicationModel created but not used

**Production impact:** MEDIUM — If ApplicationService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/ApplicationModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'applications';
```

**Acceptance test:**
1. ApplicationModel->getTable() returns 'applications'
2. ApplicationService works correctly

**Regression test:**
1. Verify application CRUD operations work
2. Verify no breaking changes

---

### MDL-004: ResumeModel Missing $table Property

**Issue ID:** MDL-004
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ResumeModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/ResumeModel.php:3-5
class ResumeModel extends Model {
    
}

// ResumeService sets $table directly: protected $table = 'resumes';
// Model not used by ResumeService
```

**File path:** `api/models/ResumeModel.php`
**Line numbers:** 3-5

**Function/class:** ResumeModel

**Dependency trace:**
- ResumeModel exists but not used by ResumeService
- ResumeService sets $table property directly
- Model would return null from getTable()

**Root cause:** ResumeModel created but not used

**Production impact:** MEDIUM — If ResumeService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/ResumeModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'resumes';
```

**Acceptance test:**
1. ResumeModel->getTable() returns 'resumes'
2. ResumeService works correctly

**Regression test:**
1. Verify resume CRUD operations work
2. Verify no breaking changes

---

### MDL-005: SettingsModel Missing $table Property

**Issue ID:** MDL-005
**Priority:** MEDIUM
**Status:** OPEN
**Description:** SettingsModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/SettingsModel.php:3-5
class SettingsModel extends Model {
    
}

// SettingsService sets $table directly: protected $table = 'settings';
// Model not used by SettingsService
```

**File path:** `api/models/SettingsModel.php`
**Line numbers:** 3-5

**Function/class:** SettingsModel

**Dependency trace:**
- SettingsModel exists but not used by SettingsService
- SettingsService sets $table property directly
- Model would return null from getTable()

**Root cause:** SettingsModel created but not used

**Production impact:** MEDIUM — If SettingsService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/SettingsModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'settings';
```

**Acceptance test:**
1. SettingsModel->getTable() returns 'settings'
2. SettingsService works correctly

**Regression test:**
1. Verify settings operations work
2. Verify no breaking changes

---

### MDL-006: ProspectModel Missing $table Property

**Issue ID:** MDL-006
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ProspectModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/ProspectModel.php:3-5
class ProspectModel extends Model {
    
}

// ProspectService sets $table directly: protected $table = 'prospects';
// Model not used by ProspectService
```

**File path:** `api/models/ProspectModel.php`
**Line numbers:** 3-5

**Function/class:** ProspectModel

**Dependency trace:**
- ProspectModel exists but not used by ProspectService
- ProspectService sets $table property directly
- Model would return null from getTable()

**Root cause:** ProspectModel created but not used

**Production impact:** MEDIUM — If ProspectService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/ProspectModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'prospects';
```

**Acceptance test:**
1. ProspectModel->getTable() returns 'prospects'
2. ProspectService works correctly

**Regression test:**
1. Verify prospect CRUD operations work
2. Verify no breaking changes

---

### MDL-007: TargetCompanyModel Missing $table Property

**Issue ID:** MDL-007
**Priority:** MEDIUM
**Status:** OPEN
**Description:** TargetCompanyModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/TargetCompanyModel.php:3-5
class TargetCompanyModel extends Model {
    
}

// TargetCompanyService sets $table directly: protected $table = 'target_companies';
// Model not used by TargetCompanyService
```

**File path:** `api/models/TargetCompanyModel.php`
**Line numbers:** 3-5

**Function/class:** TargetCompanyModel

**Dependency trace:**
- TargetCompanyModel exists but not used by TargetCompanyService
- TargetCompanyService sets $table property directly
- Model would return null from getTable()

**Root cause:** TargetCompanyModel created but not used

**Production impact:** MEDIUM — If TargetCompanyService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/TargetCompanyModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'target_companies';
```

**Acceptance test:**
1. TargetCompanyModel->getTable() returns 'target_companies'
2. TargetCompanyService works correctly

**Regression test:**
1. Verify target-companies CRUD operations work
2. Verify no breaking changes

---

### MDL-008: LinkedInStatModel Missing $table Property

**Issue ID:** MDL-008
**Priority:** MEDIUM
**Status:** OPEN
**Description:** LinkedInStatModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/LinkedInStatModel.php:3-5
class LinkedInStatModel extends Model {
    
}

// LinkedInStatService sets $table directly: protected $table = 'linkedin_stats';
// Model not used by LinkedInStatService
```

**File path:** `api/models/LinkedInStatModel.php`
**Line numbers:** 3-5

**Function/class:** LinkedInStatModel

**Dependency trace:**
- LinkedInStatModel exists but not used by LinkedInStatService
- LinkedInStatService sets $table property directly
- Model would return null from getTable()

**Root cause:** LinkedInStatModel created but not used

**Production impact:** MEDIUM — If LinkedInStatService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/LinkedInStatModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'linkedin_stats';
```

**Acceptance test:**
1. LinkedInStatModel->getTable() returns 'linkedin_stats'
2. LinkedInStatService works correctly

**Regression test:**
1. Verify linkedin-stats CRUD operations work
2. Verify no breaking changes

---

### MDL-009: SavedSearchModel Missing $table Property

**Issue ID:** MDL-009
**Priority:** MEDIUM
**Status:** OPEN
**Description:** SavedSearchModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/SavedSearchModel.php:3-5
class SavedSearchModel extends Model {
    
}

// SavedSearchService sets $table directly: protected $table = 'saved_searches';
// Model not used by SavedSearchService
```

**File path:** `api/models/SavedSearchModel.php`
**Line numbers:** 3-5

**Function/class:** SavedSearchModel

**Dependency trace:**
- SavedSearchModel exists but not used by SavedSearchService
- SavedSearchService sets $table property directly
- Model would return null from getTable()

**Root cause:** SavedSearchModel created but not used

**Production impact:** MEDIUM — If SavedSearchService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/SavedSearchModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'saved_searches';
```

**Acceptance test:**
1. SavedSearchModel->getTable() returns 'saved_searches'
2. SavedSearchService works correctly

**Regression test:**
1. Verify saved-searches CRUD operations work
2. Verify no breaking changes

---

### MDL-010: ExportModel Missing $table Property

**Issue ID:** MDL-010
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ExportModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/ExportModel.php:3-5
class ExportModel extends Model {
    
}

// ExportService doesn't use model at all
// Model not used by ExportService
```

**File path:** `api/models/ExportModel.php`
**Line numbers:** 3-5

**Function/class:** ExportModel

**Dependency trace:**
- ExportModel exists but not used by ExportService
- ExportService uses direct queries
- Model would return null from getTable()

**Root cause:** ExportModel created but not used

**Production impact:** MEDIUM — If ExportService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/ExportModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'exports';
```

**Acceptance test:**
1. ExportModel->getTable() returns 'exports'
2. ExportService works correctly

**Regression test:**
1. Verify export functionality works
2. Verify no breaking changes

---

### MDL-011: CoverLetterModel Missing $table Property

**Issue ID:** MDL-011
**Priority:** MEDIUM
**Status:** OPEN
**Description:** CoverLetterModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/CoverLetterModel.php:3-5
class CoverLetterModel extends Model {
    
}

// CoverLetterService sets $table directly: protected $table = 'cover_letters';
// Model not used by CoverLetterService
```

**File path:** `api/models/CoverLetterModel.php`
**Line numbers:** 3-5

**Function/class:** CoverLetterModel

**Dependency trace:**
- CoverLetterModel exists but not used by CoverLetterService
- CoverLetterService sets $table property directly
- Model would return null from getTable()

**Root cause:** CoverLetterModel created but not used

**Production impact:** MEDIUM — If CoverLetterService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/CoverLetterModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'cover_letters';
```

**Acceptance test:**
1. CoverLetterModel->getTable() returns 'cover_letters'
2. CoverLetterService works correctly

**Regression test:**
1. Verify cover-letter CRUD operations work
2. Verify no breaking changes

---

### MDL-012: ConversationModel Missing $table Property

**Issue ID:** MDL-012
**Priority:** MEDIUM
**Status:** OPEN
**Description:** ConversationModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/ConversationModel.php:3-5
class ConversationModel extends Model {
    
}

// ConversationService sets $table directly: protected $table = 'conversations';
// Model not used by ConversationService
```

**File path:** `api/models/ConversationModel.php`
**Line numbers:** 3-5

**Function/class:** ConversationModel

**Dependency trace:**
- ConversationModel exists but not used by ConversationService
- ConversationService sets $table property directly
- Model would return null from getTable()

**Root cause:** ConversationModel created but not used

**Production impact:** MEDIUM — If ConversationService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/ConversationModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'conversations';
```

**Acceptance test:**
1. ConversationModel->getTable() returns 'conversations'
2. ConversationService works correctly

**Regression test:**
1. Verify conversation CRUD operations work
2. Verify no breaking changes

---

### MDL-013: DocumentModel Missing $table Property

**Issue ID:** MDL-013
**Priority:** MEDIUM
**Status:** OPEN
**Description:** DocumentModel extends Model but doesn't declare $table property, getTable() returns null

**Evidence:**
```php
// api/models/DocumentModel.php:3-5
class DocumentModel extends Model {
    
}

// DocumentService sets $table directly: protected $table = 'documents';
// Model not used by DocumentService
```

**File path:** `api/models/DocumentModel.php`
**Line numbers:** 3-5

**Function/class:** DocumentModel

**Dependency trace:**
- DocumentModel exists but not used by DocumentService
- DocumentService sets $table property directly
- Model would return null from getTable()

**Root cause:** DocumentModel created but not used

**Production impact:** MEDIUM — If DocumentService ever used model->getTable(), it would fail

**Allowed files to modify:**
- api/models/DocumentModel.php

**Required fix:** Add $table property or remove unused model:
```php
protected string $table = 'documents';
```

**Acceptance test:**
1. DocumentModel->getTable() returns 'documents'
2. DocumentService works correctly

**Regression test:**
1. Verify document CRUD operations work
2. Verify no breaking changes

---

### MDL-014: Duplicate Model Files (No Suffix vs Model Suffix)

**Issue ID:** MDL-014
**Priority:** LOW
**Status:** OPEN
**Description:** Duplicate model files exist: Company.php and CompanyModel.php, Job.php and JobModel.php, etc. Both have same functionality

**Evidence:**
```php
// api/models/Company.php:10-12
class Company extends Model {
    protected string $table = 'companies';
}

// api/models/CompanyModel.php:5-8
class CompanyModel extends Model
{
    protected string $table = 'companies';
}

// Same pattern for: Job.php/JobModel.php, User.php/UserModel.php, etc.
```

**File path:** `api/models/`
**Line numbers:** Multiple files

**Function/class:** Multiple duplicate model classes

**Dependency trace:**
- Services use Model suffix versions (CompanyModel, JobModel, etc.)
- No suffix versions (Company, Job, etc.) are unused
- Confusing to have both

**Root cause:** Inconsistent naming convention, duplicate files created

**Production impact:** LOW — Services use Model suffix versions, no suffix versions unused

**Allowed files to modify:**
- api/models/Company.php
- api/models/Job.php
- api/models/User.php
- api/models/Application.php
- api/models/Resume.php
- api/models/Conversation.php
- api/models/CoverLetter.php
- api/models/Document.php
- api/models/Interview.php
- api/models/Recruiter.php
- api/models/Prospect.php

**Required fix:** Remove unused no-suffix model files

**Acceptance test:**
1. All services still work
2. No breaking changes

**Regression test:**
1. Verify all CRUD operations work
2. Verify no breaking changes

---

## SUMMARY

**Total Issues:** 14
**Critical:** 1 (MDL-001)
**High:** 1 (MDL-002)
**Medium:** 12 (MDL-003 through MDL-013)
**Low:** 1 (MDL-014)

**Production Blockers:**
- MDL-001: Model property type conflict — Blocks 6 models (CompanyModel, JobModel, UserModel, InterviewModel, RecruiterModel, AnalyticsModel)
- MDL-002: AnalyticsModel references non-existent table — Blocks if AnalyticsService uses model

**Recommended Fix Order:**
1. MDL-001 (Property type conflict) — Blocks 6 models
2. MDL-002 (AnalyticsModel table) — High priority
3. MDL-003 through MDL-013 (Missing $table properties) — Add properties or remove unused models
4. MDL-014 (Duplicate files) — Remove unused no-suffix versions

---

## MODEL PATTERNS

**Pattern A: Typed $table property (BROKEN due to MDL-001):**
- CompanyModel, JobModel, UserModel, InterviewModel, RecruiterModel, AnalyticsModel
- Declare: protected string $table = 'table_name'
- BROKEN due to type conflict with base Model

**Pattern B: No $table property (INCOMPLETE):**
- ApplicationModel, ResumeModel, SettingsModel, ProspectModel, TargetCompanyModel, LinkedInStatModel, SavedSearchModel, ExportModel, CoverLetterModel, ConversationModel, DocumentModel
- Don't declare $table property
- Services don't use models anyway

**Pattern C: Duplicate no-suffix files (UNUSED):**
- Company.php, Job.php, User.php, Application.php, Resume.php, Conversation.php, CoverLetter.php, Document.php, Interview.php, Recruiter.php, Prospect.php
- Same functionality as Model suffix versions
- Unused by services

---

## MODEL USAGE BY SERVICES

**Services that use models:**
- CompanyService → CompanyModel
- JobService → JobModel
- UserService → UserModel
- InterviewService → InterviewModel
- RecruiterService → RecruiterModel
- AnalyticsService → AnalyticsModel

**Services that don't use models:**
- ApplicationService → sets $table directly
- ResumeService → sets $table directly
- SettingsService → sets $table directly
- ProspectService → sets $table directly
- TargetCompanyService → sets $table directly
- LinkedInStatService → sets $table directly
- SavedSearchService → sets $table directly
- ExportService → doesn't use models
- CoverLetterService → sets $table directly
- ConversationService → sets $table directly
- DocumentService → sets $table directly

---

**END OF AUDIT REPORT**
