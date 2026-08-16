document.addEventListener('DOMContentLoaded', function() {

    // ── Restore collapse state from localStorage (applied to <html> on load) ──
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        document.body.classList.add('sidebar-collapsed');
    }

    // ── Shared helper: toggle sidebar collapsed state ──
    function toggleSidebar(forceCollapse) {
        const isCollapsed = forceCollapse !== undefined
            ? (document.body.classList.toggle('sidebar-collapsed', forceCollapse), forceCollapse)
            : document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
        const settingsSwitch = document.getElementById('sidebarCollapseSwitch');
        if (settingsSwitch) settingsSwitch.checked = isCollapsed;
        setTimeout(updateTooltips, 320);
    }

    // ── Desktop Sidebar Toggle (in topbar) ──
    const desktopToggleBtn = document.getElementById('sidebarToggleDesktop');
    if (desktopToggleBtn) {
        desktopToggleBtn.addEventListener('click', function () {
            toggleSidebar(); // toggles full-screen mode on desktop
        });
    }

    // ── Mobile off-canvas sidebar toggle (hamburger) ──
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            sidebar.classList.toggle('sidebar-open');
        });
    }

    // Close sidebar on overlay click (mobile)
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    overlay.addEventListener('click', function () {
        sidebar && sidebar.classList.remove('sidebar-open');
    });

    // ── Tooltips: set title from sidebar-text ──
    document.querySelectorAll('.sidebar-body .sidebar-link').forEach(link => {
        const textNode = link.querySelector('.sidebar-text');
        if (textNode) {
            link.setAttribute('data-bs-toggle', 'tooltip');
            link.setAttribute('data-bs-placement', 'right');
            link.setAttribute('title', textNode.innerText.trim());
        }
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });

    // Toggle tooltips based on sidebar collapsed state
    function updateTooltips() {
        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
        tooltipList.forEach(t => {
            if (t._element.closest('.sidebar-body')) {
                isCollapsed ? t.enable() : t.disable();
            }
        });
    }

    // Run on load
    updateTooltips();

    // Sync settings page collapse switch if present
    const collapseSwitch = document.getElementById('sidebarCollapseSwitch');
    if (collapseSwitch) {
        // Init switch state
        collapseSwitch.checked = document.body.classList.contains('sidebar-collapsed');
        collapseSwitch.addEventListener('change', function () {
            const isCollapsed = this.checked;
            document.body.classList.toggle('sidebar-collapsed', isCollapsed);
            localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
            setTimeout(updateTooltips, 320);
        });
    }

    // Sync dark mode switch on settings page
    const darkSwitch = document.getElementById('darkModeSwitchSettings');
    const darkLabel = document.getElementById('darkModeLabelSettings');
    if (darkSwitch) {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        darkSwitch.checked = isDark;
        if (darkLabel) darkLabel.innerText = isDark ? 'Mode Sombre : Activé' : 'Mode Sombre : Désactivé';

        darkSwitch.addEventListener('change', function () {
            const dark = this.checked;
            document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
            localStorage.setItem('theme', dark ? 'dark' : 'light');
            if (darkLabel) darkLabel.innerText = dark ? 'Mode Sombre : Activé' : 'Mode Sombre : Désactivé';
        });
    }

    // Topbar dark mode toggle if added
    const topbarDarkToggle = document.getElementById('topbarDarkToggle');
    if (topbarDarkToggle) {
        topbarDarkToggle.checked = document.documentElement.getAttribute('data-theme') === 'dark';
        topbarDarkToggle.addEventListener('change', function () {
            const dark = this.checked;
            document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
            localStorage.setItem('theme', dark ? 'dark' : 'light');
        });
    }
});
