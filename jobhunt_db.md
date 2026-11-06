	applications	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	2	MyISAM	utf8mb4_general_ci	5.2 KiB	-
	companies	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	0	MyISAM	utf8mb4_general_ci	4.0 KiB	-
	conversations	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	1	MyISAM	utf8mb4_general_ci	2.1 KiB	-
	cover_letters	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	1	MyISAM	utf8mb4_general_ci	2.1 KiB	-
	documents	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	2	MyISAM	utf8mb4_general_ci	3.2 KiB	-
	exports	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	2	MyISAM	utf8mb4_general_ci	2.6 KiB	-
	interviews	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	0	MyISAM	utf8mb4_general_ci	1.0 KiB	-
	jobs	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	0	MyISAM	utf8mb4_general_ci	1.0 KiB	-
	linkedin_stats	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	2	InnoDB	utf8mb4_general_ci	16.0 KiB	-
	prospects	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	3	MyISAM	utf8mb4_general_ci	3.2 KiB	-
	recruiters	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	0	MyISAM	utf8mb4_general_ci	1.0 KiB	-
	resumes	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	1	MyISAM	utf8mb4_general_ci	2.2 KiB	-
	saved_searches	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	1	MyISAM	utf8mb4_general_ci	2.1 KiB	-
	settings	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	1	MyISAM	utf8mb4_general_ci	2.1 KiB	-
	target_companies	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	1	InnoDB	utf8mb4_general_ci	16.0 KiB	-
	users	 	Browse Browse	Structure Structure	Search Search	Insert Insert	Empty Empty	Drop Drop	1	MyISAM	utf8mb4_general_ci	9.1 KiB	-
16 tables	Sum	18	InnoDB	utf8mb4_general_ci	73.0 KiB	0 B




Database operationscarrmulti_jobhunt
NewNew
Structureapplications

Columns Columns
NewNew
Columnapplied_at (datetime, nullable)
Columncompany (varchar, nullable)
Columncompany_id (MUL, int, nullable)
Columncover_letter_version_id (int, nullable)
Columncreated_at (datetime, CURREN...)
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
Columnupdated_at (datetime, CURREN...)
Columnurl (text, nullable)

Indexes Indexes
NewNew
Indexcompany_id
Indexidx_job_id
Indexidx_status
IndexPRIMARY
Structurecompanies

Columns Columns
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

Indexes Indexes
NewNew
Indexidx_industry
Indexidx_name
IndexPRIMARY
Structureconversations

Columns Columns
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

Indexes Indexes
NewNew
IndexPRIMARY
Structurecover_letters

Columns Columns
NewNew
Columncontent (longtext, nullable)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnid (PRI, int)
Columnmetadata (json, nullable)
Columntitle (varchar)
Columnupdated_at (datetime)
Columnversion (int, 1, nullable)

Indexes Indexes
NewNew
IndexPRIMARY
Structuredocuments

Columns Columns
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

Indexes Indexes
NewNew
Indexidx_owner
IndexPRIMARY
Structureexports
Expand/Collapse
Columns Columns
Expand/Collapse
Indexes Indexes
Structureinterviews

Columns Columns
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

Indexes Indexes
NewNew
Indexapplication_id
IndexPRIMARY
Indexrecruiter_id
Structurejobs

Columns Columns
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

Indexes Indexes
NewNew
Indexidx_company_id
Indexidx_status
IndexPRIMARY
Structureprospects

Columns Columns
NewNew
Columncompany (varchar, nullable)
Columncompany_id (MUL, int, nullable)
Columncreated_at (datetime, CURREN...)
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
Columnstatus (varchar, new)
Columntitle (varchar, nullable)
Columnupdated_at (datetime, CURREN...)
Columnurl (text, nullable)

Indexes Indexes
NewNew
Indexcompany_id
IndexPRIMARY
Structurerecruiters

Columns Columns
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

Indexes Indexes
NewNew
Indexcompany_id
IndexPRIMARY
Structureresumes

Columns Columns
NewNew
Columncontent (longtext, nullable)
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnid (PRI, int)
Columnmaster_resume (tinyint)
Columnmetadata (json, nullable)
Columntitle (varchar)
Columnupdated_at (datetime)
Columnversion (int, 1, nullable)

Indexes Indexes
NewNew
IndexPRIMARY
Structuresaved_searches

Columns Columns
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

Indexes Indexes
NewNew
IndexPRIMARY
Structuresettings

Columns Columns
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

Indexes Indexes
NewNew
IndexPRIMARY
Structureusers

Columns Columns
NewNew
Columncreated_at (datetime)
Columndeleted_at (datetime, nullable)
Columnemail (UNI, varchar, nullable)
Columnid (PRI, int)
Columnname (varchar)
Columnpassword_hash (varchar, nullable)
Columnrole (varchar, user, nullable)
Columnupdated_at (datetime)

Indexes Indexes
NewNew
Indexemail
IndexPRIMARY