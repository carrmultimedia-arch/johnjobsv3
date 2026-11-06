find . -print | sed -e 's;[^/]*/;|____;g;s;____|; |;g'
 johnjobsv2
.
|____php_error.log
|____tree.md
|____php.ini
|____logs
| |____app.log
| |____api.log
| |____error.log
|____config.php
|____.user.ini
|____handoffv2.md
|____api
| |____utils
| | |____Response.php
| | |____DB.php
| | |____Logger.php
| | |____Validator.php
| | |____FileStorage.php
| |____models
| | |____Interview.php
| | |____Conversation.php
| | |____Model.php
| | |____Application.php
| | |____Resume.php
| | |____Job.php
| | |____Recruiter.php
| | |____Prospect.php
| | |____User.php
| | |____Company.php
| | |____Document.php
| | |____CoverLetter.php
| |____middleware
| | |____RateLimitMiddleware.php
| | |____AuthMiddleware.php
| | |____LoggingMiddleware.php
| | |____ValidationMiddleware.php
| |____router.php
| |____services
| | |____CompanyService.php
| | |____BaseService.php
| | |____DocumentService.php
| | |____InterviewService.php
| | |____ResumeService.php
| | |____CoverLetterService.php
| | |____JobService.php
| | |____ConversationService.php
| | |____ApplicationService.php
| | |____ProspectService.php
| | |____AIService.php
| | |____RecruiterService.php
| | |____AnalyticsService.php
| |____controllers
| | |____CoverLetterController.php
| | |____InterviewController.php
| | |____ApplicationController.php
| | |____ResumeController.php
| | |____BaseController.php
| | |____AnalyticsController.php
| | |____HealthController.php
| | |____AdminController.php
| | |____JobController.php
| | |____CompanyController.php
| | |____RecruiterController.php
| | |____ConversationController.php
| | |____ProspectController.php
| | |____DocumentController.php
| |____bootstrap.php
|____documents
| |____notes
| |____resumes
| |____job_descriptions
| |____cover_letters
| |____exports
|____conversations
| |____networking
| |____ai
| |____recruiters
| |____interviews
|____.htaccessbak
|____missing.html
|____public
| |____css
| | |____components.css
| | |____main.css
| | |____dashboard.css
| | |____forms.css
| |____index.php
| |____js
| | |____components.js
| | |____forms.js
| | |____dashboard.js
| | |____app.js
| | |____api.js
|____app.php
|____.htaccess
|____docs
|____database
| |____migrations
| | |____001_initial_schema.sql
| |____schema.sql
| |____seeds
| | |____seed.sql
|____index.php
|____diag.php
|____phptest.php
|____tests
| |____unit
| |____acceptance
| |____integration


=======DATABASE SCHEMA=======
 Database operationscarrmulti_jobhunt
