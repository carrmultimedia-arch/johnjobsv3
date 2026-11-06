JOBHUNT COMMAND CENTER — SESSION HANDOFF
Date: 2026-07-13
Session: Phase 3 Rebuild — Decision Point
DECISION MADE: FRESH START PROTOCOL
After 3 restarts due to phase dependency cascade failures, the following decision was made:
Create new folder: johnjobsv2/ (parallel to existing johnjobs/)
Preserve original: johnjobs/ remains untouched for reference
Build all files first, test after: No phase-by-phase testing until all files exist
Batch workflow: 3-5 files per chat, upload, confirm, next batch
JUST CODE MODE: No analysis, no explanations, no permission debugging
WHAT EXISTS IN ORIGINAL johnjobs/
Working:
/api/companies → returns JSON (old CompanyController)
Frontend SPA loads
Database jobhunt.db has data
Most controllers exist but crash (500 errors)
Mix of old + new files, invented controllers, permission issues
Broken:
/api/health → 500 (config.php path issue)
/api/jobs, /api/prospects, etc. → 500 or HTML
New architecture (app.php → config.php → BaseController) not functional
NEW FOLDER STRUCTURE: johnjobsv2/
plain
/johnjobsv2/
  .htaccess
  index.php
  app.php
  config.php
  api.php                    (optional — old entry point compatibility)

  api/
    bootstrap.php
    router.php

    controllers/
      BaseController.php
      CompanyController.php
      JobController.php
      ProspectController.php
      RecruiterController.php
      ApplicationController.php
      InterviewController.php
      DocumentController.php
      ResumeController.php
      CoverLetterController.php
      ConversationController.php
      AnalyticsController.php
      AdminController.php

    services/
      (stub files — Phase 5+)

    models/
      (stub files — Phase 5+)

    middleware/
      (stub files — Phase 7+)

    utils/
      DB.php
      Response.php
      Validator.php
      Logger.php
      FileStorage.php

  public/
    index.html
    css/
      main.css
      dashboard.css
      forms.css
      components.css
    js/
      app.js
      api.js
      dashboard.js
      forms.js
      components.js

  database/
    schema.sql
    jobhunt.db              (copied from original)

  documents/
    resumes/
    cover_letters/
    job_descriptions/
    notes/
    exports/

  conversations/
    ai/
    recruiters/
    interviews/
    networking/

  logs/
    app.log
    api.log
    error.log
BUILD ORDER (Batch Workflow)
BATCH 1: Core Foundation (7 files)
.htaccess
config.php
index.php
app.php
api/bootstrap.php
api/utils/DB.php
api/utils/Response.php
BATCH 2: BaseController + First Controller (2 files)
api/controllers/BaseController.php
api/controllers/CompanyController.php
BATCH 3: Core Controllers 2-5 (4 files)
api/controllers/JobController.php
api/controllers/ProspectController.php
api/controllers/RecruiterController.php
api/controllers/ApplicationController.php
BATCH 4: Core Controllers 6-9 (4 files)
api/controllers/InterviewController.php
api/controllers/DocumentController.php
api/controllers/ResumeController.php
api/controllers/CoverLetterController.php
BATCH 5: Core Controllers 10-12 + Router (3 files)
api/controllers/ConversationController.php
api/controllers/AnalyticsController.php
api/controllers/AdminController.php
api/router.php
BATCH 6: Frontend Foundation (5 files)
public/index.html
public/js/api.js
public/js/app.js
public/css/main.css
public/css/dashboard.css
BATCH 7+: Remaining frontend, services, models, middleware
BIBLE GOVERNANCE
Use existing Bible files from johnjobs/PROJECT_BIBLE/:
0220_FILE_REGISTRY.md
0310_DATABASE_SCHEMA.md
0410_ENDPOINT_REFERENCE.md
0420_RESPONSE_STANDARD.md
2111_PHASE3_BUILD_PLAN.md
2112_TRUTH_LAW.md
No new Bible needed. Existing Bible is authoritative.
WORKFLOW RULES
JUST CODE MODE: No analysis, no explanations, no permission discussion
Batch size: 3-7 files per chat session
Upload confirmation: User uploads batch, confirms working, says "next"
No testing mid-batch: Build all files in batch, upload all, then test
If error: Fix in next batch, don't stop current batch
Database: Copy johnjobs/database/jobhunt.db to johnjobsv2/database/
No invented files: Only files in Bible registry
NEXT SESSION STARTS WITH
User creates johnjobsv2/ folder, copies database, says "ready for batch 1"
I generate batch 1 files (no explanation, just code blocks)
User uploads, confirms, says "next"
Repeat until all 21 core files built
CRITICAL: DO NOT
Discuss permissions
Analyze server configuration
Explain code architecture
Invent files not in Bible
Test individual files mid-batch
Patch existing johnjobs/ folder
CRITICAL: DO
Reference Bible before every line
Build exactly what's in registry
Use 0420 response standard
Use 0310 schema field names
Batch complete files only
Move to next batch on "next"

