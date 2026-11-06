<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JobHunt Command Center v3 | John Carr</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg-dark: #0f0f13;
  --bg-dark-radial: radial-gradient(circle at 50% -20%, #1f1f2e 0%, #0f0f13 65%, #08080a 100%);
  --background-image: linear-gradient(40 deg, #1f1f2e 0%, #0f0f13 65%, #08080a 100%);
  --bg-card: #12121aff;
  --bg-card-radial: radial-gradient(circle at 50% -20%, #1f1f2e 0%, #0f0f13 65%, #08080a 100%);
  --bg-card-hover: #030317ff;
  --bg-sidebar: #171821;
  --bg-input: #08080a;

  --border: #2a2a35;
  --border-subtle: #242532;

  --accent: #c9a84c;
  --accent-dim: #8a7340;
  --accent-glow: rgba(201,168,76,0.15);
  --gold-gradient: linear-gradient(135deg, #dfbc73 0%, #c9a84c 50%, #8a7340 100%);

  --text: #e8e6e1;
  --text-dim: #8a8880;
  --text-muted: #5a5850;

  --success: #4caf50;
  --danger: #f44336;
  --warning: #ff9800;
  --info: #2196f3;

  --button-primary-bg: #c9a84c;
  --button-primary-text: #0f0f13;

  --shadow-card: 0 20px 40px rgba(0, 0, 0, 0.7), 0 5px 15px rgba(0, 0, 0, 0.4);
  --shadow-inset: inset 0 1px 0 rgba(255, 255, 255, 0.04), inset 0 -2px 5px rgba(0, 0, 0, 0.8);
  
  --font-display: 'Cinzel','Georgia',serif;
  --font-body: 'Inter',-apple-system,sans-serif;
  --font-mono: 'JetBrains Mono','Fira Code',monospace;
}

* { margin:0; padding:0; box-sizing:border-box; }

body {
  background: var(--bg-dark-radial);
  background-image: var(--bg-dark-radial);
  color: var(--text);
  font-family: var(--font-body);
  line-height: 1.6;
  min-height: 100vh;
}

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: var(--bg-dark); }
::-webkit-scrollbar-thumb { background: var(--accent-dim); border-radius: 3px; }

.app-container {
  display: grid;
  grid-template-columns: 260px 1fr;
  min-height: 100vh;
}

.sidebar {
  background: var(--bg-card-radial);
  border-right: 1px solid var(--border);
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
  box-shadow: 5px 0 25px rgba(0,0,0,0.5);
  z-index: 10;
}

.brand {
  font-family: var(--font-display);
  font-size: 1.3rem;
  color: var(--accent);
  text-align: center;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border);
  margin-bottom: 8px;
  text-shadow: 0 2px 6px rgba(0,0,0,0.7);
}

.brand span {
  color: var(--text-dim);
  font-size: 0.7rem;
  display: block;
  font-family: var(--font-body);
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-top: 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  color: var(--text-dim);
  font-size: 0.9rem;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}

.nav-item:hover, .nav-item.active {
  background: var(--bg-card-hover);
  color: var(--accent);
  border-left: 2px solid var(--accent);
}

.nav-item .icon {
  font-size: 1.1rem;
  width: 24px;
  text-align: center;
}

.nav-section {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid var(--border);
}

.nav-section-title {
  font-size: 0.65rem;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: var(--text-muted);
  padding: 0 16px 8px;
}

.stats-bar {
  margin-top: auto;
  padding-top: 16px;
  border-top: 1px solid var(--border);
}

.stat-row {
  display: flex;
  justify-content: space-between;
  padding: 6px 16px;
  font-size: 0.8rem;
}

.stat-row .label { color: var(--text-muted); }
.stat-row .value { color: var(--accent); font-weight: 600; }

.main {
  padding: 32px;
  overflow-y: auto;
  max-height: 100vh;
}

.page-title {
  font-family: var(--font-display);
  font-size: 1.8rem;
  color: var(--accent);
  margin-bottom: 4px;
  text-shadow: 0 2px 6px rgba(0,0,0,0.7);
  letter-spacing: 1px;
}

.page-subtitle {
  color: var(--text-dim);
  font-size: 0.9rem;
  margin-bottom: 28px;
}

.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
  margin-bottom: 28px;
}

.card {
  background: var(--bg-card-radial);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 24px;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: var(--shadow-card), var(--shadow-inset);
  position: relative;
}

.card:hover {
  border-color: var(--accent-dim);
  background-color: var(--bg-card-hover);
  transform: translateY(-2px);
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.card-title {
  font-family: var(--font-display);
  font-size: 1.1rem;
  color: var(--accent);
  letter-spacing: 1px;
}

.card-badge {
  font-size: 0.7rem;
  padding: 4px 10px;
  border-radius: 20px;
  background: var(--accent-glow);
  color: var(--accent);
  border: 1px solid var(--accent-dim);
  font-family: var(--font-mono);
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 12px;
  margin-bottom: 28px;
}

.action-btn {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 16px;
  color: var(--text);
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
  display: flex;
  flex-direction: column;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

.action-btn:hover {
  border-color: var(--accent);
  background: var(--bg-card-hover);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px var(--accent-glow);
}

.action-btn .label {
  font-size: 0.8rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.action-btn .title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text);
}

.action-btn .desc {
  font-size: 0.8rem;
  color: var(--text-dim);
}

.table-wrap { overflow-x: auto; }

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
}

th {
  text-align: left;
  padding: 12px 16px;
  color: var(--text-muted);
  font-weight: 500;
  text-transform: uppercase;
  font-size: 0.7rem;
  letter-spacing: 1px;
  border-bottom: 1px solid var(--border);
}

td {
  padding: 14px 16px;
  border-bottom: 1px solid var(--border);
  color: var(--text-dim);
}

tr:hover td { background: var(--bg-card-hover); color: var(--text); }

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 6px;
}
.status-applied { background: var(--success); }
.status-interview { background: var(--info); }
.status-rejected { background: var(--danger); }
.status-ghosted { background: var(--text-muted); }
.status-new { background: var(--warning); }
.status-offer { background: var(--success); }

.form-group { margin-bottom: 16px; }
.form-group label {
  display: block;
  font-size: 0.8rem;
  color: var(--text-muted);
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 12px 14px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 0.9rem;
  transition: border-color 0.2s, box-shadow 0.2s;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.8);
}
.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.8), 0 0 10px var(--accent-glow);
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 4px;
  border: none;
  font-family: var(--font-body);
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  text-transform: uppercase;
  letter-spacing: 1.5px;
}
.btn-primary {
  background: var(--gold-gradient);
  color: var(--button-primary-text);
  box-shadow: 0 4px 15px var(--accent-glow), inset 0 1px 0 rgba(255,255,255,0.4);
}
.btn-primary:hover {
  filter: brightness(1.15);
  box-shadow: 0 6px 20px rgba(201,168,76,0.3), inset 0 1px 0 rgba(255,255,255,0.6);
  transform: translateY(-1px);
}
.btn-secondary {
  background: var(--bg-card-hover);
  color: var(--text);
  border: 1px solid var(--border);
}
.btn-secondary:hover {
  border-color: var(--accent);
  color: var(--accent);
}
.btn-ghost {
  background: transparent;
  color: var(--text-dim);
  border: 1px dashed var(--border);
}
.btn-ghost:hover {
  border-color: var(--accent);
  color: var(--accent);
}
.btn-danger { background: var(--danger); color: #fff; }
.btn-success { background: var(--success); color: #fff; }
.btn-sm { padding: 6px 12px; font-size: 0.7rem; }

.tabs {
  display: flex;
  gap: 4px;
  margin-bottom: 20px;
  border-bottom: 1px solid var(--border);
}
.tab-btn {
  padding: 10px 20px;
  background: none;
  border: none;
  color: var(--text-dim);
  cursor: pointer;
  font-family: var(--font-body);
  font-size: 0.85rem;
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
  transition: all 0.2s;
}
.tab-btn:hover { color: var(--text); }
.tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); }
.tab-panel { display: none; }
.tab-panel.active { display: block; }

.checklist { list-style: none; }
.checklist li {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid var(--border);
  font-size: 0.9rem;
}
.checklist li:last-child { border-bottom: none; }
.checklist input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: var(--accent);
  margin-top: 2px;
  flex-shrink: 0;
}
.checklist .task-text { flex: 1; }
.checklist .task-time {
  color: var(--text-muted);
  font-size: 0.75rem;
  font-family: var(--font-mono);
}

.workflow-step {
  display: flex;
  gap: 16px;
  padding: 20px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 4px;
  margin-bottom: 12px;
  box-shadow: var(--shadow-card);
}
.workflow-step .step-num {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--accent-glow);
  border: 2px solid var(--accent);
  color: var(--accent);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  flex-shrink: 0;
}
.workflow-step .step-content h4 {
  color: var(--text);
  margin-bottom: 4px;
  font-size: 1rem;
}
.workflow-step .step-content p {
  color: var(--text-dim);
  font-size: 0.85rem;
}
.workflow-step .step-content .tool-tag {
  display: inline-block;
  background: var(--bg-dark);
  border: 1px solid var(--border);
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 0.7rem;
  color: var(--accent);
  margin-top: 8px;
  font-family: var(--font-mono);
}

.copy-block {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 16px;
  font-family: var(--font-mono);
  font-size: 0.8rem;
  color: var(--text-dim);
  position: relative;
  margin: 12px 0;
  white-space: pre-wrap;
  line-height: 1.6;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.8);
}
.copy-block .copy-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  color: var(--text-muted);
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 0.7rem;
  cursor: pointer;
  font-family: var(--font-body);
}
.copy-block .copy-btn:hover {
  color: var(--accent);
  border-color: var(--accent);
}

.editor {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 16px;
  font-family: var(--font-body);
  font-size: 0.9rem;
  color: var(--text);
  width: 100%;
  min-height: 300px;
  resize: vertical;
  line-height: 1.6;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.8);
}
.editor:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.8), 0 0 10px var(--accent-glow);
}

.section { display: none; }
.section.active { display: block; }

.progress-bar {
  width: 100%;
  height: 6px;
  background: var(--bg-input);
  border-radius: 3px;
  overflow: hidden;
  margin: 8px 0;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.9);
}
.progress-fill {
  height: 100%;
  background: var(--gold-gradient);
  border-radius: 3px;
  transition: width 0.5s;
}

.toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: var(--bg-card);
  border: 1px solid var(--accent);
  color: var(--accent);
  padding: 12px 20px;
  border-radius: 4px;
  font-size: 0.85rem;
  z-index: 1000;
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.3s;
  box-shadow: 0 10px 30px rgba(0,0,0,0.8);
}
.toast.show {
  opacity: 1;
  transform: translateY(0);
}
.toast.error {
  border-color: var(--danger);
  color: var(--danger);
}

@media (max-width: 900px) {
  .app-container { grid-template-columns: 1fr; }
  .sidebar { display: none; }
}
</style>
</head>
<body>
<div class="app-container">
<aside class="sidebar">
<div class="brand">
JobHunt v3
<span>Command Center</span>
</div>
<button class="nav-item active" onclick="showSection('dashboard', this)">
<span class="icon">📊</span> Dashboard
</button>
<button class="nav-item" onclick="window.location.href='https://johnjobsv2.legaisee.com/jobs_today.html'">
    <span class="icon">💼</span> Jobs_Today
</button>
<button class="nav-item" onclick="showSection('pipeline', this)">
<span class="icon">🎯</span> Pipeline
</button>
<button class="nav-item" onclick="showSection('prospect-cue', this)">
<span class="icon">💡</span> Prospect Cue
</button>
<button class="nav-item" onclick="showSection('documents', this)">
<span class="icon">📄</span> Documents
</button>
<button class="nav-item" onclick="showSection('conversations', this)">
<span class="icon">💬</span> Conversations
</button>
<div class="nav-section">
<div class="nav-section-title">Workflow</div>
<button class="nav-item" onclick="showSection('daily', this)">
<span class="icon">☀️</span> Daily Protocol
</button>
<button class="nav-item" onclick="showSection('ai-tools', this)">
<span class="icon">🤖</span> AI Toolkit
</button>
</div>
<div class="nav-section">
<div class="nav-section-title">Tools</div>
<button class="nav-item" onclick="showSection('resume', this)">
<span class="icon">📝</span> Resume Lab
</button>
<button class="nav-item" onclick="showSection('companies', this)">
<span class="icon">🏢</span> Companies
</button>
<button class="nav-item" onclick="showSection('jobs', this)">
<span class="icon">💼</span> Jobs
</button>
<button class="nav-item" onclick="showSection('recruiters', this)">
<span class="icon">🧑‍💼</span> Recruiters
</button>
<button class="nav-item" onclick="showSection('interviews', this)">
<span class="icon">🗓️</span> Interviews
</button>
<button class="nav-item" onclick="showSection('cover', this)">
<span class="icon">✉️</span> Cover Letters
</button>
<button class="nav-item" onclick="showSection('ats', this)">
<span class="icon">🔍</span> ATS Bypass
</button>
<button class="nav-item" onclick="showSection('search', this)">
<span class="icon">🔎</span> Job Search
</button>
<button class="nav-item" onclick="showSection('network', this)">
<span class="icon">🌐</span> Network
</button>
</div>
<div class="nav-section">
<div class="nav-section-title">System</div>
<button class="nav-item" onclick="showSection('settings', this)">
<span class="icon">⚙️</span> Settings
</button>
</div>
<div class="stats-bar">
<div class="stat-row"><span class="label">Applied</span><span class="value" id="stat-applied">0</span></div>
<div class="stat-row"><span class="label">Interviews</span><span class="value" id="stat-interviews">0</span></div>
<div class="stat-row"><span class="label">Pending</span><span class="value" id="stat-pending">0</span></div>
<div class="stat-row"><span class="label">Rejected</span><span class="value" id="stat-rejected">0</span></div>
</div>
</aside>
<main class="main">

