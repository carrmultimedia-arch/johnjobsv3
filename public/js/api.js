/**
 * MODULE: API Client
 * WORKFLOW: All
 * PURPOSE: API communication layer per 0420_RESPONSE_STANDARD.md
 */

const API_BASE = '/api/';

class API {
  static async request(method, endpoint, body = null) {
    const url = API_BASE + endpoint;
    const options = {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    };
    if (body) options.body = JSON.stringify(body);

    try {
      const response = await fetch(url, options);
      const data = await response.json();
      return data;
    } catch (error) {
      console.error('API Error:', error);
      return { success: false, error: { code: 'NETWORK_ERROR', message: 'Network error' } };
    }
  }

  // Health
  static health() { return this.request('GET', 'health'); }

  // Companies
  static companies(params = '') { return this.request('GET', 'companies' + params); }
  static company(id) { return this.request('GET', `companies/${id}`); }
  static companyJobs(id) { return this.request('GET', `companies/${id}/jobs`); }
  static companyStats() { return this.request('GET', 'companies/stats'); }
  static createCompany(data) { return this.request('POST', 'companies', data); }
  static updateCompany(id, data) { return this.request('PUT', `companies/${id}`, data); }
  static deleteCompany(id) { return this.request('DELETE', `companies/${id}`); }
  static bulkCompanyStatus(data) { return this.request('POST', 'companies/bulk-status', data); }

  // Jobs
  static jobs(params = '') { return this.request('GET', 'jobs' + params); }
  static job(id) { return this.request('GET', `jobs/${id}`); }
  static jobStats() { return this.request('GET', 'jobs/stats'); }
  static createJob(data) { return this.request('POST', 'jobs', data); }
  static updateJob(id, data) { return this.request('PUT', `jobs/${id}`, data); }
  static deleteJob(id) { return this.request('DELETE', `jobs/${id}`); }

  // Prospects
  static prospects(params = '') { return this.request('GET', 'prospects' + params); }
  static prospect(id) { return this.request('GET', `prospects/${id}`); }
  static prospectsByCompany(id) { return this.request('GET', `prospects/company/${id}`); }
  static createProspect(data) { return this.request('POST', 'prospects', data); }
  static updateProspect(id, data) { return this.request('PUT', `prospects/${id}`, data); }
  static deleteProspect(id) { return this.request('DELETE', `prospects/${id}`); }

  // Recruiters
  static recruiters(params = '') { return this.request('GET', 'recruiters' + params); }
  static recruiter(id) { return this.request('GET', `recruiters/${id}`); }
  static createRecruiter(data) { return this.request('POST', 'recruiters', data); }
  static updateRecruiter(id, data) { return this.request('PUT', `recruiters/${id}`, data); }
  static deleteRecruiter(id) { return this.request('DELETE', `recruiters/${id}`); }

  // Applications
  static applications(params = '') { return this.request('GET', 'applications' + params); }
  static application(id) { return this.request('GET', `applications/${id}`); }
  static applicationPipeline() { return this.request('GET', 'applications/pipeline'); }
  static createApplication(data) { return this.request('POST', 'applications', data); }
  static updateApplication(id, data) { return this.request('PUT', `applications/${id}`, data); }
  static deleteApplication(id) { return this.request('DELETE', `applications/${id}`); }
  static updateStage(data) { return this.request('POST', 'applications/stage', data); }

  // Interviews
  static interviews(params = '') { return this.request('GET', 'interviews' + params); }
  static interview(id) { return this.request('GET', `interviews/${id}`); }
  static upcomingInterviews() { return this.request('GET', 'interviews/upcoming'); }
  static interviewCalendar(params = '') { return this.request('GET', 'interviews/calendar' + params); }
  static createInterview(data) { return this.request('POST', 'interviews', data); }
  static updateInterview(id, data) { return this.request('PUT', `interviews/${id}`, data); }
  static deleteInterview(id) { return this.request('DELETE', `interviews/${id}`); }

  // Documents
  static documents(params = '') { return this.request('GET', 'documents' + params); }
  static document(id) { return this.request('GET', `documents/${id}`); }
  static documentsByType(type) { return this.request('GET', `documents/type?type=${type}`); }
  static activeDocuments() { return this.request('GET', 'documents/active'); }
  static createDocument(data) { return this.request('POST', 'documents', data); }
  static updateDocument(id, data) { return this.request('PUT', `documents/${id}`, data); }
  static deleteDocument(id) { return this.request('DELETE', `documents/${id}`); }

  // Resumes
  static resumes(params = '') { return this.request('GET', 'resumes' + params); }
  static activeResume() { return this.request('GET', 'resumes/active'); }
  static setActiveResume(id) { return this.request('POST', 'resumes/active', { id }); }
  static createResume(data) { return this.request('POST', 'resumes', data); }
  static updateResume(id, data) { return this.request('PUT', `resumes/${id}`, data); }
  static deleteResume(id) { return this.request('DELETE', `resumes/${id}`); }

  // Cover Letters
  static coverLetters(params = '') { return this.request('GET', 'cover-letters' + params); }
  static coverLetter(id) { return this.request('GET', `cover-letters/${id}`); }
  static coverLettersForJob(jobId) { return this.request('GET', `cover-letters/job?job_id=${jobId}`); }
  static createCoverLetter(data) { return this.request('POST', 'cover-letters', data); }
  static updateCoverLetter(id, data) { return this.request('PUT', `cover-letters/${id}`, data); }
  static deleteCoverLetter(id) { return this.request('DELETE', `cover-letters/${id}`); }

  // Conversations
  static conversations(params = '') { return this.request('GET', 'conversations' + params); }
  static conversation(id) { return this.request('GET', `conversations/${id}`); }
  static conversationsByType(type) { return this.request('GET', `conversations/type?type=${type}`); }
  static followUps() { return this.request('GET', 'conversations/follow-ups'); }
  static conversationThread(threadId) { return this.request('GET', `conversations/thread?thread_id=${threadId}`); }
  static createConversation(data) { return this.request('POST', 'conversations', data); }
  static updateConversation(id, data) { return this.request('PUT', `conversations/${id}`, data); }
  static deleteConversation(id) { return this.request('DELETE', `conversations/${id}`); }

  // Analytics
  static analyticsSummary() { return this.request('GET', 'analytics/summary'); }
  static analyticsDashboard() { return this.request('GET', 'analytics/dashboard'); }
  static analyticsApplications() { return this.request('GET', 'analytics/applications'); }
  static analyticsCompanies() { return this.request('GET', 'analytics/companies'); }
  static analyticsActivity(params = '') { return this.request('GET', 'analytics/activity' + params); }

  // Admin
  static adminHealth() { return this.request('GET', 'admin/system-health'); }
  static adminTables() { return this.request('GET', 'admin/tables'); }
  static adminLogs(lines = 50) { return this.request('GET', `admin/logs?lines=${lines}`); }
  static clearLogs() { return this.request('POST', 'admin/clear-logs'); }
  static adminBackup() { return this.request('GET', 'admin/backup'); }
}
