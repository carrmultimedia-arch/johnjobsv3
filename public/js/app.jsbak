/**
 * MODULE: Application Bootstrap
 * WORKFLOW: All
 * PURPOSE: Core frontend SPA per 0520_NAVIGATION.md
 */

class App {
  constructor() {
    this.currentSection = 'dashboard';
    this.init();
  }

  init() {
    this.bindNavigation();
    this.bindActions();
    this.checkHealth();
    this.loadSection('dashboard');
  }

  bindNavigation() {
    document.querySelectorAll('.nav-menu li').forEach(item => {
      item.addEventListener('click', (e) => {
        const section = e.currentTarget.dataset.section;
        if (section) {
          this.setActiveNav(section);
          this.loadSection(section);
        }
      });
    });
  }

  bindActions() {
    const refreshBtn = document.getElementById('refresh-btn');
    const addBtn = document.getElementById('add-btn');
    if (refreshBtn) refreshBtn.addEventListener('click', () => this.loadSection(this.currentSection));
    if (addBtn) addBtn.addEventListener('click', () => this.showAddModal());
  }

  setActiveNav(section) {
    document.querySelectorAll('.nav-menu li').forEach(li => li.classList.remove('active'));
    const el = document.querySelector(`[data-section="${section}"]`);
    if (el) el.classList.add('active');
    this.currentSection = section;
    const titleEl = document.getElementById('page-title');
    if (titleEl) titleEl.textContent = this.capitalize(section);
  }

  capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  async checkHealth() {
    const status = document.getElementById('api-status');
    if (!status) return;
    try {
      const data = await API.health();
      if (data.success) {
        status.classList.add('online');
        status.textContent = '● API Online';
      } else {
        status.classList.add('offline');
        status.textContent = '● API Offline';
      }
    } catch (e) {
      status.classList.add('offline');
      status.textContent = '● API Offline';
    }
  }

  async loadSection(section) {
    const view = document.getElementById('main-view');
    if (!view) return;
    view.innerHTML = '<div class="loading">Loading...</div>';

    switch (section) {
      case 'dashboard':
        await this.renderDashboard(view);
        break;
      case 'companies':
        await this.renderCompanies(view);
        break;
      case 'jobs':
        await this.renderJobs(view);
        break;
      case 'applications':
        await this.renderApplications(view);
        break;
      case 'interviews':
        await this.renderInterviews(view);
        break;
      case 'prospects':
        await this.renderProspects(view);
        break;
      case 'recruiters':
        await this.renderRecruiters(view);
        break;
      case 'conversations':
        await this.renderConversations(view);
        break;
      case 'documents':
        await this.renderDocuments(view);
        break;
      case 'analytics':
        await this.renderAnalytics(view);
        break;
      default:
        view.innerHTML = '<div class="empty">Section not implemented yet</div>';
    }
  }

  // Dashboard
  async renderDashboard(container) {
    const data = await API.analyticsDashboard();
    if (!data.success) {
      container.innerHTML = '<div class="error">Failed to load dashboard</div>';
      return;
    }
    const counts = data.data.counts;
    const pipeline = data.data.pipeline;

    let html = '<div class="dashboard-grid">';
    const stats = [
      { label: 'Companies', value: counts.companies, icon: '◉' },
      { label: 'Jobs', value: counts.jobs, icon: '◉' },
      { label: 'Applications', value: counts.applications, icon: '◉' },
      { label: 'Interviews', value: counts.interviews, icon: '◉' },
      { label: 'Prospects', value: counts.prospects, icon: '◉' },
      { label: 'Recruiters', value: counts.recruiters, icon: '◉' },
    ];
    stats.forEach(s => {
      html += `<div class="stat-card"><div class="stat-icon">${s.icon}</div><div class="stat-value">${s.value}</div><div class="stat-label">${s.label}</div></div>`;
    });
    html += '</div>';

    if (pipeline && pipeline.length > 0) {
      html += '<div class="panel"><h3>Application Pipeline</h3><div class="pipeline-chart">';
      pipeline.forEach(p => {
        const width = Math.min(100, Math.max(p.count * 5, 4));
        html += `<div class="pipeline-bar"><span class="pipeline-label">${p.status}</span><div class="pipeline-bar-fill" style="width:${width}%"></div><span class="pipeline-count">${p.count}</span></div>`;
      });
      html += '</div></div>';
    }

    container.innerHTML = html;
  }