<section class="section active" id="dashboard">
  <h1 class="page-title">Command Center</h1>
  <p class="page-subtitle">Your daily mission control for landing the right role</p>
  <div class="quick-actions">
    <button class="action-btn" onclick="showSection('daily', this)">
      <span class="label">Start Here</span>
      <span class="title">Daily Execution</span>
      <span class="desc">Your fill-in-the-blank daily workflow</span>
    </button>
    <button class="action-btn" onclick="showSection('prospect-cue', this)">
      <span class="label">Capture</span>
      <span class="title">Add to Prospect Cue</span>
      <span class="desc">Quick-save jobs as you find them</span>
    </button>
    <button class="action-btn" onclick="showSection('documents', this)">
      <span class="label">Create</span>
      <span class="title">New Document</span>
      <span class="desc">Resume, cover letter, or note</span>
    </button>
    <button class="action-btn" onclick="showSection('conversations', this)">
      <span class="label">Log</span>
      <span class="title">Conversation</span>
      <span class="desc">Track emails, calls, LinkedIn</span>
    </button>
  </div>
  <div class="card-grid">
    <div class="card">
      <div class="card-header">
        <span class="card-title">Today's Targets</span>
        <span class="card-badge">DAILY</span>
      </div>
      <ul class="checklist">
        <li><input type="checkbox" id="t1"><span class="task-text">Find 5 new job listings matching your criteria</span><span class="task-time">20 min</span></li>
        <li><input type="checkbox" id="t2"><span class="task-text">Paste 3-5 listings into Prospect Cue for review</span><span class="task-time">10 min</span></li>
        <li><input type="checkbox" id="t3"><span class="task-text">Review Prospect Cue & move top 2 to Pipeline</span><span class="task-time">15 min</span></li>
        <li><input type="checkbox" id="t4"><span class="task-text">Tailor resume for 1 top-priority role</span><span class="task-time">15 min</span></li>
        <li><input type="checkbox" id="t5"><span class="task-text">Write personalized cover letter</span><span class="task-time">20 min</span></li>
        <li><input type="checkbox" id="t6"><span class="task-text">Apply via direct email or referral (bypass ATS)</span><span class="task-time">10 min</span></li>
        <li><input type="checkbox" id="t7"><span class="task-text">Log any conversations (emails, calls, LinkedIn)</span><span class="task-time">10 min</span></li>
        <li><input type="checkbox" id="t8"><span class="task-text">Follow up on 2 previous applications</span><span class="task-time">10 min</span></li>
      </ul>
      <div class="progress-bar"><div class="progress-fill" id="daily-progress" style="width:0%"></div></div>
      <p style="text-align:right;font-size:0.75rem;color:var(--text-muted);margin-top:4px">Total: ~2 hours</p>
    </div>
    <div class="card">
      <div class="card-header">
        <span class="card-title">Pipeline Health</span>
        <span class="card-badge">LIVE</span>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:8px">
        <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:4px;border:1px solid var(--border)">
          <div style="font-size:2rem;color:var(--accent);font-weight:700" id="dash-applied">0</div>
          <div style="font-size:0.75rem;color:var(--text-muted)">Applied</div>
        </div>
        <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:4px;border:1px solid var(--border)">
          <div style="font-size:2rem;color:var(--info);font-weight:700" id="dash-interview">0</div>
          <div style="font-size:0.75rem;color:var(--text-muted)">Interviews</div>
        </div>
        <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:4px;border:1px solid var(--border)">
          <div style="font-size:2rem;color:var(--warning);font-weight:700" id="dash-pending">0</div>
          <div style="font-size:0.75rem;color:var(--text-muted)">Pending</div>
        </div>
        <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:4px;border:1px solid var(--border)">
          <div style="font-size:2rem;color:var(--danger);font-weight:700" id="dash-rejected">0</div>
          <div style="font-size:0.75rem;color:var(--text-muted)">Rejected</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <span class="card-title">Quick Stats</span>
        <span class="card-badge">OVERVIEW</span>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:8px">
        <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:4px;border:1px solid var(--border)">
          <div style="font-size:1.8rem;color:var(--accent);font-weight:700" id="dash-prospects">0</div>
          <div style="font-size:0.75rem;color:var(--text-muted)">Prospects</div>
        </div>
        <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:4px;border:1px solid var(--border)">
          <div style="font-size:1.8rem;color:var(--info);font-weight:700" id="dash-docs">0</div>
          <div style="font-size:0.75rem;color:var(--text-muted)">Documents</div>
        </div>
        <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:4px;border:1px solid var(--border)">
          <div style="font-size:1.8rem;color:var(--success);font-weight:700" id="dash-convos">0</div>
          <div style="font-size:0.75rem;color:var(--text-muted)">Conversations</div>
        </div>
        <div style="text-align:center;padding:16px;background:var(--bg-input);border-radius:4px;border:1px solid var(--border)">
          <div style="font-size:1.8rem;color:var(--warning);font-weight:700" id="dash-offers">0</div>
          <div style="font-size:0.75rem;color:var(--text-muted)">Offers</div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Recent Activity</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Company</th><th>Role</th><th>Status</th><th>Date</th><th>Method</th></tr></thead>
        <tbody id="recent-activity"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<!-- ==================== PIPELINE ==================== -->
<section class="section" id="pipeline">
  <h1 class="page-title">Pipeline Tracker</h1>
  <p class="page-subtitle">Every application, every touchpoint, every outcome</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header">
      <span class="card-title">Add New Application</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Company Name</label><input type="text" id="p-company" placeholder="e.g. Houston Methodist"></div>
      <div class="form-group"><label>Job Title</label><input type="text" id="p-title" placeholder="e.g. Video Production Manager"></div>
      <div class="form-group"><label>Location / Remote</label><select id="p-location"><option>Remote</option><option>Hybrid</option><option>On-site</option><option>Not specified</option></select></div>
      <div class="form-group"><label>Salary Range (if known)</label><input type="text" id="p-salary" placeholder="e.g. $75K-$95K"></div>
      <div class="form-group"><label>Application Method</label><select id="p-method"><option>Direct Email</option><option>LinkedIn Easy Apply</option><option>Company Website ATS</option><option>Referral</option><option>Recruiter</option><option>Job Board (Indeed, etc.)</option></select></div>
      <div class="form-group"><label>Status</label><select id="p-status"><option value="new">New Lead</option><option value="applied">Applied</option><option value="interview">Interview Scheduled</option><option value="rejected">Rejected</option><option value="ghosted">No Response (Ghosted)</option><option value="offer">Offer Received</option></select></div>
      <div class="form-group" style="grid-column:1/-1"><label>Job Description URL</label><input type="text" id="p-url" placeholder="Paste the job listing URL here"></div>
      <div class="form-group" style="grid-column:1/-1"><label>Notes / Key Requirements</label><textarea id="p-notes" rows="3" placeholder="Copy/paste key requirements. What skills are they asking for?"></textarea></div>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button class="btn btn-primary" onclick="addApplication()">&#10133; Add to Pipeline</button>
      <button class="btn btn-secondary" onclick="clearPipelineForm()">Clear</button>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Active Pipeline</span>
      <div><button class="btn btn-ghost btn-sm" onclick="exportPipeline()">&#128229; Export CSV</button></div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Company</th><th>Role</th><th>Method</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody id="pipeline-body"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
  <div class="card" id="pipeline-detail-card" style="margin-top:20px;display:none">
    <div class="card-header">
      <span class="card-title" id="pd-title">Application Details</span>
      <button class="btn btn-ghost btn-sm" onclick="closePipelineDetail()">&#10005; Close</button>
    </div>
    <div id="pd-content" style="color:var(--text-dim);font-size:0.9rem;line-height:1.7"></div>
    <div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap">
      <button class="btn btn-primary btn-sm" onclick="openJobUrl()">&#128279; Open Job Posting</button>
      <button class="btn btn-secondary btn-sm" onclick="editPipelineStatus()">&#9998; Edit Status</button>
      <button class="btn btn-ghost btn-sm" onclick="copyPipelineNotes()">&#128203; Copy Notes</button>
      <button class="btn btn-ghost btn-sm" onclick="addConversationFromPipeline()">&#9993; Log Conversation</button>
      <button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteCurrentPipelineEntry()">&#128465; Delete</button>
    </div>
  </div>
</section>

<!-- ==================== PROSPECT CUE ==================== -->
<section class="section" id="prospect-cue">
  <h1 class="page-title">Prospect Cue</h1>
  <p class="page-subtitle">Rapid capture for jobs to review. Paste, save, decide later. Move forward or discard.</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header">
      <span class="card-title">Quick Capture</span>
      <span class="card-badge">PASTE & SAVE</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Company Name *</label><input type="text" id="pc-company" placeholder="e.g. Houston Methodist"></div>
      <div class="form-group"><label>Job Title *</label><input type="text" id="pc-title" placeholder="e.g. Video Production Manager"></div>
      <div class="form-group" style="grid-column:1/-1"><label>Job Posting URL</label><input type="text" id="pc-url" placeholder="Paste the full job listing URL"></div>
      <div class="form-group" style="grid-column:1/-1"><label>Job Description / Notes <span style="color:var(--text-muted)">(paste the full JD here)</span></label><textarea id="pc-desc" rows="6" placeholder="Paste the complete job description, requirements, salary info, anything you see. You'll review and decide later."></textarea></div>
      <div class="form-group"><label>Where Found</label><select id="pc-source"><option>LinkedIn Jobs</option><option>Indeed</option><option>ZipRecruiter</option><option>GlassDoor</option><option>SimplyHired</option><option>Company Website</option><option>Email / Newsletter</option><option>Recruiter Outreach</option><option>Referral</option><option>Other Job Board</option><option>Other</option></select></div>
      <div class="form-group"><label>Initial Gut Rating</label><select id="pc-rating"><option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; Strong fit</option><option value="4">&#9733;&#9733;&#9733;&#9733;&#9734; Good fit</option><option value="3" selected>&#9733;&#9733;&#9733;&#9734;&#9734; Maybe</option><option value="2">&#9733;&#9733;&#9734;&#9734;&#9734; Weak fit</option><option value="1">&#9733;&#9734;&#9734;&#9734;&#9734; Long shot</option></select></div>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px;flex-wrap:wrap">
      <button class="btn btn-primary" onclick="addProspect()">&#10133; Add to Cue</button>
      <button class="btn btn-secondary" onclick="clearProspectForm()">Clear</button>
      <button class="btn btn-ghost" onclick="pasteFromClipboard()">&#128203; Paste from Clipboard</button>
    </div>
  </div>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Cue Filters</span></div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <div class="form-group" style="margin-bottom:0;min-width:180px">
        <label style="margin-bottom:4px">Filter by Status</label>
        <select id="pc-filter-status" onchange="renderProspectCue()"><option value="all">All Prospects</option><option value="new">New / Unreviewed</option><option value="reviewed">Reviewed</option><option value="applied">Moved to Pipeline</option><option value="discarded">Discarded</option></select>
      </div>
      <div class="form-group" style="margin-bottom:0;min-width:180px">
        <label style="margin-bottom:4px">Filter by Source</label>
        <select id="pc-filter-source" onchange="renderProspectCue()"><option value="all">All Sources</option><option>LinkedIn Jobs</option><option>Indeed</option><option>ZipRecruiter</option><option>SimplyHired</option><option>Company Website</option><option>Email / Newsletter</option><option>Recruiter Outreach</option><option>Referral</option><option>Other Job Board</option><option>Other</option></select>
      </div>
      <div class="form-group" style="margin-bottom:0;min-width:180px">
        <label style="margin-bottom:4px">Sort by</label>
        <select id="pc-sort" onchange="renderProspectCue()"><option value="newest">Newest First</option><option value="oldest">Oldest First</option><option value="rating-high">Rating: High to Low</option><option value="rating-low">Rating: Low to High</option></select>
      </div>
      <div style="margin-left:auto;display:flex;gap:8px;align-items:center"><span style="color:var(--text-muted);font-size:0.8rem" id="pc-count">0 prospects</span></div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Cue List</span>
      <div style="display:flex;gap:8px">
        <button class="btn btn-ghost btn-sm" onclick="batchDiscard()">&#128465; Discard Selected</button>
        <button class="btn btn-primary btn-sm" onclick="batchMoveToPipeline()">&#10132; Move Selected to Pipeline</button>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th><input type="checkbox" id="pc-select-all" onchange="toggleSelectAll()"></th><th>Company</th><th>Role</th><th>Source</th><th>Rating</th><th>Status</th><th>Date Added</th><th>Actions</th></tr></thead>
        <tbody id="prospect-cue-body"><tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:32px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
  <div class="card" id="pc-review-card" style="margin-top:20px;display:none">
    <div class="card-header">
      <span class="card-title">Review Prospect</span>
      <button class="btn btn-ghost btn-sm" onclick="closeReview()">&#10005; Close</button>
    </div>
    <div id="pc-review-content" style="color:var(--text-dim);font-size:0.9rem;line-height:1.7"></div>
    <div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap">
      <button class="btn btn-primary btn-sm" onclick="moveReviewedToPipeline()">&#10003; Move to Pipeline</button>
      <button class="btn btn-secondary btn-sm" onclick="markReviewed()">&#128065; Mark Reviewed</button>
      <button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="discardReviewed()">&#128465; Discard</button>
    </div>
  </div>
</section>

