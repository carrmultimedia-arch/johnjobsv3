# Job Hunt Command Center - Deployment Guide

## FTP Upload Package

Upload the following files/directories to your web host:

### Root Files
- `index.php` - Front controller
- `config.php` - Database configuration (update with production credentials)
- `.htaccess` - Apache rewrite rules

### Directories
- `api/` - Complete API backend
- `public/` - Frontend application
- `documents/` - Document storage (create with 755 permissions)

## Pre-Deployment Checklist

1. **Update config.php** with production database credentials:
   ```php
   define('DB_HOST', 'your-production-host');
   define('DB_NAME', 'your-production-db');
   define('DB_USER', 'your-production-user');
   define('DB_PASS', 'your-production-password');
   define('ENV', 'production');
   ```

2. **Set directory permissions**:
   - `documents/` - 755 (or 777 if needed for file uploads)
   - `documents/exports/` - 755
   - `documents/notes/` - 755
   - `documents/resumes/` - 755
   - `documents/cover_letters/` - 755
   - `documents/job_descriptions/` - 755

3. **Run schema_gaps.sql** on production database:
   ```bash
   mysql -h your-host -u your-user -p your-db < database/schema_gaps.sql
   ```

4. **Test health endpoint**:
   ```
   https://yourdomain.com/api/health
   ```
   Should return: `{"success":true,"data":{"status":"ok"},"error":null}`

## File Structure After Upload

```
/public_html/
├── .htaccess
├── config.php
├── index.php
├── api/
│   ├── bootstrap.php
│   ├── router.php
│   ├── controllers/
│   ├── services/
│   ├── models/
│   └── utils/
├── public/
│   └── index.php
└── documents/
    ├── exports/
    ├── notes/
    ├── resumes/
    ├── cover_letters/
    └── job_descriptions/
```

## API Endpoints

All endpoints are available at `/api/`:

- `GET /api/health` - Health check
- `GET/POST /api/settings` - Settings
- `GET/POST/PUT/DELETE /api/applications` - Applications
- `GET/POST/PUT/DELETE /api/prospects` - Prospects
- `POST /api/prospects/batch-discard` - Batch discard prospects
- `POST /api/prospects/{id}/review` - Review prospect
- `GET/POST/PUT/DELETE /api/documents` - Documents
- `GET/POST/PUT/DELETE /api/conversations` - Conversations
- `GET/POST/PUT/DELETE /api/tailored-resumes` - Tailored Resumes
- `GET/POST/PUT/DELETE /api/cover-letters` - Cover Letters
- `GET/POST/PUT/DELETE /api/saved-searches` - Saved Searches
- `GET/POST/PUT/DELETE /api/target-companies` - Target Companies
- `GET/POST/PUT/DELETE /api/linkedin-stats` - LinkedIn Stats
- `GET /api/export` - Export all data

## Frontend Access

Access the frontend at:
```
https://yourdomain.com/
```

## Troubleshooting

- **404 errors**: Check that `.htaccess` is uploaded and mod_rewrite is enabled
- **500 errors**: Check file permissions and PHP error logs
- **Database connection errors**: Verify config.php credentials
- **File upload errors**: Check documents/ directory permissions

## Support

For issues, refer to workorder.txt for detailed specifications.
