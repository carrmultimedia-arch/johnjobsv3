<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar — JobHunt Command Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f0f13;
            --bg-card: #1a1a24;
            --bg-card-hover: #222230;
            --accent: #c9a84c;
            --accent-dim: #8a7340;
            --accent-glow: rgba(201,168,76,0.15);
            --text: #e8e6e1;
            --text-dim: #8a8880;
            --text-muted: #5a5850;
            --border: #2a2a35;
            --font-display: 'Cinzel', 'Georgia', serif;
            --font-body: 'Inter', -apple-system, sans-serif;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: var(--bg-dark);
            color: var(--text);
            font-family: var(--font-body);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-family: var(--font-display);
            font-size: 2rem;
            color: var(--accent);
        }
        
        .nav-links {
            display: flex;
            gap: 16px;
        }
        
        .nav-links a {
            color: var(--text-dim);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        
        .nav-links a:hover {
            color: var(--accent);
        }
        
        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .month-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .month-nav button {
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .month-nav button:hover {
            border-color: var(--accent);
            color: var(--accent);
        }
        
        .current-month {
            font-family: var(--font-display);
            font-size: 1.5rem;
            color: var(--accent);
        }
        
        .add-event-btn {
            background: var(--accent);
            color: var(--bg-dark);
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }
        
        .add-event-btn:hover {
            background: #d4b55a;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .calendar-header {
            background: var(--bg-card);
            padding: 12px;
            text-align: center;
            font-weight: 600;
            color: var(--text-dim);
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        
        .calendar-day {
            background: var(--bg-dark);
            min-height: 120px;
            padding: 8px;
            position: relative;
        }
        
        .calendar-day.other-month {
            background: var(--bg-card);
            opacity: 0.5;
        }
        
        .calendar-day.today {
            background: var(--accent-glow);
        }
        
        .day-number {
            font-size: 0.9rem;
            color: var(--text-dim);
            margin-bottom: 8px;
        }
        
        .calendar-day.today .day-number {
            color: var(--accent);
            font-weight: 600;
        }
        
        .event-dot {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 4px;
            margin-bottom: 4px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .event-dot:hover {
            background: var(--bg-card-hover);
        }
        
        .event-dot .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        
        .event-dot.interview .dot { background: #2196f3; }
        .event-dot.followup .dot { background: #ff9800; }
        .event-dot.manual .dot { background: #4caf50; }
        
        .event-dot .label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-dim);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            width: 100%;
            max-width: 500px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            font-family: var(--font-display);
            color: var(--accent);
        }
        
        .close-btn {
            background: none;
            border: none;
            color: var(--text-dim);
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .close-btn:hover {
            color: var(--accent);
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            background: var(--bg-dark);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px 12px;
            color: var(--text);
            font-family: var(--font-body);
            font-size: 0.9rem;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent);
        }
        
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--accent);
            color: var(--bg-dark);
        }
        
        .btn-primary:hover {
            background: #d4b55a;
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
        
        .btn-danger {
            background: #f44336;
            color: white;
        }
        
        .btn-danger:hover {
            background: #d32f2f;
        }
        
        .event-details {
            margin-bottom: 20px;
        }
        
        .event-details p {
            margin-bottom: 8px;
            color: var(--text-dim);
        }
        
        .event-details strong {
            color: var(--text);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Calendar</h1>
            <div class="nav-links">
                <a href="/public/index.php">← Back to Command Center</a>
            </div>
        </div>
        
        <div class="calendar-controls">
            <div class="month-nav">
                <button onclick="changeMonth(-1)">← Previous</button>
                <div class="current-month" id="currentMonth">August 2026</div>
                <button onclick="changeMonth(1)">Next →</button>
            </div>
            <button class="add-event-btn" onclick="openAddEventModal()">+ Add Event</button>
        </div>
        
        <div class="calendar-grid" id="calendarGrid">
            <!-- Calendar will be rendered here -->
        </div>
    </div>
    
    <!-- Add/Edit Event Modal -->
    <div class="modal" id="eventModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add Event</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="form-group">
                <label>Title *</label>
                <input type="text" id="eventTitle" placeholder="Event title">
            </div>
            <div class="form-group">
                <label>Date *</label>
                <input type="date" id="eventDate">
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" id="eventTime">
            </div>
            <div class="form-group">
                <label>Link to Application</label>
                <select id="eventApplication">
                    <option value="">-- None --</option>
                </select>
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea id="eventNotes" rows="3"></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveEvent()">Save</button>
            </div>
        </div>
    </div>
    
    <!-- View Event Modal -->
    <div class="modal" id="viewEventModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Event Details</h2>
                <button class="close-btn" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="event-details" id="eventDetails">
                <!-- Event details will be rendered here -->
            </div>
            <div class="modal-actions" id="viewEventActions">
                <!-- Actions will be rendered here -->
            </div>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let currentEventId = null;
        let manualEvents = []; // Store manual events for editing
        
        const API_BASE = '/api';
        
        async function fetchEvents() {
            const month = formatDate(currentDate);
            try {
                const response = await fetch(`${API_BASE}/calendar/events?month=${month}`);
                const data = await response.json();
                if (data.success) {
                    events = data.data;
                    // Also fetch manual events separately for editing capability
                    await fetchManualEvents();
                    renderCalendar();
                }
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        }
        
        async function fetchManualEvents() {
            try {
                const response = await fetch(`${API_BASE}/calendar-events`);
                const data = await response.json();
                if (data.success) {
                    manualEvents = data.data.items || data.data || [];
                    console.log('Manual events loaded:', manualEvents);
                }
            } catch (error) {
                console.error('Error fetching manual events:', error);
            }
        }
        
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            return `${year}-${month}`;
        }
        
        function renderCalendar() {
            const grid = document.getElementById('calendarGrid');
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                               'July', 'August', 'September', 'October', 'November', 'December'];
            
            document.getElementById('currentMonth').textContent = 
                `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
            
            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const startingDay = firstDay.getDay();
            const totalDays = lastDay.getDate();
            
            const prevMonthLastDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0).getDate();
            
            let html = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
                .map(day => `<div class="calendar-header">${day}</div>`).join('');
            
            // Previous month days
            for (let i = startingDay - 1; i >= 0; i--) {
                const day = prevMonthLastDay - i;
                html += `<div class="calendar-day other-month">
                    <div class="day-number">${day}</div>
                </div>`;
            }
            
            // Current month days
            const today = new Date();
            for (let day = 1; day <= totalDays; day++) {
                const dateStr = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const isToday = today.getFullYear() === currentDate.getFullYear() && 
                                today.getMonth() === currentDate.getMonth() && 
                                today.getDate() === day;
                
                const dayEvents = events.filter(e => e.date === dateStr);
                
                let eventsHtml = dayEvents.map(event => {
                    const eventClass = event.type;
                    const label = event.label || 'Event';
                    const eventId = event.event_id || null;
                    return `<div class="event-dot ${eventClass}" onclick="handleEventClick('${event.type}', ${event.application_id}, '${label}', '${dateStr}', '${event.time || ''}', ${eventId})">
                        <div class="dot"></div>
                        <div class="label">${label}</div>
                    </div>`;
                }).join('');
                
                html += `<div class="calendar-day ${isToday ? 'today' : ''}">
                    <div class="day-number">${day}</div>
                    ${eventsHtml}
                </div>`;
            }
            
            // Next month days
            const remainingCells = 42 - (startingDay + totalDays);
            for (let i = 1; i <= remainingCells; i++) {
                html += `<div class="calendar-day other-month">
                    <div class="day-number">${i}</div>
                </div>`;
            }
            
            grid.innerHTML = html;
        }
        
        function changeMonth(delta) {
            currentDate.setMonth(currentDate.getMonth() + delta);
            fetchEvents();
        }
        
        function handleEventClick(type, applicationId, label, date, time, eventId) {
            if (type === 'interview' || type === 'followup') {
                if (applicationId) {
                    window.location.href = `/public/workspace.php?id=${applicationId}`;
                }
            } else if (type === 'manual') {
                // Find the manual event by ID if available
                const manualEvent = manualEvents.find(e => e.id === eventId);
                if (manualEvent) {
                    showEventDetails(manualEvent);
                } else {
                    showEventDetails({ title: label, event_date: date, event_time: time, id: null });
                }
            }
        }
        
        function showEventDetails(event) {
            const detailsHtml = `
                <p><strong>Title:</strong> ${event.title}</p>
                <p><strong>Date:</strong> ${event.event_date}</p>
                ${event.event_time ? `<p><strong>Time:</strong> ${event.event_time}</p>` : ''}
                ${event.notes ? `<p><strong>Notes:</strong> ${event.notes}</p>` : ''}
                ${event.application_id ? `<p><strong>Linked Application ID:</strong> ${event.application_id}</p>` : ''}
            `;
            
            let actionsHtml = `<button class="btn btn-secondary" onclick="closeViewModal()">Close</button>`;
            
            // Check for both id and event_id to handle different response formats
            const eventId = event.id || event.event_id;
            if (eventId) {
                actionsHtml += `
                    <button class="btn btn-primary" onclick="editEvent(${eventId})">Edit</button>
                    <button class="btn btn-danger" onclick="deleteEvent(${eventId})">Delete</button>
                `;
            }
            
            document.getElementById('eventDetails').innerHTML = detailsHtml;
            document.getElementById('viewEventActions').innerHTML = actionsHtml;
            document.getElementById('viewEventModal').classList.add('active');
        }
        
        function openAddEventModal() {
            document.getElementById('modalTitle').textContent = 'Add Event';
            document.getElementById('eventTitle').value = '';
            document.getElementById('eventDate').value = '';
            document.getElementById('eventTime').value = '';
            document.getElementById('eventNotes').value = '';
            currentEventId = null;
            populateApplications();
            document.getElementById('eventModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('eventModal').classList.remove('active');
        }
        
        function closeViewModal() {
            document.getElementById('viewEventModal').classList.remove('active');
        }
        
        function editEvent(eventId) {
            closeViewModal();
            const event = manualEvents.find(e => e.id === eventId);
            if (!event) return;
            
            document.getElementById('modalTitle').textContent = 'Edit Event';
            document.getElementById('eventTitle').value = event.title;
            document.getElementById('eventDate').value = event.event_date;
            document.getElementById('eventTime').value = event.event_time || '';
            document.getElementById('eventNotes').value = event.notes || '';
            currentEventId = eventId;
            populateApplications().then(() => {
                document.getElementById('eventApplication').value = event.application_id || '';
            });
            document.getElementById('eventModal').classList.add('active');
        }
        
        async function deleteEvent(eventId) {
            if (!confirm('Are you sure you want to delete this event?')) return;
            
            try {
                const response = await fetch(`${API_BASE}/calendar-events/${eventId}`, {
                    method: 'DELETE'
                });
                
                const data = await response.json();
                if (data.success) {
                    closeViewModal();
                    fetchEvents();
                } else {
                    alert('Error deleting event: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error deleting event:', error);
                alert('Error deleting event');
            }
        }
        
        async function populateApplications() {
            try {
                const response = await fetch(`${API_BASE}/applications`);
                const data = await response.json();
                if (data.success) {
                    const applications = data.data.items || data.data || [];
                    const select = document.getElementById('eventApplication');
                    select.innerHTML = '<option value="">-- None --</option>' + 
                        applications.map(app => `<option value="${app.id}">${app.company} — ${app.title}</option>`).join('');
                }
            } catch (error) {
                console.error('Error fetching applications:', error);
            }
        }
        
        async function saveEvent() {
            const title = document.getElementById('eventTitle').value;
            const date = document.getElementById('eventDate').value;
            const time = document.getElementById('eventTime').value;
            const applicationId = document.getElementById('eventApplication').value;
            const notes = document.getElementById('eventNotes').value;
            
            if (!title || !date) {
                alert('Title and date are required');
                return;
            }
            
            const eventData = {
                title,
                event_date: date,
                event_time: time || null,
                application_id: applicationId || null,
                notes: notes || null
            };
            
            try {
                let response;
                if (currentEventId) {
                    // Update existing event
                    response = await fetch(`${API_BASE}/calendar-events/${currentEventId}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(eventData)
                    });
                } else {
                    // Create new event
                    response = await fetch(`${API_BASE}/calendar-events`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(eventData)
                    });
                }
                
                const data = await response.json();
                if (data.success) {
                    closeModal();
                    fetchEvents();
                } else {
                    alert('Error saving event: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving event:', error);
                alert('Error saving event');
            }
        }
        
        // Initialize
        fetchEvents();
    </script>
</body>
</html>