<!-- ==================== DOCUMENTS ==================== -->
<section class="section" id="documents">
  <h1 class="page-title">Document Manager</h1>
  <p class="page-subtitle">Store resumes, cover letters, and notes. Export to PDF or Google Docs.</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header">
      <span class="card-title">Create New Document</span>
      <span class="card-badge">STORE ON SERVER</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Document Name *</label><input type="text" id="doc-name" placeholder="e.g. Resume_HoustonMethodist_VideoProdMgr"></div>
      <div class="form-group"><label>Document Type</label><select id="doc-type"><option value="resume">Resume</option><option value="cover_letter">Cover Letter</option><option value="other">Other (Notes, Script, etc.)</option></select></div>
      <div class="form-group"><label>Company (if job-specific)</label><input type="text" id="doc-company" placeholder="e.g. Houston Methodist"></div>
      <div class="form-group"><label>Job Title (if job-specific)</label><input type="text" id="doc-job-title" placeholder="e.g. Video Production Manager"></div>
    </div>
    <div class="form-group" style="margin-top:8px">
      <label>Document Content</label>
      <textarea id="doc-content" class="editor" rows="12" placeholder="Paste or write your document content here. This is stored on the server and persists across sessions."></textarea>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px;flex-wrap:wrap">
      <button class="btn btn-primary" onclick="saveDocument()">&#128190; Save to Server</button>
      <button class="btn btn-secondary" onclick="clearDocForm()">Clear</button>
      <button class="btn btn-ghost" onclick="pasteDocFromClipboard()">&#128203; Paste from Clipboard</button>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Saved Documents</span>
      <div style="display:flex;gap:8px">
        <select id="doc-filter-type" onchange="loadDocuments()" style="background:var(--background-image);border:1px solid var(--border);color:var(--text);padding:6px 12px;border-radius:6px;font-size:0.8rem">
          <option value="all">All Types</option>
          <option value="resume">Resumes</option>
          <option value="cover_letter">Cover Letters</option>
          <option value="other">Other</option>
        </select>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Type</th><th>Company</th><th>Role</th><th>Modified</th><th>Actions</th></tr></thead>
        <tbody id="documents-body"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
  <div class="card" id="doc-editor-card" style="margin-top:20px;display:none">
    <div class="card-header">
      <span class="card-title" id="doc-editor-title">Edit Document</span>
      <button class="btn btn-ghost btn-sm" onclick="closeDocEditor()">&#10005; Close</button>
    </div>
    <div style="margin-bottom:12px">
      <input type="hidden" id="doc-edit-id">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
        <div class="form-group" style="margin-bottom:0"><label>Document Name</label><input type="text" id="doc-edit-name"></div>
        <div class="form-group" style="margin-bottom:0"><label>Type</label><select id="doc-edit-type"><option value="resume">Resume</option><option value="cover_letter">Cover Letter</option><option value="other">Other</option></select></div>
      </div>
    </div>
    <textarea id="doc-edit-content" class="editor" rows="15"></textarea>
    <div style="margin-top:16px;display:flex;gap:12px;flex-wrap:wrap">
      <button class="btn btn-primary btn-sm" onclick="updateDocument()">&#128190; Save Changes</button>
      <button class="btn btn-success btn-sm" onclick="copyDocContent()">&#128203; Copy Content</button>
      <button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteCurrentDocument()">&#128465; Delete</button>
    </div>
  </div>
</section>

<!-- ==================== CONVERSATIONS ==================== -->
<section class="section" id="conversations">
  <h1 class="page-title">Conversation Tracker</h1>
  <p class="page-subtitle">Log every email, LinkedIn message, phone call, and meeting. Never lose track of a conversation.</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header">
      <span class="card-title">Log New Conversation</span>
      <span class="card-badge">TRACK EVERYTHING</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Contact Name</label><input type="text" id="conv-contact" placeholder="e.g. Sarah Johnson"></div>
      <div class="form-group"><label>Contact Role</label><input type="text" id="conv-role" placeholder="e.g. Hiring Manager / Recruiter"></div>
      <div class="form-group"><label>Channel</label><select id="conv-channel"><option value="email">Email</option><option value="linkedin">LinkedIn Message</option><option value="phone">Phone Call</option><option value="text">Text / SMS</option><option value="meeting">In-Person Meeting</option><option value="video">Video Call (Zoom/Teams)</option><option value="other">Other</option></select></div>
      <div class="form-group"><label>Direction</label><select id="conv-direction"><option value="outbound">I reached out</option><option value="inbound">They reached out</option></select></div>
      <div class="form-group"><label>Subject / Topic</label><input type="text" id="conv-subject" placeholder="e.g. Re: Video Production Manager Application"></div>
      <div class="form-group"><label>Date</label><input type="date" id="conv-date" value=""></div>
      <div class="form-group" style="grid-column:1/-1">
        <label>Conversation Content <span style="color:var(--text-muted)">(paste the full email/message here)</span></label>
        <textarea id="conv-content" rows="6" placeholder="Paste the full email, LinkedIn message, or write a summary of the phone call."></textarea>
      </div>
      <div class="form-group" style="grid-column:1/-1">
        <label>Follow-Up Needed?</label>
        <div style="display:flex;gap:16px;align-items:center">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;text-transform:none;font-size:0.9rem"><input type="checkbox" id="conv-followup" style="width:auto"> Yes, I need to follow up</label>
          <input type="date" id="conv-followup-date" placeholder="Follow-up date" style="max-width:200px">
        </div>
      </div>
      <div class="form-group" style="grid-column:1/-1">
        <label>Notes</label>
        <input type="text" id="conv-notes" placeholder="Any additional notes about this conversation">
      </div>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button class="btn btn-primary" onclick="saveConversation()">&#9993; Save Conversation</button>
      <button class="btn btn-secondary" onclick="clearConvForm()">Clear</button>
      <button class="btn btn-ghost" onclick="pasteConvFromClipboard()">&#128203; Paste from Clipboard</button>
    </div>
  </div>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header">
      <span class="card-title">Filter Conversations</span>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <div class="form-group" style="margin-bottom:0;min-width:180px">
        <label style="margin-bottom:4px">Filter by Channel</label>
        <select id="conv-filter-channel" onchange="loadConversations()"><option value="all">All Channels</option><option value="email">Email</option><option value="linkedin">LinkedIn</option><option value="phone">Phone</option><option value="text">Text</option><option value="meeting">Meeting</option><option value="video">Video Call</option><option value="other">Other</option></select>
      </div>
      <div class="form-group" style="margin-bottom:0;min-width:180px">
        <label style="margin-bottom:4px">Filter by Direction</label>
        <select id="conv-filter-direction" onchange="loadConversations()"><option value="all">All</option><option value="outbound">My Outbound</option><option value="inbound">Their Inbound</option></select>
      </div>
      <div class="form-group" style="margin-bottom:0;min-width:180px">
        <label style="margin-bottom:4px">Show Only</label>
        <select id="conv-filter-followup" onchange="loadConversations()"><option value="all">All Conversations</option><option value="followup">Follow-Up Needed</option></select>
      </div>
      <div style="margin-left:auto"><span style="color:var(--text-muted);font-size:0.8rem" id="conv-count">0 conversations</span></div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Conversation History</span>
    </div>
    <div id="conversations-body" class="conversation-thread">
      <p style="text-align:center;color:var(--text-muted);padding:32px">Loading conversations...</p>
    </div>
  </div>
</section>

<!-- ==================== DAILY WORKFLOW ==================== -->
<section class="section" id="daily">
  <h1 class="page-title">Daily Execution Protocol</h1>
  <p class="page-subtitle">A repeatable 90-minute system. Fill in the blanks. Execute. Repeat.</p>
  <div class="tabs">
    <button class="tab-btn active" onclick="showTab('morning', this)">Morning Block</button>
    <button class="tab-btn" onclick="showTab('afternoon', this)">Afternoon Block</button>
    <button class="tab-btn" onclick="showTab('evening', this)">Evening Block</button>
  </div>
  <div class="tab-panel active" id="tab-morning">
    <div class="card">
      <div class="card-header"><span class="card-title">&#9728;&#65039; Morning Block &mdash; 8:00&ndash;9:30 AM (90 min)</span><span class="card-badge">DO FIRST</span></div>
      <div class="workflow-step"><div class="step-num">1</div><div class="step-content"><h4>Scan &amp; Curate (20 min)</h4><p>Open these tabs simultaneously. Use AI to speed-read listings.</p><div style="margin-top:8px"><a href="https://www.linkedin.com/jobs/" target="_blank" style="color:var(--accent);font-size:0.8rem">LinkedIn Jobs</a> | <a href="https://indeed.com" target="_blank" style="color:var(--accent);font-size:0.8rem">Indeed</a> | <a href="https://www.ziprecruiter.com" target="_blank" style="color:var(--accent);font-size:0.8rem">ZipRecruiter</a> | <a href="https://www.simplyhired.com" target="_blank" style="color:var(--accent);font-size:0.8rem">SimplyHired</a></div><span class="tool-tag">AI: Claude &mdash; paste 5 JDs, ask "Which 2 best match my profile?"</span></div></div>
      <div class="workflow-step"><div class="step-num">2</div><div class="step-content"><h4>ATS Keyword Harvest (10 min)</h4><p>For your top 2 picks, paste the job description into Claude/ChatGPT with this prompt:</p><div class="copy-block"><button class="copy-btn" onclick="copyText(this)">Copy</button>Extract the top 10 keywords, required skills, and qualifications from this job description. Format as a bulleted list. Identify which requirements are "must-have" vs "nice-to-have".</div><span class="tool-tag">AI: Claude or ChatGPT</span></div></div>
      <div class="workflow-step"><div class="step-num">3</div><div class="step-content"><h4>Tailor Resume (15 min)</h4><p>Using the keyword list, adjust your master resume. Match their language exactly.</p><ul style="margin:8px 0 0 20px;color:var(--text-dim);font-size:0.85rem"><li>If they say "Adobe Premiere Pro" &mdash; don&apos;t say "video editing software"</li><li>If they say "FAA Part 107" &mdash; include it verbatim</li><li>If they say "live broadcast" &mdash; use that exact phrase</li><li>Keep formatting clean: single column, standard fonts, no tables</li></ul><span class="tool-tag">AI: ChatGPT &mdash; "Rewrite this bullet to include: [keyword 1], [keyword 2]"</span></div></div>
      <div class="workflow-step"><div class="step-num">4</div><div class="step-content"><h4>Write Cover Letter (20 min)</h4><p>Use the fill-in-the-blank template. Personalize with one specific detail from their website or JD.</p><span class="tool-tag">AI: Claude &mdash; paste JD + your resume, ask for a 3-paragraph cover letter</span></div></div>
      <div class="workflow-step"><div class="step-num">5</div><div class="step-content"><h4>Apply &mdash; Prefer Direct Over ATS (15 min)</h4><p><strong>Priority order:</strong> Direct email &rarr; LinkedIn DM &rarr; Referral &rarr; Company website (ATS last resort)</p><ul style="margin:8px 0 0 20px;color:var(--text-dim);font-size:0.85rem"><li>Find the hiring manager on LinkedIn</li><li>Send connection request with note</li><li>If no direct contact, apply via company site but ALSO email careers@company.com</li></ul><span class="tool-tag">Tool: Hunter.io or Apollo.io for email finding</span></div></div>
      <div class="workflow-step"><div class="step-num">6</div><div class="step-content"><h4>Log Everything (10 min)</h4><p>Add the application to your Pipeline Tracker. Copy/paste the JD into notes. Log any conversations in the Conversation Tracker.</p><span class="tool-tag">This dashboard &mdash; Pipeline + Conversations tabs</span></div></div>
    </div>
  </div>
  <div class="tab-panel" id="tab-afternoon">
    <div class="card">
      <div class="card-header"><span class="card-title">&#9729;&#65039; Afternoon Block &mdash; 1:00&ndash;2:00 PM (60 min)</span><span class="card-badge">NETWORKING</span></div>
      <div class="workflow-step"><div class="step-num">1</div><div class="step-content"><h4>LinkedIn Outreach (20 min)</h4><p>Send 3 personalized connection requests to hiring managers or team members at target companies.</p><span class="tool-tag">Script in Copy/Paste Scripts tab</span></div></div>
      <div class="workflow-step"><div class="step-num">2</div><div class="step-content"><h4>Follow-Up Round (20 min)</h4><p>For any application older than 7 days with no response, send a polite follow-up. Log it in Conversations.</p><span class="tool-tag">Script in Copy/Paste Scripts tab</span></div></div>
      <div class="workflow-step"><div class="step-num">3</div><div class="step-content"><h4>Portfolio / Content Touch (20 min)</h4><p>Post one piece of content to LinkedIn. Keeps you visible and signals you&apos;re active.</p><span class="tool-tag">AI: ChatGPT &mdash; "Write a 150-word LinkedIn post about [recent project insight]"</span></div></div>
    </div>
  </div>
  <div class="tab-panel" id="tab-evening">
    <div class="card">
      <div class="card-header"><span class="card-title">&#127769; Evening Block &mdash; 7:00&ndash;7:30 PM (30 min)</span><span class="card-badge">REVIEW &amp; PLAN</span></div>
      <div class="workflow-step"><div class="step-num">1</div><div class="step-content"><h4>Review Responses (10 min)</h4><p>Check email, LinkedIn, and voicemail. Update pipeline statuses. Log new conversations. Celebrate any wins.</p></div></div>
      <div class="workflow-step"><div class="step-num">2</div><div class="step-content"><h4>Plan Tomorrow (10 min)</h4><p>Identify 3-5 new prospects to add to the cue. Check for any follow-ups due tomorrow.</p></div></div>
      <div class="workflow-step"><div class="step-num">3</div><div class="step-content"><h4>Quick Skill Touch (10 min)</h4><p>Watch one tutorial, read one article, or practice one skill relevant to your target roles.</p></div></div>
    </div>
  </div>
</section>