========================================

johnjobsv2/
├── .htaccess                          # Phase 1
├── config.php                         # Phase 1
├── app.php                            # Phase 1
├── index.php                          # Phase 1
├── api/
│   ├── bootstrap.php                  # Phase 2
│   ├── router.php                     # Phase 2
│   ├── utils/
│   │   ├── DB.php                     # Phase 3 (MySQL)
│   │   ├── Response.php               # Phase 3 (0420 standard)
│   │   ├── Validator.php              # Phase 3
│   │   ├── Logger.php                 # Phase 3
│   │   └── FileStorage.php            # Phase 3
│   ├── middleware/
│   │   ├── AuthMiddleware.php         # Phase 4
│   │   ├── ValidationMiddleware.php   # Phase 4
│   │   ├── LoggingMiddleware.php      # Phase 4
│   │   └── RateLimitMiddleware.php    # Phase 4
│   ├── models/
│   │   ├── Model.php                  # Phase 5 (base)
│   │   ├── Company.php                # Phase 5
│   │   ├── Prospect.php               # Phase 5
│   │   ├── Job.php                    # Phase 5
│   │   ├── Application.php            # Phase 5
│   │   ├── Recruiter.php              # Phase 5
│   │   ├── Interview.php              # Phase 5
│   │   ├── Document.php               # Phase 5
│   │   ├── Resume.php                 # Phase 5
│   │   ├── CoverLetter.php            # Phase 5
│   │   ├── Conversation.php           # Phase 5
│   │   └── User.php                   # Phase 5
│   ├── services/
│   │   ├── BaseService.php            # Phase 6
│   │   ├── CompanyService.php         # Phase 6
│   │   ├── ProspectService.php        # Phase 6
│   │   ├── JobService.php             # Phase 6
│   │   ├── ApplicationService.php     # Phase 6
│   │   ├── RecruiterService.php       # Phase 6
│   │   ├── InterviewService.php       # Phase 6
│   │   ├── DocumentService.php        # Phase 6
│   │   ├── ResumeService.php          # Phase 6
│   │   ├── CoverLetterService.php     # Phase 6
│   │   ├── ConversationService.php    # Phase 6
│   │   ├── AIService.php              # Phase 6
│   │   └── AnalyticsService.php       # Phase 6
│   └── controllers/
│       ├── BaseController.php           # Phase 7
│       ├── CompanyController.php        # Phase 7
│       ├── ProspectController.php       # Phase 7
│       ├── JobController.php          # Phase 7
│       ├── ApplicationController.php  # Phase 7
│       ├── RecruiterController.php    # Phase 7
│       ├── InterviewController.php    # Phase 7
│       ├── DocumentController.php     # Phase 7
│       ├── ResumeController.php       # Phase 7
│       ├── CoverLetterController.php  # Phase 7
│       ├── ConversationController.php # Phase 7
│       ├── AnalyticsController.php    # Phase 7
│       └── AdminController.php        # Phase 7
├── database/
│   ├── schema.sql                     # Phase 8 (matches your MySQL schema)
│   ├── migrations/
│   │   └── 001_initial_schema.sql   # Phase 8
│   └── seeds/
│       └── seed.sql                   # Phase 8
├── public/
│   ├── index.html                     # Phase 11
│   ├── css/
│   │   ├── main.css                   # Phase 9
│   │   ├── dashboard.css              # Phase 9
│   │   ├── forms.css                  # Phase 9
│   │   └── components.css             # Phase 9
│   └── js/
│       ├── api.js                     # Phase 10
│       ├── app.js                     # Phase 10
│       ├── dashboard.js               # Phase 10
│       ├── forms.js                   # Phase 10
│       └── components.js              # Phase 10
├── documents/...                      # Phase 12
├── conversations/...                  # Phase 12
├── logs/                              # Phase 13
├── tests/...                          # Phase 14
└── docs/...                           # Phase 15