  // Generic list renderers
  async renderCompanies(container) { await this.renderTable(container, 'companies', API.companies(), ['name', 'industry', 'location', 'status']); }
  async renderJobs(container) { await this.renderTable(container, 'jobs', API.jobs(), ['title', 'location', 'employment_type', 'status']); }
  async renderApplications(container) { await this.renderTable(container, 'applications', API.applications(), ['title', 'status', 'applied_at']); }
  async renderInterviews(container) { await this.renderTable(container, 'interviews', API.interviews('?upcoming=1'), ['interview_type', 'scheduled_at', 'status']); }
  async renderProspects(container) { await this.renderTable(container, 'prospects', API.prospects(), ['name', 'title', 'company', 'status']); }
  async renderRecruiters(container) { await this.renderTable(container, 'recruiters', API.recruiters(), ['name', 'email', 'role']); }
  async renderDocuments(container) { await this.renderTable(container, 'documents', API.documents(), ['title', 'document_type', 'version']); }

  async renderTable(container, section, promise, columns) {
    const data = await promise;
    if (!data.success) {
      container.innerHTML = '<div class="error">Failed to load ' + section + '</div>';
      return;
    }
    const items = data.data.items || [];
    if (items.length === 0) {
      container.innerHTML = '<div class="empty">No ' + section + ' yet. Click + New to add one.</div>';
      return;
    }

    let html = '<div class="data-table"><table><thead><tr>';
    columns.forEach(c => html += `<th>${this.capitalize(c.replace('_', ' '))}</th>`);
    html += '<th>Actions</th></tr></thead><tbody>';

    items.forEach(item => {
      html += '<tr>';
      columns.forEach(c => {
        let val = item[c] ?? '-';
        if (c === 'status' || c === 'employment_type') {
          val = `<span class="badge badge-${val}">${val}</span>`;
        }
        html += `<td>${val}</td>`;
      });
      html += `<td><button class="btn-sm" onclick="app.editItem('${section}', ${item.id})">Edit</button><button class="btn-sm btn-danger" onclick="app.deleteItem('${section}', ${item.id})">×</button></td></tr>`;
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
  }

  async renderConversations(container) {
    const data = await API.conversations();
    if (!data.success) { container.innerHTML = '<div class="error">Failed to load conversations</div>'; return; }
    const items = data.data.items || [];
    if (items.length === 0) { container.innerHTML = '<div class="empty">No conversations yet.</div>'; return; }

    let html = '<div class="conversation-list">';
    items.forEach(c => {
      html += `<div class="conversation-card">
        <div class="conversation-header"><span class="conversation-type">${c.channel || 'message'}</span><span class="conversation-date">${new Date(c.created_at).toLocaleDateString()}</span></div>
        <div class="conversation-subject">${c.subject || 'No subject'}</div>
        <div class="conversation-preview">${c.content ? c.content.substring(0, 100) + '...' : ''}</div>
        <div class="conversation-meta"><span>${c.contact_name || 'Unknown'}</span></div>
      </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
  }

  async renderAnalytics(container) {
    const data = await API.analyticsDashboard();
    if (!data.success) { container.innerHTML = '<div class="error">Failed to load analytics</div>'; return; }
    const counts = data.data.counts;
    let html = '<div class="analytics-grid">';
    Object.entries(counts).forEach(([key, value]) => {
      html += `<div class="analytics-card"><div class="analytics-label">${key}</div><div class="analytics-value">${value}</div></div>`;
    });
    html += '</div>';
    container.innerHTML = html;
  }

  showAddModal() { alert('Add modal for ' + this.currentSection + ' — implement in Phase 6'); }
  editItem(section, id) { alert('Edit ' + section + ' ' + id); }
  deleteItem(section, id) {
    const sectionMap = {
      companies: 'Company',
      jobs: 'Job',
      applications: 'Application',
      interviews: 'Interview',
      prospects: 'Prospect',
      recruiters: 'Recruiter',
      conversations: 'Conversation',
      documents: 'Document'
    };
    const methodName = 'delete' + (sectionMap[section] || this.capitalize(section.slice(0, -1)));
    const deleteMethod = API[methodName];
    if (!deleteMethod) {
      console.error('Delete method not found for section:', section, methodName);
      return;
    }
    if (confirm('Delete?')) {
      deleteMethod.call(API, id).then(() => this.loadSection(section));
    }
  }
}

const app = new App();