<!-- ==================== AI TOOLS ==================== -->
<section class="section" id="ai-tools">
  <h1 class="page-title">AI Toolkit</h1>
  <p class="page-subtitle">Accelerate your job search with AI-powered assistance</p>
  <div class="card-grid">
    <div class="card">
      <div class="card-header"><span class="card-title">Claude (Anthropic)</span><span class="card-badge">RECOMMENDED</span></div>
      <p style="color:var(--text-dim);font-size:0.85rem;margin-bottom:12px">Best for: Resume tailoring, cover letter writing, JD analysis, interview prep</p>
      <div style="margin-bottom:12px">
        <input type="password" id="api-claude" placeholder="Enter API Key (sk-ant-...)" style="width:100%;background:var(--bg-dark);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:6px;font-size:0.8rem">
      </div>
      <button class="btn btn-primary btn-sm" onclick="saveAPIKey('claude')">Save Key</button>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">ChatGPT (OpenAI)</span><span class="card-badge">ALTERNATIVE</span></div>
      <p style="color:var(--text-dim);font-size:0.85rem;margin-bottom:12px">Best for: Quick content generation, brainstorming, general assistance</p>
      <div style="margin-bottom:12px">
        <input type="password" id="api-openai" placeholder="Enter API Key (sk-...)" style="width:100%;background:var(--bg-dark);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:6px;font-size:0.8rem">
      </div>
      <button class="btn btn-primary btn-sm" onclick="saveAPIKey('openai')">Save Key</button>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Quick Prompts</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:12px">
      <button class="btn btn-secondary" onclick="copyPrompt('ats')">Copy ATS Keyword Extractor Prompt</button>
      <button class="btn btn-secondary" onclick="copyPrompt('resume')">Copy Resume Tailor Prompt</button>
      <button class="btn btn-secondary" onclick="copyPrompt('cover')">Copy Cover Letter Prompt</button>
      <button class="btn btn-secondary" onclick="copyPrompt('interview')">Copy Interview Prep Prompt</button>
    </div>
  </div>
</section>

<!-- ==================== RESUME LAB ==================== -->
<section class="section" id="resume">
  <h1 class="page-title">Resume Lab</h1>
  <p class="page-subtitle">Tailor your resume for each application. Store master and tailored versions.</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Master Resume</span><span class="card-badge">SOURCE</span></div>
    <div class="form-group">
      <textarea id="master-resume" class="editor" rows="15" placeholder="Paste your master resume here. This serves as the base for all tailored versions."></textarea>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button class="btn btn-primary" onclick="saveMasterResume()">&#128190; Save Master</button>
      <button class="btn btn-secondary" onclick="copyMasterResume()">&#128203; Copy</button>
      <button class="btn btn-ghost" onclick="pasteMasterResume()">&#128203; Paste from Clipboard</button>
    </div>
  </div>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Generate Tailored Resume</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Target Role</label><input type="text" id="resume-target-role" placeholder="e.g. Senior Software Engineer"></div>
      <div class="form-group"><label>Company</label><input type="text" id="resume-company" placeholder="e.g. Google"></div>
    </div>
    <div class="form-group" style="margin-top:8px">
      <label>Job Description</label>
      <textarea id="resume-jd" class="editor" rows="8" placeholder="Paste the job description here"></textarea>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button class="btn btn-primary" onclick="generateTailoredResume()">&#10024; Generate</button>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Tailored Resumes</span></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Target Role</th><th>Company</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody id="tailored-resumes-body"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
  <div class="card" id="resume-output" style="display:none;margin-top:20px">
    <div class="card-header"><span class="card-title" id="resume-output-title">Tailored Resume</span></div>
    <textarea id="resume-generated-text" class="editor" rows="18" readonly></textarea>
    <div style="margin-top:16px"><button class="btn btn-secondary" onclick="copyTailoredResume()">&#128203; Copy</button></div>
  </div>
</section>
<!-- ==================== COMPANIES ==================== -->
<section class="section" id="companies">
  <h1 class="page-title">Companies</h1>
  <p class="page-subtitle">Track companies you're targeting or have applied to</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Add Company</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Name *</label><input type="text" id="co-name" placeholder="e.g. Community Bible Church"></div>
      <div class="form-group"><label>Industry</label><input type="text" id="co-industry" placeholder="e.g. Nonprofit / Media"></div>
      <div class="form-group"><label>Location</label><input type="text" id="co-location" placeholder="e.g. San Antonio, TX"></div>
      <div class="form-group"><label>Size</label><input type="text" id="co-size" placeholder="e.g. 50-100 employees"></div>
      <div class="form-group"><label>Website</label><input type="text" id="co-website" placeholder="https://..."></div>
      <div class="form-group"><label>Status</label><select id="co-status"><option value="active">Active</option><option value="watching">Watching</option><option value="closed">Closed</option></select></div>
    </div>
    <div class="form-group" style="margin-top:8px"><label>Description / Notes</label><textarea id="co-description" rows="3"></textarea></div>
    <div style="margin-top:16px"><button class="btn btn-primary" onclick="addCompany()">➕ Add Company</button></div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">All Companies</span></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Industry</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="companies-body"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<!-- ==================== JOBS ==================== -->
<section class="section" id="jobs">
  <h1 class="page-title">Jobs</h1>
  <p class="page-subtitle">Specific job postings tied to companies</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Add Job</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Job Title *</label><input type="text" id="job-title" placeholder="e.g. Audio Engineer"></div>
      <div class="form-group"><label>Company</label><select id="job-company"><option value="">-- Select Company --</option></select></div>
      <div class="form-group"><label>Location</label><input type="text" id="job-location" placeholder="e.g. Remote"></div>
      <div class="form-group"><label>Employment Type</label><select id="job-employment-type"><option>Full-time</option><option>Part-time</option><option>Contract</option><option>Freelance</option></select></div>
      <div class="form-group"><label>Salary Range</label><input type="text" id="job-salary-range" placeholder="e.g. $60K-$75K"></div>
      <div class="form-group"><label>Status</label><select id="job-status"><option value="open">Open</option><option value="closed">Closed</option><option value="applied">Applied</option></select></div>
      <div class="form-group" style="grid-column:1/-1"><label>Source URL</label><input type="text" id="job-source-url" placeholder="Link to the job posting"></div>
    </div>
    <div class="form-group" style="margin-top:8px"><label>Description</label><textarea id="job-description" rows="4"></textarea></div>
    <div style="margin-top:16px"><button class="btn btn-primary" onclick="addJob()">➕ Add Job</button></div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">All Jobs</span></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Title</th><th>Company</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="jobs-body"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<!-- ==================== RECRUITERS ==================== -->
<section class="section" id="recruiters">
  <h1 class="page-title">Recruiters</h1>
  <p class="page-subtitle">Contacts at recruiting firms and internal talent teams</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Add Recruiter</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Name *</label><input type="text" id="rec-name" placeholder="e.g. Sarah Johnson"></div>
      <div class="form-group"><label>Company</label><select id="rec-company"><option value="">-- Select Company --</option></select></div>
      <div class="form-group"><label>Role</label><input type="text" id="rec-role" placeholder="e.g. Talent Acquisition"></div>
      <div class="form-group"><label>Email</label><input type="email" id="rec-email"></div>
      <div class="form-group"><label>Phone</label><input type="text" id="rec-phone"></div>
      <div class="form-group"><label>LinkedIn</label><input type="text" id="rec-linkedin"></div>
    </div>
    <div class="form-group" style="margin-top:8px"><label>Notes</label><textarea id="rec-notes" rows="3"></textarea></div>
    <div style="margin-top:16px"><button class="btn btn-primary" onclick="addRecruiter()">➕ Add Recruiter</button></div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">All Recruiters</span></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Company</th><th>Role</th><th>Email</th><th>Actions</th></tr></thead>
        <tbody id="recruiters-body"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<!-- ==================== INTERVIEWS ==================== -->
<section class="section" id="interviews">
  <h1 class="page-title">Interviews</h1>
  <p class="page-subtitle">Track interview schedule and outcomes, tied to your applications</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Schedule Interview</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Application *</label><select id="int-application"><option value="">-- Select Application --</option></select></div>
      <div class="form-group"><label>Recruiter</label><select id="int-recruiter"><option value="">-- None --</option></select></div>
      <div class="form-group"><label>Interview Type</label><select id="int-type"><option>Phone Screen</option><option>Technical</option><option>Onsite</option><option>Final Round</option><option>Other</option></select></div>
      <div class="form-group"><label>Scheduled Date/Time</label><input type="datetime-local" id="int-scheduled-at"></div>
    </div>
    <div class="form-group" style="margin-top:8px"><label>Notes</label><textarea id="int-notes" rows="3"></textarea></div>
    <div style="margin-top:16px"><button class="btn btn-primary" onclick="addInterview()">➕ Schedule Interview</button></div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">All Interviews</span></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Type</th><th>Application</th><th>Scheduled</th><th>Outcome</th><th>Actions</th></tr></thead>
        <tbody id="interviews-body"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<!-- ==================== COVER LETTERS ==================== -->
<section class="section" id="cover">
  <h1 class="page-title">Cover Letters</h1>
  <p class="page-subtitle">Generate personalized cover letters for each application.</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Generate Cover Letter</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Company</label><input type="text" id="cl-company"></div>
      <div class="form-group"><label>Job Title</label><input type="text" id="cl-title"></div>
      <div class="form-group"><label>Hiring Manager</label><input type="text" id="cl-hiring-manager"></div>
      <div class="form-group"><label>Tone</label><select id="cl-tone"><option>Professional</option><option>Casual</option><option>Enthusiastic</option></select></div>
    </div>
    <div class="form-group" style="margin-top:8px">
      <label>Job Description</label>
      <textarea id="cl-jd" class="editor" rows="8" placeholder="Paste the job description here"></textarea>
    </div>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button class="btn btn-primary" onclick="generateCoverLetter()">&#10024; Generate</button>
      <button class="btn btn-secondary" onclick="copyCoverLetter()">&#128203; Copy</button>
    </div>
  </div>
  <div class="card" id="cl-output" style="display:none">
    <div class="card-header"><span class="card-title">Generated Cover Letter</span></div>
    <textarea id="cl-generated-text" class="editor" rows="15"></textarea>
    <div style="margin-top:16px;display:flex;gap:12px">
      <button class="btn btn-primary" onclick="saveCoverLetter()">&#128190; Save</button>
      <button class="btn btn-secondary" onclick="copyCoverLetter()">&#128203; Copy</button>
    </div>
  </div>
  <div class="card" style="margin-top:20px">
    <div class="card-header"><span class="card-title">Saved Cover Letters</span></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Company</th><th>Title</th><th>Tone</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody id="cover-letters-body"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<!-- ==================== ATS BYPASS ==================== -->
<section class="section" id="ats">
  <h1 class="page-title">ATS Bypass</h1>
  <p class="page-subtitle">Strategies to get past automated screening systems</p>
  <div class="card-grid">
    <div class="card">
      <div class="card-header"><span class="card-title">Keyword Extractor</span></div>
      <p style="color:var(--text-dim);font-size:0.85rem;margin-bottom:12px">Paste a job description to extract ATS keywords</p>
      <textarea id="ats-jd" class="editor" rows="8" placeholder="Paste job description here"></textarea>
      <div style="margin-top:12px">
        <button class="btn btn-primary" onclick="extractKeywords()">&#128269; Extract Keywords</button>
      </div>
      <div id="ats-keywords" style="margin-top:12px;color:var(--text-dim);font-size:0.85rem"></div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">ATS Score Simulator</span></div>
      <p style="color:var(--text-dim);font-size:0.85rem;margin-bottom:12px">Check how well your resume matches a job description</p>
      <textarea id="ats-resume" class="editor" rows="6" placeholder="Paste your resume here"></textarea>
      <div style="margin-top:12px">
        <button class="btn btn-primary" onclick="simulateATSScore()">&#128221; Score Resume</button>
      </div>
      <div id="ats-score" style="margin-top:12px;color:var(--text-dim);font-size:0.85rem"></div>
    </div>
  </div>
</section>

<!-- ==================== JOB SEARCH ==================== -->
<section class="section" id="search">
  <h1 class="page-title">Job Search</h1>
  <p class="page-subtitle">Saved search queries and job board links</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Add Saved Search</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Name</label><input type="text" id="ss-name"></div>
      <div class="form-group"><label>URL</label><input type="text" id="ss-url"></div>
    </div>
    <div style="margin-top:16px">
      <button class="btn btn-primary" onclick="addSavedSearch()">&#10133; Add Search</button>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Saved Searches</span></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>URL</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody id="saved-searches-body"><tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<!-- ==================== NETWORK ==================== -->
<section class="section" id="network">
  <h1 class="page-title">Network</h1>
  <p class="page-subtitle">Target companies and contacts</p>
  <div class="card" style="margin-bottom:20px">
    <div class="card-header"><span class="card-title">Add Target Company</span></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      <div class="form-group"><label>Company</label><input type="text" id="tc-company"></div>
      <div class="form-group"><label>Target Role</label><input type="text" id="tc-role"></div>
      <div class="form-group"><label>Hiring Manager</label><input type="text" id="tc-hiring-manager"></div>
    </div>
    <div class="form-group" style="margin-top:8px">
      <label>Notes</label>
      <textarea id="tc-notes" rows="3"></textarea>
    </div>
    <div style="margin-top:16px">
      <button class="btn btn-primary" onclick="addTargetCompany()">&#10133; Add Company</button>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Target Companies</span></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Company</th><th>Target Role</th><th>Hiring Manager</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody id="target-companies-body"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<!-- ==================== SETTINGS ==================== -->
<section class="section" id="settings">
  <h1 class="page-title">Settings</h1>
  <p class="page-subtitle">Configure your profile and preferences</p>
  <div class="card-grid">
    <div class="card">
      <div class="card-header"><span class="card-title">Profile</span></div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div class="form-group"><label>Name</label><input type="text" id="set-name"></div>
        <div class="form-group"><label>Email</label><input type="email" id="set-email"></div>
        <div class="form-group"><label>Phone</label><input type="text" id="set-phone"></div>
        <div class="form-group"><label>LinkedIn</label><input type="text" id="set-linkedin"></div>
        <div class="form-group"><label>Portfolio</label><input type="text" id="set-portfolio"></div>
        <div class="form-group"><label>Target Salary</label><input type="text" id="set-salary"></div>
        <div class="form-group"><label>Work Arrangement</label><input type="text" id="set-arrangement"></div>
      </div>
      <div style="margin-top:16px">
        <button class="btn btn-primary" onclick="saveSettings()">&#128190; Save Profile</button>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">&#128190; Data Management</span><span class="card-badge">BACKUP</span></div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="padding:12px;background:var(--bg-dark);border-radius:6px">
          <div style="font-weight:600;color:var(--text);font-size:0.9rem;margin-bottom:4px">Export All Data</div>
          <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:8px">Download your entire database as JSON</p>
          <button class="btn btn-primary btn-sm" onclick="exportAllData()">&#128229; Export JSON</button>
        </div>
        <div style="padding:12px;background:var(--bg-dark);border-radius:6px;border:1px solid var(--danger)">
          <div style="font-weight:600;color:var(--danger);font-size:0.9rem;margin-bottom:4px">&#9888;&#65039; Danger Zone</div>
          <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:8px">Clear all data. This cannot be undone.</p>
          <button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" disabled title="This feature is not available in production">&#128465; Clear All Data</button>
        </div>
      </div>
    </div>
  </div>
