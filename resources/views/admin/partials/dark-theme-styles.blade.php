<!-- Material Dark Elegance Theme Styles -->
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        background: #0D1326;
        color: #E4E8F5;
        min-height: 100vh;
    }
    
    /* Header & Navigation */
    .admin-header { 
        background: #091F57;
        color: #E4E8F5;
        padding: 1.2rem 2rem; 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        flex-wrap: wrap;
        gap: 1rem;
    }
    .admin-header h1 { font-size: clamp(1.2rem, 3vw, 1.8rem); color: #E4E8F5; }
    .admin-nav { 
        background: #091F57;
        padding: 0.8rem 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        overflow-x: auto;
        white-space: nowrap;
    }
    .admin-nav a { 
        color: #E4E8F5;
        text-decoration: none; 
        margin-right: 1.5rem; 
        padding: 0.6rem 1.2rem; 
        border-radius: 6px;
        transition: all 0.3s ease;
        display: inline-block;
        font-size: clamp(0.85rem, 2vw, 0.95rem);
    }
    .admin-nav a:hover { background: rgba(43, 85, 189, 0.3); }
    .admin-nav a.active { background: #2B55BD; box-shadow: 0 2px 8px rgba(43, 85, 189, 0.4); }
    
    /* Container */
    .container { max-width: 1400px; margin: 2rem auto; padding: 0 1rem; }
    @media (max-width: 768px) { .container { margin: 1rem auto; padding: 0 0.5rem; } }
    
    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
    @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } }
    .stat-card { 
        background: linear-gradient(135deg, #091F57 0%, #0D1326 100%);
        padding: 1.8rem; 
        border-radius: 12px;
        border: 1px solid rgba(43, 85, 189, 0.2);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 6px 16px rgba(43, 85, 189, 0.4); }
    .stat-number { font-size: clamp(2rem, 5vw, 2.5rem); font-weight: bold; color: #2B55BD; text-shadow: 0 2px 4px rgba(43, 85, 189, 0.3); }
    .stat-label { color: #E4E8F5; margin-top: 0.5rem; font-size: clamp(0.85rem, 2vw, 0.95rem); opacity: 0.9; }
    
    /* Tables */
    .alerts-table, .shelters-table, .requests-table, .notifications-table { 
        background: #091F57;
        border-radius: 12px; 
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(43, 85, 189, 0.2);
    }
    @media (max-width: 768px) { 
        .alerts-table, .shelters-table, .requests-table, .notifications-table { overflow-x: auto; }
    }
    .alerts-table table, .shelters-table table, .requests-table table, .notifications-table table { 
        width: 100%; 
        border-collapse: collapse;
        min-width: 600px;
    }
    .alerts-table th, .alerts-table td,
    .shelters-table th, .shelters-table td,
    .requests-table th, .requests-table td,
    .notifications-table th, .notifications-table td { 
        padding: 1.2rem; 
        text-align: left; 
        border-bottom: 1px solid rgba(43, 85, 189, 0.2);
    }
    .alerts-table th, .shelters-table th, .requests-table th, .notifications-table th { 
        background: rgba(43, 85, 189, 0.2);
        font-weight: 600;
        color: #E4E8F5;
        font-size: clamp(0.85rem, 2vw, 0.95rem);
    }
    .alerts-table td, .shelters-table td, .requests-table td, .notifications-table td {
        color: #E4E8F5;
        font-size: clamp(0.8rem, 2vw, 0.9rem);
    }
    .alerts-table tr:hover, .shelters-table tr:hover, .requests-table tr:hover, .notifications-table tr:hover {
        background: rgba(43, 85, 189, 0.1);
    }
    
    /* Status Colors */
    .severity-critical, .priority-critical, .urgency-critical { color: #ff6b6b; font-weight: bold; text-shadow: 0 1px 3px rgba(255, 107, 107, 0.3); }
    .severity-high, .priority-high, .urgency-high { color: #ffa94d; font-weight: bold; text-shadow: 0 1px 3px rgba(255, 169, 77, 0.3); }
    .severity-medium, .priority-medium, .urgency-medium { color: #4ecdc4; text-shadow: 0 1px 3px rgba(78, 205, 196, 0.3); }
    .severity-low, .priority-low, .urgency-low { color: #95e1d3; }
    .status-active { color: #51cf66; font-weight: bold; text-shadow: 0 1px 3px rgba(81, 207, 102, 0.3); }
    .status-inactive { color: #ff6b6b; opacity: 0.8; }
    .status-pending { color: #ffa94d; font-weight: bold; }
    .status-assigned { color: #4ecdc4; font-weight: bold; }
    .status-completed { color: #51cf66; font-weight: bold; }
    .status-expired { color: #868e96; }
    .capacity-high { color: #ff6b6b; font-weight: bold; }
    .capacity-medium { color: #ffa94d; font-weight: bold; }
    .capacity-low { color: #51cf66; }
    
    /* Status Badges */
    .status { padding: 0.3rem 0.8rem; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
    .status.pending { background: rgba(255, 169, 77, 0.2); color: #ffa94d; }
    .status.assigned { background: rgba(78, 205, 196, 0.2); color: #4ecdc4; }
    .status.completed { background: rgba(81, 207, 102, 0.2); color: #51cf66; }
    .emergency-type { background: #ff6b6b; color: #E4E8F5; padding: 0.3rem 0.8rem; border-radius: 12px; font-size: 0.8em; }
    
    /* Buttons */
    .btn { 
        padding: 0.6rem 1.2rem; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer; 
        text-decoration: none; 
        display: inline-block;
        transition: all 0.3s ease;
        font-size: clamp(0.8rem, 2vw, 0.9rem);
        font-weight: 500;
        margin-right: 0.5rem;
    }
    .btn-primary { background: #2B55BD; color: #E4E8F5; box-shadow: 0 2px 8px rgba(43, 85, 189, 0.3); }
    .btn-primary:hover { background: #3d6fd4; box-shadow: 0 4px 12px rgba(43, 85, 189, 0.5); transform: translateY(-2px); }
    .btn-success { background: #51cf66; color: #091F57; box-shadow: 0 2px 8px rgba(81, 207, 102, 0.3); }
    .btn-success:hover { background: #69db7c; box-shadow: 0 4px 12px rgba(81, 207, 102, 0.5); transform: translateY(-2px); }
    .btn-warning { background: #ffa94d; color: #091F57; box-shadow: 0 2px 8px rgba(255, 169, 77, 0.3); }
    .btn-warning:hover { background: #ffb86c; box-shadow: 0 4px 12px rgba(255, 169, 77, 0.5); transform: translateY(-2px); }
    .btn-danger { background: #ff6b6b; color: #E4E8F5; box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3); }
    .btn-danger:hover { background: #ff8787; box-shadow: 0 4px 12px rgba(255, 107, 107, 0.5); transform: translateY(-2px); }
    .btn-assign { background: #9f7aea; color: #E4E8F5; box-shadow: 0 2px 8px rgba(159, 122, 234, 0.3); }
    .btn-assign:hover { background: #b197fc; box-shadow: 0 4px 12px rgba(159, 122, 234, 0.5); transform: translateY(-2px); }
    .btn-status { background: #38b2ac; color: #E4E8F5; box-shadow: 0 2px 8px rgba(56, 178, 172, 0.3); }
    .btn-status:hover { background: #4ecdc4; box-shadow: 0 4px 12px rgba(56, 178, 172, 0.5); transform: translateY(-2px); }
    .btn-secondary { background: rgba(43, 85, 189, 0.2); color: #E4E8F5; border: 1px solid rgba(43, 85, 189, 0.4); }
    .btn-secondary:hover { background: rgba(43, 85, 189, 0.3); border-color: #2B55BD; }
    
    /* Action Buttons Section */
    .action-buttons { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
    .action-buttons h2 { color: #E4E8F5; font-size: clamp(1.3rem, 4vw, 1.8rem); }
    
    /* Alert Messages */
    .alert-message { padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid; font-size: clamp(0.85rem, 2vw, 0.95rem); }
    .alert-success { background: rgba(81, 207, 102, 0.1); border-color: #51cf66; color: #51cf66; }
    .alert-error { background: rgba(255, 107, 107, 0.1); border-color: #ff6b6b; color: #ff6b6b; }
    
    /* Maps */
    .map-section { background: #091F57; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); margin-bottom: 2rem; overflow: hidden; border: 1px solid rgba(43, 85, 189, 0.2); }
    .map-header { background: linear-gradient(135deg, #2B55BD, #091F57); color: #E4E8F5; padding: 1.5rem; text-align: center; }
    .map-header h3 { margin: 0; font-size: clamp(1.2rem, 3vw, 1.5rem); }
    .map-header p { margin-top: 0.5rem; opacity: 0.9; font-size: clamp(0.85rem, 2vw, 0.95rem); }
    .map-controls { background: rgba(43, 85, 189, 0.1); padding: 1rem; border-bottom: 1px solid rgba(43, 85, 189, 0.2); display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
    .map-filter { display: flex; align-items: center; gap: 0.5rem; }
    .map-filter label { font-weight: 600; color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.9rem); }
    .map-filter select { padding: 0.5rem; border: 1px solid rgba(43, 85, 189, 0.3); border-radius: 4px; background: rgba(9, 31, 87, 0.5); color: #E4E8F5; }
    .view-toggle { display: flex; gap: 1rem; margin-bottom: 2rem; justify-content: center; flex-wrap: wrap; }
    .toggle-btn { padding: 0.75rem 1.5rem; border: 2px solid #2B55BD; background: transparent; color: #E4E8F5; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; font-size: clamp(0.85rem, 2vw, 0.95rem); }
    .toggle-btn.active { background: #2B55BD; box-shadow: 0 4px 12px rgba(43, 85, 189, 0.4); }
    .toggle-btn:hover { background: #2B55BD; box-shadow: 0 4px 12px rgba(43, 85, 189, 0.4); }
    
    /* Legend */
    .priority-legend, .status-legend { background: #091F57; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); margin-bottom: 2rem; border: 1px solid rgba(43, 85, 189, 0.2); }
    .priority-legend h4, .status-legend h4 { color: #E4E8F5; margin-bottom: 1rem; font-size: clamp(1rem, 2.5vw, 1.2rem); }
    .priority-legend h5, .status-legend h5 { color: #E4E8F5; margin-bottom: 0.5rem; font-size: clamp(0.9rem, 2vw, 1rem); opacity: 0.9; }
    .legend-item { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.9rem); }
    .legend-marker { width: 20px; height: 20px; border-radius: 50%; border: 2px solid rgba(228, 232, 245, 0.3); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); flex-shrink: 0; }
    .legend-marker.critical { background: #ff6b6b; }
    .legend-marker.high { background: #ffa94d; }
    .legend-marker.medium { background: #4ecdc4; }
    .legend-marker.low { background: #51cf66; }
    .legend-marker.pending { background: #ffa94d; }
    .legend-marker.assigned { background: #9f7aea; }
    .legend-marker.completed { background: #38b2ac; }
    .legend-marker.active { background: #51cf66; }
    .legend-marker.inactive { background: #ff6b6b; }
    .legend-marker.high-capacity { background: #ff6b6b; }
    .legend-marker.medium-capacity { background: #ffa94d; }
    .legend-marker.low-capacity { background: #51cf66; }
    
    /* Bulk Actions */
    .bulk-actions { background: #091F57; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); border: 1px solid rgba(43, 85, 189, 0.2); }
    .bulk-actions h3 { color: #E4E8F5; margin-bottom: 1rem; font-size: clamp(1.1rem, 3vw, 1.3rem); }
    .bulk-form { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
    .bulk-form select { padding: 0.6rem 1rem; border: 1px solid rgba(43, 85, 189, 0.3); border-radius: 6px; background: rgba(9, 31, 87, 0.5); color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.9rem); }
    .bulk-form label { color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.9rem); }
    #selected-count { color: #2B55BD; font-weight: 600; font-size: clamp(0.85rem, 2vw, 0.9rem); }
    
    /* Modal */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(13, 19, 38, 0.9); backdrop-filter: blur(4px); }
    .modal-content { background: #091F57; margin: 10% auto; padding: 2rem; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5); border: 1px solid rgba(43, 85, 189, 0.3); }
    @media (max-width: 768px) { .modal-content { margin: 20% auto; width: 95%; padding: 1.5rem; } }
    .close { color: #E4E8F5; float: right; font-size: 28px; font-weight: bold; cursor: pointer; opacity: 0.7; transition: opacity 0.3s; }
    .close:hover { opacity: 1; }
    
    /* Forms */
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.95rem); }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 0.75rem 1rem; border: 1px solid rgba(43, 85, 189, 0.3); border-radius: 6px; background: rgba(9, 31, 87, 0.3); color: #E4E8F5; font-size: clamp(0.85rem, 2vw, 0.95rem); transition: all 0.3s ease; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #2B55BD; box-shadow: 0 0 0 3px rgba(43, 85, 189, 0.2); background: rgba(9, 31, 87, 0.5); }
    .form-textarea { resize: vertical; min-height: 120px; }
    
    /* Scrollbar */
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: #0D1326; }
    ::-webkit-scrollbar-thumb { background: #2B55BD; border-radius: 5px; }
    ::-webkit-scrollbar-thumb:hover { background: #3d6fd4; }
</style>
