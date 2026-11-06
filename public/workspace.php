<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Work This Job | JobHunt</title>
<style>
:root {
  --bg-dark: #0f0f13; --bg-card: #1a1a24; --bg-card-hover: #222230;
  --accent: #c9a84c; --accent-dim: #8a7340; --accent-glow: rgba(201,168,76,0.15);
  --text: #e8e6e1; --text-dim: #8a8880; --text-muted: #5a5850;
  --success: #4caf50; --warning: #ff9800; --danger: #f44336; --info: #2196f3;
  --border: #2a2a35;
}
* { margin:0; padding:0; box-sizing:border-box; }
body { background:var(--bg-dark); color:var(--text); font-family:-apple-system,Inter,sans-serif; line-height:1.6; padding:24px; max-width:900px; margin:0 auto; }
a { color:var(--accent); text-decoration:none; }
.back-link { display:inline-block; margin-bottom:16px; font-size:0.85rem; }
.header { margin-bottom:20px; }
.header h1 { font-size:1.6rem; }
.header .sub { color:var(--text-dim); font-size:0.9rem; margin-top:4px; }
.card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:24px; margin-bottom:20px; }
.card h2 { font-size:1rem; color:var(--accent); margin-bottom:16px; text-transform:uppercase; letter-spacing:1px; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:6px; text-transform:uppercase; letter-spacing:1px; }
.form-group input, .form-group textarea { width:100%; background:var(--bg-dark); border:1px solid var(--border); border-radius:8px; padding:10px 12px; color:var(--text); font-size:0.9rem; font-family:inherit; }
.form-group textarea { min-height:140px; resize:vertical; }
.btn { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:8px; border:none; font-size:0.8rem; font-weight:600; cursor:pointer; text-transform:uppercase; letter-spacing:1px; margin-right:8px; margin-top:8px; }
.btn-primary { background:var(--accent); color:var(--bg-dark); }
.btn-success { background:var(--success); color:#fff; }
.btn-secondary { background:var(--bg-card-hover); color:var(--text); border:1px solid var(--border); }
.output-box { background:var(--bg-dark); border:1px solid var(--border); border-radius:8px; padding:16px; margin-top:12px; white-space:pre-wrap; font-size:0.85rem; max-height:400px; overflow-y:auto; display:none; }
.stepper { display:flex; align-items:center; margin:8px 0 20px; }
.stepper-step { display:flex; flex-direction:column; align-items:center; flex:1; position:relative; }
.stepper-step .dot { width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; background:var(--bg-dark); border:2px solid var(--border); color:var(--text-muted); z-index:1; }
.stepper-step.done .dot { background:var(--accent); border-color:var(--accent); color:var(--bg-dark); }
.stepper-step.current .dot { background:var(--bg-dark); border-color:var(--accent); color:var(--accent); box-shadow:0 0 0 3px var(--accent-glow); }
.stepper-step .label { font-size:0.6rem; color:var(--text-muted); margin-top:4px; text-transform:uppercase; }
.stepper-step.done .label, .stepper-step.current .label { color:var(--text); }
.stepper-line { position:absolute; top:12px; left:50%; width:100%; height:2px; background:var(--border); z-index:0; }
.stepper-step.done .stepper-line { background:var(--accent); }
.stepper-step:last-child .stepper-line { display:none; }
.toast { position:fixed; bottom:24px; right:24px; background:var(--bg-card); border:1px solid var(--accent); color:var(--accent); padding:12px 20px; border-radius:8px; font-size:0.85rem; z-index:1000; opacity:0; transition:all 0.3s; }
.toast.show { opacity:1; }
.toast.error { border-color:var(--danger); color:var(--danger); }
</style>
</head>
<body>
<a href="index.php" class="back-link">&larr; Back to Pipeline</a>
<div class="header">
  <h1 id="job-title">Loading...</h1>
  <div class="sub" id="job-sub"></div>
</div>
<div id="stepper"></div>

<div class="card">
  <h2>Job Details</h2>
  <p id="job-details" style="color:var(--text-dim);font-size:0.9rem"></p>
</div>

<div class="card">
  <h2>Resume</h2>
  <div class="form-group"><label>Target Role</label><input type="text" id="r-role"></div>
  <div class="form-group"><label>Company</label><input type="text" id="r-company"></div>
  <div class="form-group"><label>Job Description</label><textarea id="r-jd"></textarea></div>
  <button class="btn btn-primary" onclick="genResume()">Generate Resume</button>
  <div class="output-box" id="r-output"></div>
</div>

<div class="card">
  <h2>Cover Letter</h2>
  <div class="form-group"><label>Hiring Manager (optional)</label><input type="text" id="c-manager"></div>
  <div class="form-group"><label>Tone</label>
    <select id="c-tone" style="width:100%;background:var(--bg-dark);border:1px solid var(--border);border-radius:8px;padding:10px 12px;color:var(--text)">
      <option>Professional</option><option>Casual</option><option>Enthusiastic</option>
    </select>
  </div>
  <button class="btn btn-primary" onclick="genCoverLetter()">Generate Cover Letter</button>
  <div class="output-box" id="c-output"></div>
</div>

<div class="card">
  <h2>Status</h2>
  <button class="btn btn-success" id="mark-applied-btn" onclick="markApplied()">Mark as Applied</button>
</div>

<script>
const API_BASE = '/api/';
const params = new URLSearchParams(window.location.search);
const appId = params.get('id');
let currentApp = null;

async function apiGet(path) {
  const res = await fetch(API_BASE + path);
  return res.json();
}
async function apiPost(path, data) {
  const res = await fetch(API_BASE + path, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data) });
  return res.json();
}
async function apiPut(path, data) {
  const res = await fetch(API_BASE + path, { method:'PUT', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data) });
  return res.json();
}
function showToast(msg, isError) {
  let t = document.getElementById('toast');
  if (!t) { t = document.createElement('div'); t.id='toast'; t.className='toast'; document.body.appendChild(t); }
  t.textContent = msg;
  t.classList.toggle('error', !!isError);
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

function renderStepper(status) {
  const terminal = ['rejected','ghosted'].includes(status);
  const steps = [
    {key:'draft',label:'Draft',num:1},{key:'resume_ready',label:'Resume',num:2},
    {key:'cover_ready',label:'Cover Ltr',num:3},{key:'applied',label:'Applied',num:4},
    {key:'interview',label:'Interview',num:5},{key:'offer',label:'Outcome',num:6}
  ];
  const order = steps.map(s=>s.key);
  let cur = terminal ? order.indexOf('applied') : order.indexOf(status);
  let html = '<div class="stepper">';
  steps.forEach((s,i) => {
    let cls = i<cur ? 'done' : (i===cur ? 'current' : '');
    const label = (terminal && s.key==='offer') ? (status==='rejected'?'Rejected':'Ghosted') : s.label;
    html += `<div class="stepper-step ${cls}"><div class="dot">${cls==='done'?'&#10003;':s.num}<div class="stepper-line"></div></div><div class="label">${label}</div></div>`;
  });
  return html + '</div>';
}

async function loadJob() {
  if (!appId) { document.getElementById('job-title').textContent = 'No job selected'; return; }
  const res = await apiGet('applications');
  const apps = res.data?.items || res.data || [];
  currentApp = apps.find(a => a.id == appId);
  if (!currentApp) { document.getElementById('job-title').textContent = 'Job not found'; return; }
  document.title = currentApp.company + ' — Work This Job';
  document.getElementById('job-title').textContent = currentApp.company + ' — ' + currentApp.title;
  document.getElementById('job-sub').textContent = (currentApp.location||'') + ' · ' + (currentApp.method||'');
  document.getElementById('stepper').innerHTML = renderStepper(currentApp.status);
  document.getElementById('job-details').textContent = currentApp.notes || 'No saved job description.';
  document.getElementById('r-role').value = currentApp.title || '';
  document.getElementById('r-company').value = currentApp.company || '';
  document.getElementById('r-jd').value = currentApp.notes || '';
  const btn = document.getElementById('mark-applied-btn');
  if (!['draft','resume_ready','cover_ready'].includes(currentApp.status)) btn.style.display = 'none';
}

async function genResume() {
  const master = ''; // master resume not duplicated here; uses saved settings server-side if applicable
  showToast('Generating resume...');
  try {
    const settingsRes = await apiGet('settings');
    const s = settingsRes.data?.items?.[0] || settingsRes.data || {};
    const result = await apiPost('resumes/generate', {
      master_resume: s.master_resume || '',
      job_description: document.getElementById('r-jd').value,
      target_role: document.getElementById('r-role').value,
      company: document.getElementById('r-company').value,
      application_id: appId
    });
    if (result.success) {
      document.getElementById('r-output').style.display = 'block';
      document.getElementById('r-output').textContent = result.data.content || '';
      showToast('Resume generated');
      loadJob();
    } else { showToast('Error: ' + (result.error?.message || result.error), true); }
  } catch(e) { showToast('Error generating resume', true); }
}

async function genCoverLetter() {
  showToast('Generating cover letter...');
  try {
    const result = await apiPost('cover-letters/generate', {
      job_title: currentApp.title,
      company_name: currentApp.company,
      background: document.getElementById('r-jd').value,
      hiring_manager: document.getElementById('c-manager').value,
      tone: document.getElementById('c-tone').value,
      application_id: appId
    });
    if (result.success) {
      document.getElementById('c-output').style.display = 'block';
      document.getElementById('c-output').textContent = result.data.content || result.data.generated_text || '';
      showToast('Cover letter generated');
      loadJob();
    } else { showToast('Error: ' + (result.error?.message || result.error), true); }
  } catch(e) { showToast('Error generating cover letter', true); }
}

async function markApplied() {
  if (!confirm('Mark this application as Applied?')) return;
  try {
    await apiPut('applications/' + appId, { status:'applied', applied_at: new Date().toISOString().slice(0,19).replace('T',' ') });
    showToast('Marked as Applied');
    loadJob();
  } catch(e) { showToast('Error updating status', true); }
}

loadJob();
</script>
</body>
</html>