</section>
</main>
</div>
<div class="detail-overlay" id="detail-overlay" onclick="if(event.target===this)closeDetailPanel()">
  <div class="detail-panel">
    <h2 id="detail-panel-title">Details</h2>
    <div id="detail-panel-fields"></div>
    <div class="detail-actions">
      <button class="btn btn-primary" onclick="saveDetailPanel()">&#128190; Save</button>
      <button class="btn btn-ghost" onclick="closeDetailPanel()">&#10005; Close</button>
    </div>
  </div>
</div>
<script>
const API_BASE = '/api/';

async function apiGet(path) {
    const res = await fetch(API_BASE + path, {
        headers: { 'Accept': 'application/json' }
    });
    const text = await res.text();
    let json;
    try {
        json = JSON.parse(text);
    } catch(e) {
        throw new Error('API returned non-JSON response');
    }
    if (!res.ok) {
        throw new Error(json.error?.message || json.error || 'API request failed');
    }
    return json;
}

async function apiPost(path, data) {
    const res = await fetch(API_BASE + path, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.error?.message || json.error || 'API request failed');
    }
    return json;
}

async function apiPut(path, data) {
    const res = await fetch(API_BASE + path, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.error?.message || json.error || 'API request failed');
    }
    return json;
}

async function apiDelete(path) {
    const res = await fetch(API_BASE + path, { method: 'DELETE' });
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.error?.message || json.error || 'API request failed');
    }
    return json;
}

const detailConfig = {
    applications: {
        apiPath: 'applications',
        title: (data) => (data.company || 'Unknown') + ' — ' + (data.title || ''),
        onSave: () => { if (typeof loadPipeline === 'function') loadPipeline(); },
        fields: [
            { key: 'company', label: 'Company', type: 'text' },
            { key: 'title', label: 'Job Title', type: 'text' },
            { key: 'location', label: 'Location', type: 'text' },
            { key: 'salary', label: 'Salary', type: 'text' },
            { key: 'method', label: 'Method', type: 'text' },
            { key: 'status', label: 'Status', type: 'select', options: ['new', 'applied', 'interview', 'rejected', 'ghosted', 'offer'] },
            { key: 'date_applied', label: 'Date Applied', type: 'text' },
            { key: 'url', label: 'Job URL', type: 'text' },
            { key: 'notes', label: 'Notes', type: 'textarea', fullHeight: true }
        ]
    }
};

let currentDetail = { entityType: null, id: null };

function openDetailPanel(entityType, id) {
    const config = detailConfig[entityType];
    if (!config) { console.error('No detail config for', entityType); return; }
    apiGet(config.apiPath).then(res => {
        const items = res.data?.items || res.data || [];
        const data = items.find(x => x.id == id);
        if (!data) { showToast('Record not found'); return; }
        currentDetail = { entityType, id, data };
        document.getElementById('detail-panel-title').textContent = config.title(data);
        const fieldsHtml = config.fields.map(f => {
            const val = (data[f.key] ?? '').toString().replace(/"/g, '&quot;');
            if (f.type === 'select') {
                const opts = f.options.map(o => `<option value="${o}" ${data[f.key] === o ? 'selected' : ''}>${o}</option>`).join('');
                return `<div class="detail-field"><label>${f.label}</label><select data-field="${f.key}">${opts}</select></div>`;
            }
            if (f.type === 'textarea') {
                return `<div class="detail-field"><label>${f.label}</label><textarea data-field="${f.key}" class="${f.fullHeight ? 'full-height' : ''}">${data[f.key] ?? ''}</textarea></div>`;
            }
            return `<div class="detail-field"><label>${f.label}</label><input type="text" data-field="${f.key}" value="${val}"></div>`;
        }).join('');
        document.getElementById('detail-panel-fields').innerHTML = fieldsHtml;
        document.getElementById('detail-overlay').classList.add('open');
    }).catch(e => { console.error('Detail load error:', e); showToast('Failed to load details'); });
}

function closeDetailPanel() {
    document.getElementById('detail-overlay').classList.remove('open');
    currentDetail = { entityType: null, id: null };
}

async function saveDetailPanel() {
    const config = detailConfig[currentDetail.entityType];
    if (!config) return;
    const payload = {};
    document.querySelectorAll('#detail-panel-fields [data-field]').forEach(el => {
        payload[el.getAttribute('data-field')] = el.value;
    });
    try {
        await apiPut(config.apiPath + '/' + currentDetail.id, payload);
        showToast('Saved');
        closeDetailPanel();
        if (config.onSave) config.onSave();
    } catch (e) {
        console.error('Save error:', e);
        showToast('Save failed');
    }
}

function showSection(id, el) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    if (el) el.classList.add('active');
    window.scrollTo(0, 0);
    refreshSectionData(id);
}

function showTab(tabId, el) {
    const panelId = 'tab-' + tabId;
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById(panelId).classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    if (el) el.classList.add('active');
}

function refreshSectionData(id) {
    const loaders = {
        'dashboard': loadDashboard,
        'pipeline': loadPipeline,
        'prospect-cue': loadProspectCue,
        'documents': loadDocuments,
        'conversations': loadConversations,
        'resume': loadTailoredResumes,
        'cover': loadCoverLetters,
        'search': loadSavedSearches,
        'network': loadTargetCompanies,
        'settings': loadSettings,
        'companies': loadCompanies,
        'jobs': loadJobs,
        'recruiters': loadRecruiters,
        'interviews': loadInterviews
    };
    if (loaders[id]) loaders[id]();
}

function showToast(msg, isError) {
    let t = document.getElementById('toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'toast';
        t.className = 'toast';
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.classList.toggle('error', !!isError);
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

function copyText(btn) {
    const block = btn.closest('.copy-block');
    const cloned = block.cloneNode(true);
    const button = cloned.querySelector('button');
    if (button) button.remove();
    const text = cloned.textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = 'Copy', 1500);
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => showToast('Copied to clipboard'));
}

function readMetadata(row) {
    if (!row || !row.metadata) return {};
    if (typeof row.metadata === 'object') return row.metadata;
    try { return JSON.parse(row.metadata); } catch (e) { return {}; }
}

function updateDailyProgress() {
    const checks = document.querySelectorAll('#dashboard input[type="checkbox"]');
    const checked = document.querySelectorAll('#dashboard input[type="checkbox"]:checked');
    const pct = checks.length ? Math.round((checked.length / checks.length) * 100) : 0;
    const bar = document.getElementById('daily-progress');
    if (bar) bar.style.width = pct + '%';
}

