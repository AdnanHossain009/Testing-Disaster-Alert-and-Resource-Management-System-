/**
 * Live Dashboard Updates Module
 * Handles real-time updates for emergency requests, statistics, and status changes
 */

class LiveDashboard {
    constructor() {
        this.stats = {
            total: 0,
            pending: 0,
            inProgress: 0,
            completed: 0
        };
        
        this.requestsTable = null;
        this.initialized = false;
        this.connectionLost = false;
        this.refreshInterval = null;
        this.lastUpdateTime = Date.now();
    }

    /**
     * Initialize the live dashboard
     */
    init() {
        if (this.initialized) return;
        
        console.log('🎯 Initializing Live Dashboard...');
        
        this.requestsTable = document.querySelector('#requests-table tbody');
        this.loadCurrentStats();
        this.setupEventListeners();
        this.setupConnectionMonitoring();
        this.setupAutoRefreshFallback();
        
        this.initialized = true;
        console.log('✅ Live Dashboard initialized successfully');
    }

    /**
     * Load current statistics from the page
     */
    loadCurrentStats() {
        const totalEl = document.querySelector('[data-stat="total"]');
        const pendingEl = document.querySelector('[data-stat="pending"]');
        const inProgressEl = document.querySelector('[data-stat="in-progress"]');
        const completedEl = document.querySelector('[data-stat="completed"]');

        if (totalEl) this.stats.total = parseInt(totalEl.textContent) || 0;
        if (pendingEl) this.stats.pending = parseInt(pendingEl.textContent) || 0;
        if (inProgressEl) this.stats.inProgress = parseInt(inProgressEl.textContent) || 0;
        if (completedEl) this.stats.completed = parseInt(completedEl.textContent) || 0;

        console.log('📊 Current Stats:', this.stats);
    }

    /**
     * Setup event listeners for real-time updates
     */
    setupEventListeners() {
        // Listen for new request submissions
        if (window.Echo) {
            window.Echo.channel('emergency-requests')
                .listen('.NewRequestSubmitted', (event) => {
                    console.log('🆕 New request received:', event);
                    this.handleNewRequest(event.helpRequest);
                })
                .listen('.request.status.updated', (event) => {
                    console.log('🔄 Status update received:', event);
                    this.handleStatusUpdate(event);
                });
        }
    }

    /**
     * Handle new emergency request
     */
    handleNewRequest(request) {
        console.log('📝 Processing new request:', request);
        
        // Update last update time
        this.lastUpdateTime = Date.now();
        
        // Add to table
        this.addRequestToTable(request);
        
        // Update statistics
        this.updateStatistics('new', request);
        
        // Show success indicator
        this.showUpdateIndicator('New emergency request added!', 'success');
    }

    /**
     * Handle status update for existing request
     */
    handleStatusUpdate(event) {
        const { helpRequest, oldStatus, newStatus } = event;
        
        console.log(`📋 Updating request #${helpRequest.id}: ${oldStatus} → ${newStatus}`);
        
        // Update last update time
        this.lastUpdateTime = Date.now();
        
        // Update table row
        this.updateRequestInTable(helpRequest);
        
        // Update statistics
        this.updateStatistics('status-change', helpRequest, oldStatus, newStatus);
        
        // Show update indicator
        this.showUpdateIndicator(`Request #${helpRequest.id} status updated to ${newStatus}`, 'info');
    }

    /**
     * Add new request to the table
     */
    addRequestToTable(request) {
        if (!this.requestsTable) return;

        const row = this.createRequestRow(request);
        
        // Add animation class
        row.classList.add('new-row-animation');
        
        // Insert at the top of the table
        if (this.requestsTable.firstChild) {
            this.requestsTable.insertBefore(row, this.requestsTable.firstChild);
        } else {
            this.requestsTable.appendChild(row);
        }
        
        // Remove animation class after animation completes
        setTimeout(() => row.classList.remove('new-row-animation'), 1000);
        
        // Highlight the row temporarily
        setTimeout(() => row.classList.add('highlight-row'), 100);
        setTimeout(() => row.classList.remove('highlight-row'), 3000);
    }