NewNew
Expand/Collapse Structureapplications
Expand/Collapse
Columns  Columns
NewNew
Columnapplied_at (datetime, nullable)
Columncompany (varchar, nullable)
Columncompany_id (MUL, int, nullable)
Columncover_letter_version_id (int, nullable)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnfollow_up_at (datetime, nullable)
Columnid (PRI, int)
Columnjob_id (MUL, int, nullable)
Columnlocation (varchar, nullable)
Columnmethod (varchar, nullable)
Columnnotes (text, nullable)
Columnoutcome (varchar, nullable)
Columnresume_version_id (int, nullable)
Columnsalary (varchar, nullable)
Columnstatus (MUL, varchar, draft, nullable)
Columntitle (varchar, nullable)
Columnupdated_at (datetime)
Columnurl (text, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
Indexcompany_id
Indexidx_job_id
Indexidx_status
IndexPRIMARY
Expand/Collapse Structurecompanies
Expand/Collapse
Columns  Columns
NewNew
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columndescription (text, nullable)
Columnid (PRI, int)
Columnindustry (MUL, varchar, nullable)
Columnlocation (varchar, nullable)
Columnname (MUL, varchar)
Columnsize (varchar, nullable)
Columnstatus (varchar, active, nullable)
Columnupdated_at (datetime)
Columnwebsite (varchar, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
Indexidx_industry
Indexidx_name
IndexPRIMARY
Expand/Collapse Structureconversations
Expand/Collapse
Columns  Columns
NewNew
Columnchannel (varchar, nullable)
Columncontact_name (varchar, nullable)
Columncontact_role (varchar, nullable)
Columncontent (longtext, nullable)
Columncreated_at (datetime)
Columndate_sent (date, nullable)
Columndeleted_at (datetime, nullable)
Columndirection (varchar, nullable)
Columnfollow_up_date (date, nullable)
Columnfollow_up_needed (tinyint, nullable)
Columnid (PRI, int)
Columnmetadata (json, nullable)
Columnnotes (text, nullable)
Columnowner_id (int, nullable)
Columnowner_type (varchar, nullable)
Columnsubject (varchar, nullable)
Columnupdated_at (datetime)
Expand/Collapse
Indexes  Indexes
NewNew
IndexPRIMARY
Expand/Collapse Structurecover_letters
Expand/Collapse
Columns  Columns
NewNew
Columncontent (longtext, nullable)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnid (PRI, int)
Columnmetadata (json, nullable)
Columntitle (varchar)
Columnupdated_at (datetime)
Columnversion (int, 1, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
IndexPRIMARY
Expand/Collapse Structuredocuments
Expand/Collapse
Columns  Columns
NewNew
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columndocument_type (varchar)
Columnfile_path (varchar)
Columnid (PRI, int)
Columnmetadata (json, nullable)
Columnowner_id (int)
Columnowner_type (MUL, varchar)
Columntitle (varchar, nullable)
Columnupdated_at (datetime)
Columnversion (int, 1, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
Indexidx_owner
IndexPRIMARY
Expand/Collapse Structureexports
Expand/Collapse
Columns  Columns
NewNew
Columncreated_at (datetime, CURREN..., nullable)
Columnfile_path (varchar, nullable)
Columnformat (varchar, nullable)
Columnid (PRI, int)
Columnmetadata (json, nullable)
Columnstatus (varchar, nullable)
Columntype (varchar, nullable)
Columnupdated_at (datetime, CURREN..., nullable)
Columnuser_id (int, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
IndexPRIMARY
Expand/Collapse Structureinterviews
Expand/Collapse
Columns  Columns
NewNew
Columnapplication_id (MUL, int)
Columncompleted_at (datetime, nullable)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnfeedback (text, nullable)
Columnid (PRI, int)
Columninterview_type (varchar, nullable)
Columnnotes (text, nullable)
Columnoutcome (varchar, nullable)
Columnrecruiter_id (MUL, int, nullable)
Columnscheduled_at (datetime, nullable)
Columnupdated_at (datetime)
Expand/Collapse
Indexes  Indexes
NewNew
Indexapplication_id
IndexPRIMARY
Indexrecruiter_id
Expand/Collapse Structurejobs
Expand/Collapse
Columns  Columns
NewNew
Columncompany_id (MUL, int)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columndescription (text, nullable)
Columnemployment_type (varchar, nullable)
Columnexternal_id (varchar, nullable)
Columnid (PRI, int)
Columnlocation (varchar, nullable)
Columnposted_at (datetime, nullable)
Columnsalary_range (varchar, nullable)
Columnsource_url (varchar, nullable)
Columnstatus (MUL, varchar, open, nullable)
Columntitle (varchar)
Columnupdated_at (datetime)
Expand/Collapse
Indexes  Indexes
NewNew
Indexidx_company_id
Indexidx_status
IndexPRIMARY
Expand/Collapse Structureprospects
Expand/Collapse
Columns  Columns
NewNew
Columncompany (varchar, nullable)
Columncompany_id (MUL, int, nullable)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columndescription (text, nullable)
Columnemail (varchar, nullable)
Columnid (PRI, int)
Columnlinkedin (varchar, nullable)
Columnname (varchar)
Columnnotes (text, nullable)
Columnphone (varchar, nullable)
Columnpriority_score (int, nullable)
Columnrating (int, nullable)
Columnrole_type (varchar, nullable)
Columnsource (varchar, nullable)
Columntitle (varchar, nullable)
Columnupdated_at (datetime)
Columnurl (text, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
Indexcompany_id
IndexPRIMARY
Expand/Collapse Structurerecruiters
Expand/Collapse
Columns  Columns
NewNew
Columncompany_id (MUL, int, nullable)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnemail (varchar, nullable)
Columnid (PRI, int)
Columnlinkedin (varchar, nullable)
Columnname (varchar)
Columnnotes (text, nullable)
Columnphone (varchar, nullable)
Columnrole (varchar, nullable)
Columnupdated_at (datetime)
Expand/Collapse
Indexes  Indexes
NewNew
Indexcompany_id
IndexPRIMARY
Expand/Collapse Structureresumes
Expand/Collapse
Columns  Columns
NewNew
Columncontent (longtext, nullable)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnid (PRI, int)
Columnmetadata (json, nullable)
Columntitle (varchar)
Columnupdated_at (datetime)
Columnversion (int, 1, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
IndexPRIMARY
Expand/Collapse Structuresaved_searches
Expand/Collapse
Columns  Columns
NewNew
Columnalert_frequency (varchar, nullable)
Columncreated_at (datetime, CURREN..., nullable)
Columnfilters (json, nullable)
Columnid (PRI, int)
Columnname (varchar, nullable)
Columnquery (text, nullable)
Columnsource (varchar, nullable)
Columnupdated_at (datetime, CURREN..., nullable)
Columnuser_id (int, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
IndexPRIMARY
Expand/Collapse Structuresettings
Expand/Collapse
Columns  Columns
NewNew
Columnarrangement (varchar, nullable)
Columncategory (varchar, nullable)
Columncreated_at (datetime, CURREN..., nullable)
Columndeleted_at (datetime, nullable)
Columnemail (varchar, nullable)
Columnid (PRI, int)
Columnkey (varchar, nullable)
Columnlinkedin (varchar, nullable)
Columnname (varchar, nullable)
Columnphone (varchar, nullable)
Columnportfolio (varchar, nullable)
Columnsalary (varchar, nullable)
Columnupdated_at (datetime, CURREN..., nullable)
Columnuser_id (int, nullable)
Columnvalue (text, nullable)
Expand/Collapse
Indexes  Indexes
NewNew
IndexPRIMARY
Expand/Collapse Structureusers
Expand/Collapse
Columns  Columns
NewNew
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnemail (UNI, varchar, nullable)
Columnid (PRI, int)
Columnname (varchar)
Columnpassword_hash (varchar, nullable)
Columnrole (varchar, user, nullable)
Columnupdated_at (datetime)
Expand/Collapse
Indexes  Indexes
NewNew
Indexemail
IndexPRIMARY