document.addEventListener('change', function(e) {
    if (e.target.closest('#dashboard') && e.target.type === 'checkbox') {
        updateDailyProgress();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('conv-date');
    if (dateInput && !dateInput.value) dateInput.value = today;
    loadSettings();
    loadDashboard();
});

async function loadDashboard() {
    try {
        const appsRes = await apiGet('applications');
        const prospectsRes = await apiGet('prospects');
        let docsRes = { data: { items: [] } };
        let convosRes = { data: { items: [] } };
        try { docsRes = await apiGet('documents'); } catch(e) { }
        try { convosRes = await apiGet('conversations'); } catch(e) { }

        const apps = appsRes.data?.items || appsRes.data || [];
        const prospects = prospectsRes.data?.items || prospectsRes.data || [];
        const docs = docsRes.data?.items || docsRes.data || [];
        const convos = convosRes.data?.items || convosRes.data || [];

        const applied = apps.filter(a => a.status === 'applied').length;
        const interview = apps.filter(a => a.status === 'interview').length;
        const pending = apps.filter(a => a.status === 'applied' || a.status === 'interview').length;
        const rejected = apps.filter(a => a.status === 'rejected').length;
        const offer = apps.filter(a => a.status === 'offer').length;

        const statApplied = document.getElementById('stat-applied');
        const statInterviews = document.getElementById('stat-interviews');
        const statPending = document.getElementById('stat-pending');
        const statRejected = document.getElementById('stat-rejected');
        if (statApplied) statApplied.textContent = applied;
        if (statInterviews) statInterviews.textContent = interview;
        if (statPending) statPending.textContent = pending;
        if (statRejected) statRejected.textContent = rejected;

        const dashApplied = document.getElementById('dash-applied');
        const dashInterview = document.getElementById('dash-interview');
        const dashPending = document.getElementById('dash-pending');
        const dashRejected = document.getElementById('dash-rejected');
        const dashProspects = document.getElementById('dash-prospects');
        const dashDocs = document.getElementById('dash-docs');
        const dashConvos = document.getElementById('dash-convos');
        const dashOffers = document.getElementById('dash-offers');
        if (dashApplied) dashApplied.textContent = applied;
        if (dashInterview) dashInterview.textContent = interview;
        if (dashPending) dashPending.textContent = pending;
        if (dashRejected) dashRejected.textContent = rejected;
        if (dashProspects) dashProspects.textContent = prospects.length;
        if (dashDocs) dashDocs.textContent = docs.length;
        if (dashConvos) dashConvos.textContent = convos.length;
        if (dashOffers) dashOffers.textContent = offer;

        const all = [...apps, ...prospects].sort((a, b) =>
            new Date(b.created_at || b.applied_at || 0) - new Date(a.created_at || a.applied_at || 0)
        ).slice(0, 5);

        const tbody = document.getElementById('recent-activity');
        if (tbody) {
            if (all.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">No activity yet. Start by adding prospects!</td></tr>';
            } else {
                tbody.innerHTML = all.map(item => {
                    const status = item.status || 'new';
                    const statusClass = 'status-' + status;
                    const statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
                    return `<tr>
          <td>${item.company || item.name || '—'}</td>
          <td>${item.title || '—'}</td>
          <td><span class="status-dot ${statusClass}"></span>${statusLabel}</td>
          <td>${(item.created_at || item.applied_at || '').split(' ')[0] || '—'}</td>
          <td>${item.method || item.source || '—'}</td>
        </tr>`;
                }).join('');
            }
        }
    } catch (e) {
        console.error('Dashboard load error:', e);
    }
}

async function loadPipeline() {
    try {
        const res = await apiGet('applications');
        const apps = res.data?.items || res.data || [];
        const tbody = document.getElementById('pipeline-body');
        if (!apps.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">No applications yet. Add your first one above.</td></tr>';
            return;
        }
        tbody.innerHTML = apps.map(app => {
            const statusClass = 'status-' + app.status;
            const statusLabel = app.status.charAt(0).toUpperCase() + app.status.slice(1);
            return `<tr onclick="viewPipelineDetail(${app.id})" style="cursor:pointer">
        <td><strong style="color:var(--text)">${app.company}</strong></td>
        <td>${app.title}</td>
        <td>${app.method || '—'}</td>
        <td><span class="status-dot ${statusClass}"></span>${statusLabel}</td>
        <td>${app.date_applied ? app.date_applied.split(' ')[0] : '—'}</td>
        <td>
          <button class="btn btn-ghost btn-sm" onclick="event.stopPropagation();openDetailPanel('applications', ${app.id})">View</button>
          <button class="btn btn-ghost btn-sm" onclick="event.stopPropagation();editPipelineStatus(${app.id},'${app.status}')">Edit</button>
          <button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="event.stopPropagation();deletePipelineEntry(${app.id})">🗑</button>
        </td>
      </tr>`;
        }).join('');
    } catch (e) {
        console.error('Pipeline load error:', e);
    }
}

let currentPipelineId = null;

function viewPipelineDetail(id) {
    currentPipelineId = id;
    apiGet('applications').then(res => {
        const apps = res.data?.items || res.data || [];
        const app = apps.find(a => a.id == id);
        if (!app) return;
        document.getElementById('pd-title').textContent = app.company + ' — ' + app.title;
        document.getElementById('pd-content').innerHTML = `
      <p><strong>Location:</strong> ${app.location || '—'}</p>
      <p><strong>Salary:</strong> ${app.salary || 'Not specified'}</p>
      <p><strong>Method:</strong> ${app.method || '—'}</p>
      <p><strong>Status:</strong> ${app.status}</p>
      <p><strong>Applied:</strong> ${app.date_applied || '—'}</p>
      ${app.url ? '<p><strong>URL:</strong> <a href="' + app.url + '" target="_blank" style="color:var(--accent)">' + app.url + '</a></p>' : ''}
      ${app.notes ? '<p><strong>Notes:</strong></p><div style="background:var(--bg-dark);padding:12px;border-radius:6px;white-space:pre-wrap">' + app.notes + '</div>' : ''}
    `;
        document.getElementById('pipeline-detail-card').style.display = 'block';
    });
}

function closePipelineDetail() {
    document.getElementById('pipeline-detail-card').style.display = 'none';
    currentPipelineId = null;
}

function openJobUrl() {
    if (!currentPipelineId) return;
    apiGet('applications').then(res => {
        const app = (res.data?.items || res.data || []).find(a => a.id == currentPipelineId);
        if (app && app.url) window.open(app.url, '_blank');
    });
}

async function editPipelineStatus(id, currentStatus) {
    const statuses = ['new', 'applied', 'interview', 'rejected', 'ghosted', 'offer'];
    const newStatus = prompt('New status:\n' + statuses.join(', '), currentStatus);
    if (!newStatus || !statuses.includes(newStatus)) return;
    try {
        await apiPut('applications/' + id, { status: newStatus });
        showToast('Status updated');
        loadPipeline();
        loadDashboard();
    } catch (e) { showToast('Error updating status', true); }
}

async function deletePipelineEntry(id) {
    if (!confirm('Delete this application?')) return;
    try {
        await apiDelete('applications/' + id);
        showToast('Deleted');
        loadPipeline();
        loadDashboard();
        closePipelineDetail();
    } catch (e) { showToast('Error deleting', true); }
}

function deleteCurrentPipelineEntry() {
    if (currentPipelineId) deletePipelineEntry(currentPipelineId);
}

function copyPipelineNotes() {
    if (!currentPipelineId) return;
    apiGet('applications').then(res => {
        const app = (res.data?.items || res.data || []).find(a => a.id == currentPipelineId);
        if (app && app.notes) copyToClipboard(app.notes);
    });
}

function addConversationFromPipeline() {
    if (!currentPipelineId) return;
    showSection('conversations', document.querySelector('[onclick*="conversations"]'));
    document.getElementById('conv-subject').value = 'Re: Application at ' + document.getElementById('pd-title').textContent;
}

function exportPipeline() {
    apiGet('applications').then(res => {
        const apps = res.data?.items || res.data || [];
        if (!apps.length) { showToast('No data to export', true); return; }
        const headers = ['ID', 'Company', 'Title', 'Location', 'Salary', 'Method', 'Status', 'URL', 'Notes', 'Date Applied'];
        const rows = apps.map(a => [a.id, a.company, a.title, a.location, a.salary, a.method, a.status, a.url, a.notes, a.date_applied].map(v => '"' + (v || '').toString().replace(/"/g, '""') + '"').join(','));
        const csv = [headers.join(','), ...rows].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'pipeline_' + new Date().toISOString().split('T')[0] + '.csv';
        a.click();
        showToast('Pipeline exported');
    });
}

async function loadProspectCue() {
    try {
        const res = await apiGet('prospects');
        const prospects = res.data?.items || res.data || [];
        const statusFilter = document.getElementById('pc-filter-status')?.value || 'all';
        const sourceFilter = document.getElementById('pc-filter-source')?.value || 'all';
        const sortOrder = document.getElementById('pc-sort')?.value || 'newest';

        let filtered = prospects;
        if (statusFilter !== 'all') {
            filtered = filtered.filter(p => {
                const status = p.status || 'new';
                if (statusFilter === 'new') {
                    return status === 'new';
                }
                if (statusFilter === 'applied') {
                    return status === 'applied';
                }
                return status === statusFilter;
            });
        }
        if (sourceFilter !== 'all') {
            filtered = filtered.filter(p => p.source === sourceFilter);
        }
        if (sortOrder === 'oldest') {
            filtered.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));
        } else if (sortOrder === 'rating-high') {
            filtered.sort((a, b) => (b.rating || 0) - (a.rating || 0));
        } else if (sortOrder === 'rating-low') {
            filtered.sort((a, b) => (a.rating || 0) - (b.rating || 0));
        } else {
            filtered.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
        }

        document.getElementById('pc-count').textContent = filtered.length + ' prospect' + (filtered.length !== 1 ? 's' : '');
        const tbody = document.getElementById('prospect-cue-body');
        if (!filtered.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:32px">No prospects match the selected filters.</td></tr>';
            return;
        }
        tbody.innerHTML = filtered.map(p => {
            const stars = '★'.repeat(p.rating || 3) + '☆'.repeat(5 - (p.rating || 3));
            const statusClass = 'status-' + (p.status || 'new');
            return `<tr>
        <td><input type="checkbox" class="pc-select" data-id="${p.id}"></td>
        <td><strong style="color:var(--text)">${p.company}</strong></td>
        <td>${p.title || '—'}</td>
        <td>${p.source || '—'}</td>
        <td>${stars}</td>
        <td><span class="status-dot ${statusClass}"></span>${p.status || 'new'}</td>
        <td>${p.created_at ? p.created_at.split(' ')[0] : '—'}</td>
        <td>
          <button class="btn btn-ghost btn-sm" onclick="reviewProspect(${p.id})">Review</button>
          <button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteProspect(${p.id})">🗑</button>
        </td>
      </tr>`;
        }).join('');
    } catch (e) { console.error('Prospect load error:', e); }
}

function renderProspectCue() {
    loadProspectCue();
}

let currentProspectId = null;

function toggleSelectAll() {
    const checked = document.getElementById('pc-select-all').checked;
    document.querySelectorAll('.pc-select').forEach(cb => cb.checked = checked);
}

async function batchMoveToPipeline() {
    const ids = Array.from(document.querySelectorAll('.pc-select:checked')).map(cb => parseInt(cb.dataset.id));
    if (!ids.length) { showToast('No prospects selected', true); return; }
    let moved = 0;
    let failed = 0;
    for (const id of ids) {
        try {
            const res = await apiGet('prospects/' + id);
            const p = res.data;
            if (!p) { failed++; continue; }
            const companyId = await findOrCreateCompany(p.company || '');
            const jobId = await findOrCreateJob(p.title || '', companyId);
            await apiPost('applications', {
                company: p.company || '',
                title: p.title || '',
                location: '',
                salary: '',
                method: p.source || 'Direct',
                url: p.url || '',
                status: 'applied',
                notes: p.description || '',
                company_id: companyId,
                job_id: jobId
            });
            await apiPut('prospects/' + id, { status: 'applied' });
            moved++;
        } catch (e) { failed++; }
    }
    showToast(moved + ' moved, ' + failed + ' failed');
    loadProspectCue();
    loadPipeline();
    loadDashboard();
}

async function batchDiscard() {
    const ids = Array.from(document.querySelectorAll('.pc-select:checked')).map(cb => parseInt(cb.dataset.id));
    if (!ids.length) { showToast('No prospects selected', true); return; }
    if (!confirm('Discard ' + ids.length + ' selected prospect(s)?')) return;
    try {
        await apiPost('prospects/batch-discard', { ids });
        showToast(ids.length + ' prospect(s) discarded');
        loadProspectCue();
        loadDashboard();
    } catch (e) { showToast('Error discarding', true); }
}

function reviewProspect(id) {
    currentProspectId = id;
    apiGet('prospects').then(res => {
        const p = (res.data?.items || res.data || []).find(x => x.id == id);
        if (!p) return;
        document.getElementById('pc-review-content').innerHTML = `
      <h3 style="color:var(--accent);margin-bottom:12px">${p.company} — ${p.title}</h3>
      <p><strong>Source:</strong> ${p.source || '—'}</p>
      <p><strong>Rating:</strong> ${'★'.repeat(p.rating || 3)}${'☆'.repeat(5 - (p.rating || 3))}</p>
      ${p.url ? '<p><strong>URL:</strong> <a href="' + p.url + '" target="_blank" style="color:var(--accent)">' + p.url + '</a></p>' : ''}
      ${p.description ? '<div style="background:var(--bg-dark);padding:16px;border-radius:8px;margin-top:12px;white-space:pre-wrap;max-height:300px;overflow-y:auto">' + p.description + '</div>' : '<p style="color:var(--text-muted)">No description saved.</p>'}
    `;
        document.getElementById('pc-review-card').style.display = 'block';
    });
}

function closeReview() {
    document.getElementById('pc-review-card').style.display = 'none';
    currentProspectId = null;
}

async function moveReviewedToPipeline() {
    if (!currentProspectId) return;
    try {
        const res = await apiGet('prospects/' + currentProspectId);
        const p = res.data;
        if (!p) { showToast('Prospect not found', true); return; }
        const companyId = await findOrCreateCompany(p.company || '');
        const jobId = await findOrCreateJob(p.title || '', companyId);
        await apiPost('applications', {
            company: p.company || '',
            title: p.title || '',
            location: '',
            salary: '',
            method: p.source || 'Direct',
            url: p.url || '',
            status: 'applied',
            notes: p.description || '',
            company_id: companyId,
            job_id: jobId
        });
        await apiPut('prospects/' + currentProspectId, { status: 'applied' });
        showToast('Moved to pipeline');
        closeReview();
        loadProspectCue();
        loadPipeline();
        loadDashboard();
    } catch (e) { showToast('Error moving: ' + e.message, true); }
}

async function markReviewed() {
    if (!currentProspectId) return;
    try {
        await apiPost('prospects/' + currentProspectId + '/review', {});
        showToast('Marked as reviewed');
        closeReview();
        loadProspectCue();
    } catch (e) { showToast('Error marking reviewed', true); }
}

async function discardReviewed() {
    if (!currentProspectId) return;
    if (!confirm('Discard this prospect?')) return;
    try {
        await apiPut('prospects/' + currentProspectId, { status: 'discarded' });
        showToast('Discarded');
        closeReview();
        loadProspectCue();
        loadDashboard();
    } catch (e) { showToast('Error discarding', true); }
}

async function deleteProspect(id) {
    if (!confirm('Delete this prospect permanently?')) return;
    try {
        await apiDelete('prospects/' + id);
        showToast('Deleted');
        loadProspectCue();
        loadDashboard();
    } catch (e) { showToast('Error deleting', true); }
}

async function loadDocuments() {
    const type = document.getElementById('doc-filter-type').value;
    try {
        const res = await apiGet('documents');
        let docs = res.data?.items || res.data || [];
        if (type !== 'all') docs = docs.filter(d => d.document_type === type);
        const tbody = document.getElementById('documents-body');
        if (!docs.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">No documents yet. Create your first one above.</td></tr>';
            return;
        }
        tbody.innerHTML = docs.map(d => {
            const typeLabel = d.document_type === 'resume' ? 'Resume' : d.document_type === 'cover_letter' ? 'Cover Letter' : 'Other';
            return `<tr>
        <td><strong style="color:var(--text)">${d.title || d.name}</strong></td>
        <td>${typeLabel}</td>
        <td>${d.company || '—'}</td>
        <td>${d.job_title || '—'}</td>
        <td>${d.updated_at ? d.updated_at.split(' ')[0] : '—'}</td>
        <td>
          <button class="btn btn-ghost btn-sm" onclick="editDocument(${d.id})">Edit</button>
          <button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteDocument(${d.id})">🗑</button>
        </td>
      </tr>`;
        }).join('');
    } catch (e) { console.error('Documents load error:', e); }
}

let currentDocId = null;

async function editDocument(id) {
    currentDocId = id;
    try {
        const res = await apiGet('documents/' + id);
        const doc = res.data;
        if (!doc) { showToast('Document not found', true); return; }
        document.getElementById('doc-edit-id').value = id;
        document.getElementById('doc-edit-name').value = doc.title || doc.name;
        document.getElementById('doc-edit-type').value = doc.document_type || 'other';
        document.getElementById('doc-edit-content').value = doc.content || '';
        document.getElementById('doc-editor-title').textContent = 'Edit: ' + (doc.title || doc.name);
        document.getElementById('doc-editor-card').style.display = 'block';
    } catch (e) { showToast('Error loading document', true); }
}

function closeDocEditor() {
    document.getElementById('doc-editor-card').style.display = 'none';
    currentDocId = null;
}

async function updateDocument() {
    if (!currentDocId) return;
    const content = document.getElementById('doc-edit-content').value;
    try {
        await apiPut('documents/' + currentDocId, { content });
        showToast('Document updated');
        closeDocEditor();
        loadDocuments();
    } catch (e) { showToast('Error updating', true); }
}

async function deleteDocument(id) {
    if (!confirm('Delete this document?')) return;
    try {
        await apiDelete('documents/' + id);
        showToast('Deleted');
        loadDocuments();
        loadDashboard();
        if (currentDocId == id) closeDocEditor();
    } catch (e) { showToast('Error deleting', true); }
}

function deleteCurrentDocument() {
    if (currentDocId) deleteDocument(currentDocId);
}

function copyDocContent() {
    const content = document.getElementById('doc-edit-content').value;
    if (content) copyToClipboard(content);
}

async function loadConversations() {
    const channel = document.getElementById('conv-filter-channel').value;
    const direction = document.getElementById('conv-filter-direction').value;
    const followup = document.getElementById('conv-filter-followup').value;
    try {
        const res = await apiGet('conversations');
        let filtered = res.data?.items || res.data || [];
        if (channel !== 'all') filtered = filtered.filter(c => c.channel === channel);
        if (direction !== 'all') filtered = filtered.filter(c => c.direction === direction);
        if (followup === 'followup') filtered = filtered.filter(c => c.follow_up_needed == 1);

        document.getElementById('conv-count').textContent = filtered.length + ' conversation' + (filtered.length !== 1 ? 's' : '');
        const container = document.getElementById('conversations-body');
        if (!filtered.length) {
            container.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:32px">No conversations yet. Log your first one above.</p>';
            return;
        }
        container.innerHTML = filtered.map(c => `
      <div class="conversation-item ${c.direction === 'inbound' ? 'inbound' : ''}">
        <div class="conv-header">
          <span class="conv-from">${c.contact_name || 'Unknown'} ${c.contact_role ? '(' + c.contact_role + ')' : ''}</span>
          <span class="conv-date">${c.date_sent || c.created_at || '—'}</span>
        </div>
        <div class="conv-subject">${c.subject || 'No subject'}</div>
        <div class="conv-body">${c.content || ''}</div>
        <div style="margin-top:8px">
          <span class="conv-channel">${c.channel}</span>
          <span class="conv-channel">${c.direction}</span>
          ${c.follow_up_needed ? '<span class="conv-channel" style="background:rgba(255,152,0,0.2);color:var(--warning)">Follow-up needed</span>' : ''}
          ${c.follow_up_date ? '<span class="conv-channel">Follow-up: ' + c.follow_up_date + '</span>' : ''}
        </div>
        ${c.notes ? '<div style="margin-top:8px;color:var(--text-muted);font-size:0.8rem"><strong>Notes:</strong> ' + c.notes + '</div>' : ''}
        <div style="margin-top:12px">
          <button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteConversation(${c.id})">🗑 Delete</button>
        </div>
      </div>
    `).join('');
    } catch (e) { console.error('Conversations load error:', e); }
}

async function deleteConversation(id) {
    if (!confirm('Delete this conversation?')) return;
    try {
        await apiDelete('conversations/' + id);
        showToast('Deleted');
        loadConversations();
        loadDashboard();
    } catch (e) { showToast('Error deleting', true); }
}

async function loadTailoredResumes() {
    try {
        const res = await apiGet('resumes');
        const resumes = res.data?.items || res.data || [];
        const tbody = document.getElementById('tailored-resumes-body');
        if (!resumes.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px">No tailored resumes yet.</td></tr>';
            return;
        }
        tbody.innerHTML = resumes.map(r => { const metadata = readMetadata(r); return `<tr>
        <td>${r.title || r.name}</td>
        <td>${metadata.target_role || '—'}</td>
        <td>${metadata.company || '—'}</td>
        <td>${r.created_at ? r.created_at.split(' ')[0] : '—'}</td>
        <td><button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteTailoredResume(${r.id})">🗑</button></td>
      </tr>`; }).join('');
        tbody.querySelectorAll('tr').forEach(row => {
            const deleteButton = row.querySelector('[onclick^="deleteTailoredResume"]');
            if (!deleteButton) return;
            const viewButton = document.createElement('button');
            viewButton.className = 'btn btn-ghost btn-sm';
            viewButton.textContent = 'View';
            viewButton.onclick = () => viewTailoredResume(deleteButton.getAttribute('onclick').match(/\d+/)[0]);
            deleteButton.before(viewButton, document.createTextNode(' '));
        });
    } catch (e) { console.error('Tailored resumes load error:', e); }
}

async function deleteTailoredResume(id) {
    if (!confirm('Delete?')) return;
    try {
        await apiDelete('resumes/' + id);
        showToast('Deleted');
        loadTailoredResumes();
    } catch (e) { showToast('Error deleting', true); }
}

async function viewTailoredResume(id) {
    try {
        const res = await apiGet('resumes/' + id);
        const resume = res.data;
        if (!resume || !resume.content) {
            showToast('Resume content not found', true);
            return;
        }
        document.getElementById('resume-output-title').textContent = resume.title || 'Tailored Resume';
        document.getElementById('resume-generated-text').value = resume.content;
        document.getElementById('resume-output').style.display = 'block';
        window.scrollTo(0, document.getElementById('resume-output').offsetTop - 100);
    } catch (e) { showToast('Unable to load resume', true); }
}

function copyTailoredResume() {
    const content = document.getElementById('resume-generated-text').value;
    if (content) copyToClipboard(content);
}

async function loadCoverLetters() {
    try {
        const res = await apiGet('cover-letters');
        const letters = res.data?.items || res.data || [];
        const tbody = document.getElementById('cover-letters-body');
        if (!letters.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px">No cover letters yet.</td></tr>';
            return;
        }
        tbody.innerHTML = letters.map(l => { const metadata = readMetadata(l); const company = metadata.company || (l.title || '').split(' - ')[0] || '—'; return `<tr>
        <td>${company}</td>
        <td>${l.title}</td>
        <td>${metadata.tone || '—'}</td>
        <td>${l.created_at ? l.created_at.split(' ')[0] : '—'}</td>
        <td><button class="btn btn-ghost btn-sm" onclick="viewCoverLetter(${l.id})">View</button></td>
      </tr>`; }).join('');
    } catch (e) { console.error('Cover letters load error:', e); }
}

async function viewCoverLetter(id) {
    try {
        const res = await apiGet('cover-letters/' + id);
        const l = res.data;
        if (l && l.content) {
            document.getElementById('cl-generated-text').value = l.content;
            document.getElementById('cl-output').style.display = 'block';
            window.scrollTo(0, document.getElementById('cl-output').offsetTop - 100);
        } else {
            showToast('Cover letter content not found', true);
        }
    } catch (e) { showToast('Unable to load cover letter', true); }
}

async function loadSavedSearches() {
    try {
        const res = await apiGet('saved-searches');
        const searches = res.data?.items || res.data || [];
        const tbody = document.getElementById('saved-searches-body');
        if (!searches.length) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:24px">No saved searches yet.</td></tr>';
            return;
        }
        tbody.innerHTML = searches.map(s => `<tr>
        <td><strong style="color:var(--text)">${s.name}</strong></td>
        <td><a href="${s.url}" target="_blank" style="color:var(--accent);font-size:0.8rem">${s.url.substring(0, 50)}...</a></td>
        <td>${s.created_at ? s.created_at.split(' ')[0] : '—'}</td>
        <td><button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteSavedSearch(${s.id})">🗑</button></td>
      </tr>`).join('');
    } catch (e) { console.error('Saved searches load error:', e); }
}

async function deleteSavedSearch(id) {
    if (!confirm('Delete?')) return;
    try {
        await apiDelete('saved-searches/' + id);
        showToast('Deleted');
        loadSavedSearches();
    } catch (e) { showToast('Error deleting', true); }
}

async function loadTargetCompanies() {
    try {
        const res = await apiGet('target-companies');
        const companies = res.data?.items || res.data || [];
        const tbody = document.getElementById('target-companies-body');
        if (!companies.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">No target companies yet.</td></tr>';
            return;
        }
        tbody.innerHTML = companies.map(c => `<tr>
        <td><strong style="color:var(--text)">${c.company}</strong></td>
        <td>${c.target_role || '—'}</td>
        <td>${c.hiring_manager || '—'}</td>
        <td>${c.created_at ? c.created_at.split(' ')[0] : '—'}</td>
        <td><button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteTargetCompany(${c.id})">🗑</button></td>
      </tr>`).join('');
    } catch (e) { console.error('Target companies load error:', e); }
}

async function deleteTargetCompany(id) {
    if (!confirm('Delete?')) return;
    try {
        await apiDelete('target-companies/' + id);
        showToast('Deleted');
        loadTargetCompanies();
    } catch (e) { showToast('Error deleting', true); }
}

async function loadSettings() {
    try {
        const res = await apiGet('settings');
        const s = res.data?.items?.[0] || res.data || {};
        if (s.name) document.getElementById('set-name').value = s.name;
        if (s.email) document.getElementById('set-email').value = s.email;
        if (s.phone) document.getElementById('set-phone').value = s.phone;
        if (s.linkedin) document.getElementById('set-linkedin').value = s.linkedin;
        if (s.portfolio) document.getElementById('set-portfolio').value = s.portfolio;
        if (s.salary) document.getElementById('set-salary').value = s.salary;
        if (s.arrangement) document.getElementById('set-arrangement').value = s.arrangement;
        if (s.master_resume) document.getElementById('master-resume').value = s.master_resume;
    } catch (e) {
        showToast('Failed to load settings: ' + (e.message || 'Server error'), true);
    }
}

async function exportAllData() {
    try {
        const result = await apiGet('export');
        const filePath = result.data?.export?.file_path;
        if (filePath) {
            window.open(filePath, '_blank');
            showToast('Export ready. Opening download file...');
        } else {
            showToast('Export completed', false);
        }
    } catch (e) {
        showToast('Export failed', true);
    }
}
async function findOrCreateCompany(name) {
    if (!name || !name.trim()) return null;
    try {
        const res = await apiGet('companies');
        const items = res.data?.items || res.data || [];
        const existing = items.find(c => c.name.trim().toLowerCase() === name.trim().toLowerCase());
        if (existing) return existing.id;
        const created = await apiPost('companies', { name: name.trim(), status: 'active' });
        return created.data?.id || null;
    } catch (e) {
        console.error('findOrCreateCompany error:', e);
        return null;
    }
}

async function findOrCreateJob(title, companyId) {
    if (!title || !title.trim()) return null;
    try {
        const res = await apiGet('jobs');
        const items = res.data?.items || res.data || [];
        const existing = items.find(j =>
            j.title.trim().toLowerCase() === title.trim().toLowerCase() &&
            (companyId ? j.company_id == companyId : true)
        );
        if (existing) return existing.id;
        const created = await apiPost('jobs', {
            title: title.trim(),
            company_id: companyId || null,
            status: 'open'
        });
        return created.data?.id || null;
    } catch (e) {
        console.error('findOrCreateJob error:', e);
        return null;
    }
}

async function addApplication() {
    const data = {
        company: document.getElementById('p-company').value,
        title: document.getElementById('p-title').value,
        location: document.getElementById('p-location').value,
        salary: document.getElementById('p-salary').value,
        method: document.getElementById('p-method').value,
        status: document.getElementById('p-status').value,
        url: document.getElementById('p-url').value,
        notes: document.getElementById('p-notes').value
    };
    if (!data.company || !data.title) { showToast('Company and Title required', true); return; }
    try {
        const companyId = await findOrCreateCompany(data.company);
        const jobId = await findOrCreateJob(data.title, companyId);
        data.company_id = companyId;
        data.job_id = jobId;
        await apiPost('applications', data);
        showToast('Application added');
        clearPipelineForm();
        loadPipeline();
        loadDashboard();
    } catch(e) { showToast('Error: ' + e.message, true); }
}

function clearPipelineForm() {
    ['p-company','p-title','p-salary','p-url','p-notes'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('p-location').selectedIndex = 0;
    document.getElementById('p-method').selectedIndex = 0;
    document.getElementById('p-status').selectedIndex = 0;
}

async function addProspect() {
    const data = {
        company: document.getElementById('pc-company').value,
        title: document.getElementById('pc-title').value,
        url: document.getElementById('pc-url').value,
        description: document.getElementById('pc-desc').value,
        source: document.getElementById('pc-source').value,
        rating: parseInt(document.getElementById('pc-rating').value)
    };
    if (!data.company || !data.title) { showToast('Company and Title required', true); return; }
    try {
        await apiPost('prospects', data);
        showToast('Prospect added');
        clearProspectForm();
        loadProspectCue();
        loadDashboard();
    } catch(e) { showToast('Error: ' + e.message, true); }
}

function clearProspectForm() {
    ['pc-company','pc-title','pc-url','pc-desc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('pc-source').selectedIndex = 0;
    document.getElementById('pc-rating').selectedIndex = 2;
}

async function pasteFromClipboard() {
    try {
        const text = await navigator.clipboard.readText();
        document.getElementById('pc-desc').value = text;
        showToast('Pasted from clipboard');
    } catch(e) { showToast('Could not read clipboard', true); }
}

async function saveDocument() {
    const data = {
        name: document.getElementById('doc-name').value,
        document_type: document.getElementById('doc-type').value,
        company: document.getElementById('doc-company').value,
        job_title: document.getElementById('doc-job-title').value,
        content: document.getElementById('doc-content').value
    };
    if (!data.name) { showToast('Document name required', true); return; }
    try {
        await apiPost('documents', data);
        showToast('Document saved');
        clearDocForm();
        loadDocuments();
        loadDashboard();
    } catch(e) { showToast('Error: ' + e.message, true); }
}

function clearDocForm() {
    ['doc-name','doc-company','doc-job-title','doc-content'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('doc-type').selectedIndex = 0;
}

async function pasteDocFromClipboard() {
    try {
        const text = await navigator.clipboard.readText();
        document.getElementById('doc-content').value = text;
        showToast('Pasted from clipboard');
    } catch(e) { showToast('Could not read clipboard', true); }
}

async function saveConversation() {
    const data = {
        contact_name: document.getElementById('conv-contact').value,
        contact_role: document.getElementById('conv-role').value,
        channel: document.getElementById('conv-channel').value,
        direction: document.getElementById('conv-direction').value,
        subject: document.getElementById('conv-subject').value,
        content: document.getElementById('conv-content').value,
        date_sent: document.getElementById('conv-date').value,
        follow_up_needed: document.getElementById('conv-followup').checked ? 1 : 0,
        follow_up_date: document.getElementById('conv-followup-date').value,
        notes: document.getElementById('conv-notes').value
    };
    if (!data.contact_name && !data.subject) { showToast('Contact or Subject required', true); return; }
    try {
        await apiPost('conversations', data);
        showToast('Conversation logged');
        clearConvForm();
        loadConversations();
        loadDashboard();
    } catch(e) { showToast('Error: ' + e.message, true); }
}

function clearConvForm() {
    ['conv-contact','conv-role','conv-subject','conv-content','conv-notes'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('conv-channel').selectedIndex = 0;
    document.getElementById('conv-direction').selectedIndex = 0;
    document.getElementById('conv-date').value = new Date().toISOString().split('T')[0];
    document.getElementById('conv-followup').checked = false;
    document.getElementById('conv-followup-date').value = '';
}

async function pasteConvFromClipboard() {
    try {
        const text = await navigator.clipboard.readText();
        document.getElementById('conv-content').value = text;
        showToast('Pasted from clipboard');
    } catch(e) { showToast('Could not read clipboard', true); }
}

function saveSettings() {
    const settings = {
        name: document.getElementById('set-name').value,
        email: document.getElementById('set-email').value,
        phone: document.getElementById('set-phone').value,
        linkedin: document.getElementById('set-linkedin').value,
        portfolio: document.getElementById('set-portfolio').value,
        salary: document.getElementById('set-salary').value,
        arrangement: document.getElementById('set-arrangement').value
    };
    apiPost('settings', settings).then(() => {
        showToast('Profile saved');
    }).catch((e) => {
        showToast('Failed to save profile: ' + (e.message || 'Server error'), true);
    });
}

async function addTargetCompany() {
    const company = document.getElementById('tc-company').value;
    const role = document.getElementById('tc-role').value;
    const manager = document.getElementById('tc-hiring-manager').value;
    const notes = document.getElementById('tc-notes').value;
    if (!company) { showToast('Company name required', true); return; }
    try {
        await apiPost('target-companies', { company, target_role: role, hiring_manager: manager, notes });
        showToast('Target company added');
        document.getElementById('tc-company').value = '';
        document.getElementById('tc-role').value = '';
        document.getElementById('tc-hiring-manager').value = '';
        document.getElementById('tc-notes').value = '';
        loadTargetCompanies();
    } catch(e) { showToast('Error saving', true); }
}

async function addSavedSearch() {
    const name = document.getElementById('ss-name').value;
    const url = document.getElementById('ss-url').value;
    if (!name || !url) { showToast('Name and URL required', true); return; }
    try {
        await apiPost('saved-searches', { name, url });
        showToast('Saved search added');
        document.getElementById('ss-name').value = '';
        document.getElementById('ss-url').value = '';
        loadSavedSearches();
    } catch(e) { showToast('Error saving', true); }
}

async function saveMasterResume() {
    const content = document.getElementById('master-resume').value;
    try {
        await apiPost('settings', { master_resume: content });
        showToast('Master resume saved');
    } catch (e) {
        showToast('Failed to save master resume: ' + (e.message || 'Server error'), true);
    }
}

function copyMasterResume() {
    const content = document.getElementById('master-resume').value;
    if (content) copyToClipboard(content);
}

function pasteMasterResume() {
    navigator.clipboard.readText().then(text => {
        document.getElementById('master-resume').value = text;
        showToast('Pasted from clipboard');
    }).catch(() => showToast('Could not read clipboard', true));
}

async function generateTailoredResume() {
    const masterResume = document.getElementById('master-resume').value;
    const jobDescription = document.getElementById('resume-jd').value;
    const targetRole = document.getElementById('resume-target-role').value;
    const company = document.getElementById('resume-company').value;
    
    if (!masterResume || !jobDescription) {
        showToast('Master resume and job description are required', true);
        return;
    }
    
    try {
            showToast('Generating tailored resume...');
        const result = await apiPost('resumes/generate', {
            master_resume: masterResume,
            job_description: jobDescription,
            target_role: targetRole,
            company: company
        });
        showToast('Tailored resume generated and saved');
        loadTailoredResumes();
    } catch (e) {
        showToast('Error generating resume: ' + (e.message || 'Unknown error'), true);
    }
}

async function generateCoverLetter() {
    const company = document.getElementById('cl-company').value;
    const title = document.getElementById('cl-title').value;
    const hiringManager = document.getElementById('cl-hiring-manager').value;
    const tone = document.getElementById('cl-tone').value;
    const jd = document.getElementById('cl-jd').value;
    
    if (!company || !title || !jd) {
        showToast('Fill in company, title, and JD', true);
        return;
    }
    
    try {
        showToast('Generating cover letter...');
        const result = await apiPost('cover-letters/generate', {
            job_title: title,
            company_name: company,
            background: jd, // Using JD as background for now - could add dedicated background field later
            hiring_manager: hiringManager,
            tone: tone
        });
        document.getElementById('cl-generated-text').value = result.data?.content || result.data?.generated_text || '';
        document.getElementById('cl-output').style.display = 'block';
        showToast('Cover letter generated and saved');
        loadCoverLetters();
    } catch (e) {
        showToast('Error generating cover letter: ' + (e.message || 'Unknown error'), true);
    }
}

function copyCoverLetter() {
    const content = document.getElementById('cl-generated-text').value;
    if (content) copyToClipboard(content);
}

async function saveCoverLetter() {
    const data = {
        company: document.getElementById('cl-company').value,
        title: document.getElementById('cl-title').value,
        hiring_manager: document.getElementById('cl-hiring-manager').value,
        tone: document.getElementById('cl-tone').value,
        jd_text: document.getElementById('cl-jd').value,
        generated_text: document.getElementById('cl-generated-text').value
    };
    try {
        await apiPost('cover-letters', data);
        showToast('Cover letter saved');
        loadCoverLetters();
    } catch(e) { showToast('Error saving', true); }
}

function extractKeywords() {
    const jd = document.getElementById('ats-jd').value;
    if (!jd) { showToast('Paste a job description first', true); return; }
    const keywords = jd.match(/\b[A-Z][a-z]+\b/g)?.slice(0, 20) || [];
    document.getElementById('ats-keywords').innerHTML = '<strong>Extracted Keywords:</strong><br>' + keywords.join(', ');
}

function simulateATSScore() {
    const resume = document.getElementById('ats-resume').value;
    if (!resume) { showToast('Paste your resume first', true); return; }
    const score = Math.min(100, Math.floor(resume.length / 50));
    document.getElementById('ats-score').innerHTML = '<strong>Estimated ATS Score:</strong> ' + score + '/100 (simulated)';
}

function copyPrompt(type) {
    const prompts = {
        ats: 'Extract the top 10 keywords, required skills, and qualifications from this job description. Format as a bulleted list.',
        resume: 'Rewrite my resume to include these keywords while maintaining professional tone: [paste keywords]',
        cover: 'Write a 3-paragraph cover letter for [position] at [company] using this job description: [paste JD]',
        interview: 'Generate 5 interview questions for [position] role and provide ideal answers based on this JD: [paste JD]'
    };
    copyToClipboard(prompts[type] || '');
}

async function saveAPIKey(service) {
    const key = document.getElementById('api-' + service).value;
    if (!key) { showToast('Enter an API key', true); return; }
    try {
        // Get current settings to preserve existing api_keys
        const current = await apiGet('settings');
        const profile = current.data?.items?.[0] || current.data || {};
        const existingKeys = profile.api_keys ? JSON.parse(profile.api_keys) : {};
        existingKeys[service] = key;
        await apiPost('settings', { api_keys: JSON.stringify(existingKeys) });
        showToast('API key saved');
    } catch (e) {
        showToast('Failed to save API key: ' + (e.message || 'Server error'), true);
    }
}

function clearAllData() {
    if (confirm('Are you sure you want to clear all data? This cannot be undone.')) {
        showToast('Clear all data - implement server endpoint');
    }
}
// ==================== COMPANIES ====================
async function loadCompanies() {
    try {
        const res = await apiGet('companies');
        const items = res.data?.items || res.data || [];
        const tbody = document.getElementById('companies-body');
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">No companies yet.</td></tr>';
            return;
        }
        tbody.innerHTML = items.map(c => `<tr>
        <td><strong style="color:var(--text)">${c.name}</strong></td>
        <td>${c.industry || '—'}</td>
        <td>${c.location || '—'}</td>
        <td>${c.status || '—'}</td>
        <td><button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteCompany(${c.id})">🗑</button></td>
      </tr>`).join('');
    } catch (e) { console.error('Companies load error:', e); }
}

async function addCompany() {
    const data = {
        name: document.getElementById('co-name').value,
        industry: document.getElementById('co-industry').value,
        location: document.getElementById('co-location').value,
        size: document.getElementById('co-size').value,
        website: document.getElementById('co-website').value,
        status: document.getElementById('co-status').value,
        description: document.getElementById('co-description').value
    };
    if (!data.name) { showToast('Company name required', true); return; }
    try {
        await apiPost('companies', data);
        showToast('Company added');
        ['co-name','co-industry','co-location','co-size','co-website','co-description'].forEach(id => document.getElementById(id).value = '');
        loadCompanies();
    } catch (e) { showToast('Error: ' + e.message, true); }
}

async function deleteCompany(id) {
    if (!confirm('Delete this company?')) return;
    try {
        await apiDelete('companies/' + id);
        showToast('Deleted');
        loadCompanies();
    } catch (e) { showToast('Error deleting', true); }
}

async function populateCompanyDropdowns() {
    try {
        const res = await apiGet('companies');
        const items = res.data?.items || res.data || [];
        const opts = items.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        ['job-company', 'rec-company'].forEach(selId => {
            const sel = document.getElementById(selId);
            if (sel) sel.innerHTML = '<option value="">-- Select Company --</option>' + opts;
        });
    } catch (e) { console.error('Company dropdown error:', e); }
}

// ==================== JOBS ====================
async function loadJobs() {
    await populateCompanyDropdowns();
    try {
        const res = await apiGet('jobs');
        const items = res.data?.items || res.data || [];
        const companiesRes = await apiGet('companies');
        const companies = companiesRes.data?.items || companiesRes.data || [];
        const tbody = document.getElementById('jobs-body');
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">No jobs yet.</td></tr>';
            return;
        }
        tbody.innerHTML = items.map(j => {
            const co = companies.find(c => c.id == j.company_id);
            return `<tr>
        <td><strong style="color:var(--text)">${j.title}</strong></td>
        <td>${co ? co.name : '—'}</td>
        <td>${j.location || '—'}</td>
        <td>${j.status || '—'}</td>
        <td><button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteJob(${j.id})">🗑</button></td>
      </tr>`;
        }).join('');
    } catch (e) { console.error('Jobs load error:', e); }
}

async function addJob() {
    const data = {
        title: document.getElementById('job-title').value,
        company_id: document.getElementById('job-company').value || null,
        location: document.getElementById('job-location').value,
        employment_type: document.getElementById('job-employment-type').value,
        salary_range: document.getElementById('job-salary-range').value,
        source_url: document.getElementById('job-source-url').value,
        status: document.getElementById('job-status').value,
        description: document.getElementById('job-description').value
    };
    if (!data.title) { showToast('Job title required', true); return; }
    try {
        await apiPost('jobs', data);
        showToast('Job added');
        ['job-title','job-location','job-salary-range','job-source-url','job-description'].forEach(id => document.getElementById(id).value = '');
        loadJobs();
    } catch (e) { showToast('Error: ' + e.message, true); }
}

async function deleteJob(id) {
    if (!confirm('Delete this job?')) return;
    try {
        await apiDelete('jobs/' + id);
        showToast('Deleted');
        loadJobs();
    } catch (e) { showToast('Error deleting', true); }
}

// ==================== RECRUITERS ====================
async function loadRecruiters() {
    await populateCompanyDropdowns();
    try {
        const res = await apiGet('recruiters');
        const items = res.data?.items || res.data || [];
        const companiesRes = await apiGet('companies');
        const companies = companiesRes.data?.items || companiesRes.data || [];
        const tbody = document.getElementById('recruiters-body');
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">No recruiters yet.</td></tr>';
            return;
        }
        tbody.innerHTML = items.map(r => {
            const co = companies.find(c => c.id == r.company_id);
            return `<tr>
        <td><strong style="color:var(--text)">${r.name}</strong></td>
        <td>${co ? co.name : '—'}</td>
        <td>${r.role || '—'}</td>
        <td>${r.email || '—'}</td>
        <td><button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteRecruiter(${r.id})">🗑</button></td>
      </tr>`;
        }).join('');
        await populateRecruiterDropdown();
    } catch (e) { console.error('Recruiters load error:', e); }
}

async function addRecruiter() {
    const data = {
        name: document.getElementById('rec-name').value,
        company_id: document.getElementById('rec-company').value || null,
        role: document.getElementById('rec-role').value,
        email: document.getElementById('rec-email').value,
        phone: document.getElementById('rec-phone').value,
        linkedin: document.getElementById('rec-linkedin').value,
        notes: document.getElementById('rec-notes').value
    };
    if (!data.name) { showToast('Recruiter name required', true); return; }
    try {
        await apiPost('recruiters', data);
        showToast('Recruiter added');
        ['rec-name','rec-role','rec-email','rec-phone','rec-linkedin','rec-notes'].forEach(id => document.getElementById(id).value = '');
        loadRecruiters();
    } catch (e) { showToast('Error: ' + e.message, true); }
}

async function deleteRecruiter(id) {
    if (!confirm('Delete this recruiter?')) return;
    try {
        await apiDelete('recruiters/' + id);
        showToast('Deleted');
        loadRecruiters();
    } catch (e) { showToast('Error deleting', true); }
}

async function populateRecruiterDropdown() {
    try {
        const res = await apiGet('recruiters');
        const items = res.data?.items || res.data || [];
        const sel = document.getElementById('int-recruiter');
        if (sel) sel.innerHTML = '<option value="">-- None --</option>' + items.map(r => `<option value="${r.id}">${r.name}</option>`).join('');
    } catch (e) { console.error('Recruiter dropdown error:', e); }
}

// ==================== INTERVIEWS ====================
async function populateApplicationDropdown() {
    try {
        const res = await apiGet('applications');
        const items = res.data?.items || res.data || [];
        const sel = document.getElementById('int-application');
        if (sel) sel.innerHTML = '<option value="">-- Select Application --</option>' + items.map(a => `<option value="${a.id}">${a.company} — ${a.title}</option>`).join('');
    } catch (e) { console.error('Application dropdown error:', e); }
}

async function loadInterviews() {
    await populateApplicationDropdown();
    await populateRecruiterDropdown();
    try {
        const res = await apiGet('interviews');
        const items = res.data?.items || res.data || [];
        const appsRes = await apiGet('applications');
        const apps = appsRes.data?.items || appsRes.data || [];
        const tbody = document.getElementById('interviews-body');
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px">No interviews scheduled yet.</td></tr>';
            return;
        }
        tbody.innerHTML = items.map(i => {
            const app = apps.find(a => a.id == i.application_id);
            return `<tr>
        <td>${i.interview_type || '—'}</td>
        <td>${app ? app.company + ' — ' + app.title : '—'}</td>
        <td>${i.scheduled_at ? i.scheduled_at.replace('T', ' ').slice(0, 16) : '—'}</td>
        <td>${i.outcome || '—'}</td>
        <td><button class="btn btn-ghost btn-sm" style="border-color:var(--danger);color:var(--danger)" onclick="deleteInterview(${i.id})">🗑</button></td>
      </tr>`;
        }).join('');
    } catch (e) { console.error('Interviews load error:', e); }
}

async function addInterview() {
    const data = {
        application_id: document.getElementById('int-application').value,
        recruiter_id: document.getElementById('int-recruiter').value || null,
        interview_type: document.getElementById('int-type').value,
        scheduled_at: document.getElementById('int-scheduled-at').value ? document.getElementById('int-scheduled-at').value.replace('T', ' ') + ':00' : null,
        notes: document.getElementById('int-notes').value
    };
    if (!data.application_id) { showToast('Application is required', true); return; }
    try {
        await apiPost('interviews', data);
        showToast('Interview scheduled');
        document.getElementById('int-notes').value = '';
        document.getElementById('int-scheduled-at').value = '';
        loadInterviews();
    } catch (e) { showToast('Error: ' + e.message, true); }
}

async function deleteInterview(id) {
    if (!confirm('Delete this interview?')) return;
    try {
        await apiDelete('interviews/' + id);
        showToast('Deleted');
        loadInterviews();
    } catch (e) { showToast('Error deleting', true); }
}
</script>
</body>
</html>