    /**
     * Update existing request in table
     */
    updateRequestInTable(request) {
        if (!this.requestsTable) return;

        const row = this.requestsTable.querySelector(`tr[data-request-id="${request.id}"]`);
        
        if (row) {
            // Update status badge
            const statusCell = row.querySelector('.status-badge');
            if (statusCell) {
                statusCell.className = `status-badge ${this.getStatusClass(request.status)}`;
                statusCell.textContent = request.status;
                
                // Add pulse animation
                statusCell.classList.add('status-update-pulse');
                setTimeout(() => statusCell.classList.remove('status-update-pulse'), 1000);
            }
            
            // Highlight the row
            row.classList.add('highlight-row');
            setTimeout(() => row.classList.remove('highlight-row'), 3000);
        }
    }

    /**
     * Create a table row for a request
     */
    createRequestRow(request) {
        const row = document.createElement('tr');
        row.setAttribute('data-request-id', request.id);
        row.className = 'hover:bg-gray-50 transition-colors';
        
        const urgencyClass = this.getUrgencyClass(request.urgency_level);
        const statusClass = this.getStatusClass(request.status);
        
        row.innerHTML = `
            <td class="px-4 py-3 text-sm font-medium text-gray-900">#${request.id}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${this.escapeHtml(request.name)}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${this.escapeHtml(request.phone)}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${this.escapeHtml(request.location)}</td>
            <td class="px-4 py-3 text-sm text-gray-700">${this.escapeHtml(request.emergency_type)}</td>
            <td class="px-4 py-3 text-sm">
                <span class="urgency-badge ${urgencyClass}">
                    ${request.urgency_level}
                </span>
            </td>
            <td class="px-4 py-3 text-sm">
                <span class="status-badge ${statusClass}">
                    ${request.status}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-500">${this.formatDate(request.created_at)}</td>
            <td class="px-4 py-3 text-sm">
                <a href="/admin/requests/${request.id}" class="text-blue-600 hover:text-blue-800 font-medium">
                    View Details
                </a>
            </td>
        `;
        
        return row;
    }

    /**
     * Update dashboard statistics
     */
    updateStatistics(action, request, oldStatus = null, newStatus = null) {
        if (action === 'new') {
            // New request added
            this.stats.total++;
            if (request.status === 'Pending') this.stats.pending++;
            else if (request.status === 'In Progress') this.stats.inProgress++;
            else if (request.status === 'Completed') this.stats.completed++;
        } else if (action === 'status-change') {
            // Status changed
            if (oldStatus === 'Pending') this.stats.pending--;
            else if (oldStatus === 'In Progress') this.stats.inProgress--;
            else if (oldStatus === 'Completed') this.stats.completed--;
            
            if (newStatus === 'Pending') this.stats.pending++;
            else if (newStatus === 'In Progress') this.stats.inProgress++;
            else if (newStatus === 'Completed') this.stats.completed++;
        }
        
        // Update DOM
        this.renderStatistics();
    }

    /**
     * Render updated statistics to DOM
     */
    renderStatistics() {
        const totalEl = document.querySelector('[data-stat="total"]');
        const pendingEl = document.querySelector('[data-stat="pending"]');
        const inProgressEl = document.querySelector('[data-stat="in-progress"]');
        const completedEl = document.querySelector('[data-stat="completed"]');

        if (totalEl) this.animateNumber(totalEl, this.stats.total);
        if (pendingEl) this.animateNumber(pendingEl, this.stats.pending);
        if (inProgressEl) this.animateNumber(inProgressEl, this.stats.inProgress);
        if (completedEl) this.animateNumber(completedEl, this.stats.completed);
    }

    /**
     * Animate number change
     */
    animateNumber(element, newValue) {
        const oldValue = parseInt(element.textContent) || 0;
        
        if (oldValue === newValue) return;
        
        element.classList.add('stat-update-pulse');
        element.textContent = newValue;
        
        setTimeout(() => element.classList.remove('stat-update-pulse'), 600);
    }

    /**
     * Show update indicator
     */
    showUpdateIndicator(message, type = 'info') {
        const indicator = document.createElement('div');
        indicator.className = `update-indicator ${type}`;
        indicator.textContent = message;
        
        document.body.appendChild(indicator);
        
        setTimeout(() => indicator.classList.add('show'), 100);
        setTimeout(() => indicator.classList.remove('show'), 3000);
        setTimeout(() => indicator.remove(), 3500);
    }

    /**
     * Get urgency level CSS class
     */
    getUrgencyClass(urgency) {
        const classes = {
            'Critical': 'urgency-critical',
            'High': 'urgency-high',
            'Medium': 'urgency-medium',
            'Low': 'urgency-low'
        };
        return classes[urgency] || 'urgency-medium';
    }

