/**
 * MODULE: Dashboard Logic
 * WORKFLOW: All
 * PURPOSE: Dashboard-specific interactions and charts
 */

class Dashboard {
  constructor() {
    this.charts = {};
  }

  init() {
    this.loadStats();
    this.loadPipeline();
    this.loadRecentActivity();
  }

  async loadStats() {
    const data = await API.analyticsSummary();
    if (!data.success) return;
    const stats = data.data;
    // Update stat cards if they exist
    const cards = document.querySelectorAll('.stat-value');
    if (cards[0]) cards[0].textContent = stats.applications_total;
    if (cards[1]) cards[1].textContent = stats.interviews_total;
    if (cards[2]) cards[2].textContent = stats.offers_total;
  }

  async loadPipeline() {
    const data = await API.applicationPipeline();
    if (!data.success) return;
    // Render pipeline bars
    const container = document.querySelector('.pipeline-chart');
    if (!container) return;
    let html = '';
    data.data.forEach(stage => {
      const width = Math.min(100, Math.max(stage.count * 5, 4));
      html += `<div class="pipeline-bar"><span class="pipeline-label">${stage.status}</span><div class="pipeline-bar-fill" style="width:${width}%"></div><span class="pipeline-count">${stage.count}</span></div>`;
    });
    container.innerHTML = html;
  }

  async loadRecentActivity() {
    const data = await API.analyticsActivity('?start=' + this.getStartDate() + '&end=' + this.getEndDate());
    if (!data.success) return;
    // Render activity timeline
  }

  getStartDate() {
    const d = new Date();
    d.setDate(d.getDate() - 30);
    return d.toISOString().split('T')[0];
  }

  getEndDate() {
    return new Date().toISOString().split('T')[0];
  }
}

// Initialize dashboard when on dashboard page
document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('.dashboard-grid')) {
    new Dashboard().init();
  }
});
