document.addEventListener('DOMContentLoaded', () => {
    const badge = document.getElementById('notification-badge');
    const container = document.getElementById('notification-items-container');
    const markAllBtn = document.getElementById('mark-all-read-btn');
    
    if (!badge || !container) return;

    let unreadIds = [];

    // Fetch new notifications
    const fetchNotifications = async () => {
        try {
            const res = await fetch('/api/notifications');
            const data = await res.json();
            
            updateUI(data.count, data.notifications);
        } catch (e) {
            console.error('Failed to fetch notifications', e);
        }
    };

    // Update the HTML
    const updateUI = (count, notifications) => {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'inline-block';
            markAllBtn.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
            markAllBtn.style.display = 'none';
            container.innerHTML = `<li><span class="dropdown-item text-center text-muted small py-3">Aucune nouvelle notification</span></li>`;
            return;
        }

        let html = '';
        unreadIds = [];

        notifications.forEach(n => {
            unreadIds.push(n.id);
            html += `
                <li>
                    <a class="dropdown-item rounded mb-1 border-bottom pb-2 d-flex justify-content-between align-items-start notification-item" 
                       href="${n.link || '#'}" 
                       data-id="${n.id}">
                        <div>
                            <small class="fw-bold d-block text-primary">${n.type}</small>
                            <small class="text-dark d-block text-wrap" style="width: 250px;">${n.message}</small>
                            <small class="text-muted" style="font-size: 0.70rem;">${n.time}</small>
                        </div>
                        <button class="btn btn-sm text-success p-0 mark-read-btn" title="Marquer comme lu">
                            <i class="bi bi-check-circle"></i>
                        </button>
                    </a>
                </li>
            `;
        });

        container.innerHTML = html;

        // Attach click events on new items
        document.querySelectorAll('.mark-read-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = e.currentTarget.closest('a').dataset.id;
                markAsRead(id);
            });
        });
        
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if(!e.target.closest('.mark-read-btn')) {
                    const id = e.currentTarget.dataset.id;
                    // fire and forget then let it navigate
                    fetch(`/api/notifications/${id}/read`, { method: 'POST' });
                }
            });
        });
    };

    // Mark single as read
    const markAsRead = async (id) => {
        try {
            await fetch(`/api/notifications/${id}/read`, { method: 'POST' });
            fetchNotifications(); // Refresh 
        } catch (e) {
            console.error(e);
        }
    };

    // Mark all as read
    if (markAllBtn) {
        markAllBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            try {
                await fetch('/api/notifications/read-all', { method: 'POST' });
                fetchNotifications();
            } catch (e) {
                console.error(e);
            }
        });
    }

    // Initial fetch
    fetchNotifications();

    // Poll every 15 seconds
    setInterval(fetchNotifications, 15000);
});