    /**
     * Get status CSS class
     */
    getStatusClass(status) {
        const classes = {
            'Pending': 'status-pending',
            'Assigned': 'status-assigned',
            'In Progress': 'status-in-progress',
            'Completed': 'status-completed',
            'Cancelled': 'status-cancelled'
        };
        return classes[status] || 'status-pending';
    }

    /**
     * Format date for display
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000); // seconds
        
        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)} mins ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
        
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Setup connection monitoring
     */
    setupConnectionMonitoring() {
        if (!window.Echo || !window.Echo.connector || !window.Echo.connector.pusher) {
            console.warn('Echo or Pusher not available for connection monitoring');
            return;
        }

        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            this.connectionLost = true;
            // DISABLED: Toast notification (fallback mode handles updates silently)
            console.warn('⚠️ WebSocket disconnected. Auto-refresh activated.');
        });

        window.Echo.connector.pusher.connection.bind('connected', () => {
            if (this.connectionLost) {
                this.connectionLost = false;
                this.showUpdateIndicator('Connection restored! Real-time updates active.', 'success');
                console.log('✅ WebSocket reconnected');
            }
        });
    }

    /**
     * Setup auto-refresh fallback when WebSocket is down
     */
    setupAutoRefreshFallback() {
        // Check every 30 seconds if connection is lost
        this.refreshInterval = setInterval(() => {
            const timeSinceUpdate = Date.now() - this.lastUpdateTime;
            
            // If no updates in 2 minutes or connection is lost, refresh data
            if (this.connectionLost || timeSinceUpdate > 120000) {
                console.log('📡 Performing fallback data refresh...');
                this.performFallbackRefresh();
            }
        }, 30000); // Check every 30 seconds
    }

    /**
     * Perform fallback data refresh via AJAX
     */
    performFallbackRefresh() {
        // Only refresh if page is visible
        if (document.hidden) return;

        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the response to extract updated statistics
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update statistics from fresh data
            const stats = {
                total: parseInt(doc.querySelector('[data-stat="total-alerts"]')?.textContent || '0'),
                pending: parseInt(doc.querySelector('[data-stat="pending"]')?.textContent || '0'),
                inProgress: parseInt(doc.querySelector('[data-stat="in-progress"]')?.textContent || '0'),
                completed: parseInt(doc.querySelector('[data-stat="completed"]')?.textContent || '0')
            };
            
            // Check if there are changes
            if (JSON.stringify(stats) !== JSON.stringify(this.stats)) {
                this.stats = stats;
                this.renderStatistics();
                this.showUpdateIndicator('Dashboard refreshed', 'info');
            }
            
            this.lastUpdateTime = Date.now();
        })
        .catch(error => {
            console.error('Fallback refresh failed:', error);
        });
    }

    /**
     * Optimistic UI update for status changes
     */
    optimisticStatusUpdate(requestId, newStatus) {
        if (!this.requestsTable) return;

        const row = this.requestsTable.querySelector(`tr[data-request-id="${requestId}"]`);
        
        if (row) {
            const statusCell = row.querySelector('.status-badge');
            if (statusCell) {
                // Store old status for rollback if needed
                const oldStatus = statusCell.textContent;
                statusCell.dataset.oldStatus = oldStatus;
                
                // Apply optimistic update
                statusCell.className = `status-badge ${this.getStatusClass(newStatus)}`;
                statusCell.textContent = newStatus;
                statusCell.style.opacity = '0.6'; // Visual indicator of pending update
                
                // Add a small loading indicator
                const loadingDot = document.createElement('span');
                loadingDot.className = 'loading-dot';
                loadingDot.textContent = ' ⏳';
                statusCell.appendChild(loadingDot);
                
                return {
                    rollback: () => {
                        statusCell.className = `status-badge ${this.getStatusClass(oldStatus)}`;
                        statusCell.textContent = oldStatus;
                        statusCell.style.opacity = '1';
                    },
                    confirm: () => {
                        statusCell.style.opacity = '1';
                        if (loadingDot.parentNode) {
                            loadingDot.remove();
                        }
                    }
                };
            }
        }
        
        return null;
    }

    /**
     * Cleanup when page unloads
     */
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.liveDashboard = new LiveDashboard();
        window.liveDashboard.init();
    });
} else {
    window.liveDashboard = new LiveDashboard();
    window.liveDashboard.init();
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.liveDashboard) {
        window.liveDashboard.destroy();
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LiveDashboard;
}
