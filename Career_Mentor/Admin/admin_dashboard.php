<?php
// Start session to access session variables
session_start();

// Check if user is logged in and is an Admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Career Mentor AI - Admin Dashboard</title>

    <!-- SweetAlert2 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

        :root {
            /* Admin Blue Theme */
            --primary-dark: #0b4a8f;
            --primary-main: #1565c0;
            --primary-light: #e3f2fd;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --sidebar-width: 245px;
            --sidebar-collapsed-width: 70px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #f1f5f9;
            height: 100vh;
            overflow: hidden;
        }

        /* Dashboard Layout Container */
        .dashboard-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
            background-color: var(--primary-light);
            position: relative;
        }

        /* FIXED SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-dark);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: width 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* Sidebar Brand Header */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            color: white;
            height: var(--header-height);
            cursor: default;
            user-select: none;
        }

        .sidebar-brand i {
            font-size: 24px;
            min-width: 30px;
            text-align: center;
        }

        .sidebar-brand span {
            font-weight: 700;
            font-size: 16px;
            white-space: nowrap;
            transition: opacity 0.2s;
        }

        .sidebar.collapsed .sidebar-brand span {
            opacity: 0;
            pointer-events: none;
        }

        /* Navigation Menu */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
            padding: 10px 0;
        }

        .nav-item {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 11px 20px;
            transition: all 0.2s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        .nav-item i {
            font-size: 17px;
            min-width: 30px;
            text-align: center;
        }

        .nav-item span {
            transition: opacity 0.2s;
        }

        .sidebar.collapsed .nav-item span {
            opacity: 0;
            pointer-events: none;
        }

        .nav-item:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Active Main Menu Item Style */
        .nav-item.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.22);
            border-left: 4px solid #90caf9;
            font-weight: 600;
        }

        .sidebar-bottom {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding-bottom: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* MAIN CONTAINER LAYOUT */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            height: 100vh;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .sidebar.collapsed~.main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        /* FIXED TOP HEADER */
        .top-header {
            height: var(--header-height);
            background-color: var(--card-bg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 900;
            position: sticky;
            top: 0;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .toggle-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-main);
            cursor: pointer;
            padding: 5px;
        }

        .top-header h1 {
            font-size: 18px;
            color: var(--text-main);
            cursor: default;
            user-select: none;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
        }

        .search-bar {
            background: #f8fafc;
            padding: 6px 15px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .search-bar input {
            border: none;
            outline: none;
            background: transparent;
            padding: 3px 5px;
            font-size: 13px;
        }

        .search-bar i {
            color: var(--text-muted);
        }

        /* Notification Bell Button */
        .notification-btn {
            position: relative;
            color: var(--primary-main);
            font-size: 18px;
            cursor: pointer;
            background: transparent;
            border: none;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .notification-btn:hover {
            background-color: var(--primary-light);
        }

        .notification-btn .badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            font-size: 9px;
            border-radius: 50%;
            padding: 2px 5px;
            font-weight: 600;
        }

        /* NOTIFICATION SHUTTER PANEL OVERLAY */
        .notification-shutter {
            position: absolute;
            top: calc(100% + 10px);
            right: 120px;
            width: 320px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            overflow: hidden;

            /* Hide animation state */
            opacity: 0;
            transform: translateY(-15px);
            visibility: hidden;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                visibility 0.3s ease;
        }

        .notification-shutter.active {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }

        .shutter-header {
            padding: 12px 16px;
            background-color: var(--primary-dark);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
        }

        .shutter-body {
            max-height: 280px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .notification-item {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 3px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .notification-item:hover {
            background-color: var(--primary-light);
        }

        .notification-item.unread {
            background-color: #bbdefb;
        }

        .notification-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
        }

        .notification-desc {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.3;
        }

        .notification-time {
            font-size: 9px;
            color: var(--primary-main);
            align-self: flex-end;
            margin-top: 2px;
        }

        .shutter-footer {
            padding: 8px 12px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .shutter-btn {
            font-size: 11px;
            font-weight: 600;
            color: var(--primary-main);
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .shutter-btn:hover {
            background-color: #bbdefb;
        }

        .shutter-btn.close-btn {
            color: #ef4444;
        }

        .shutter-btn.close-btn:hover {
            background-color: #fee2e2;
        }

        /* Profile Button Styling */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background-color 0.2s, transform 0.1s;
            border: 1px solid transparent;
        }

        .user-profile:hover {
            background-color: var(--primary-light);
            border-color: #90caf9;
            transform: translateY(-1px);
        }

        .user-profile img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
        }

        .user-role {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        /* MAIN SCROLLABLE CONTENT VIEW */
        .main-content {
            flex: 1;
            padding: 20px 30px;
            overflow-y: auto;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .sub-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .sub-header h2 {
            font-size: 18px;
            color: var(--primary-dark);
            font-weight: 600;
        }

        .submenu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .submenu-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            cursor: pointer;
            border: 1.5px solid rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
        }

        .submenu-card i {
            color: var(--primary-main);
            font-size: 16px;
            min-width: 20px;
            text-align: center;
        }

        .submenu-card span {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-main);
        }

        .submenu-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(21, 101, 192, 0.15);
            border-color: var(--primary-main);
        }

        .submenu-card.active-sub {
            background-color: var(--primary-main);
            border-color: var(--primary-dark);
            box-shadow: 0 4px 10px rgba(21, 101, 192, 0.25);
        }

        .submenu-card.active-sub i,
        .submenu-card.active-sub span {
            color: #ffffff !important;
            font-weight: 600;
        }

        .view-container {
            width: 100%;
            flex: 1;
            min-height: 480px;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .view-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .fab-chatbot {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 50px;
            height: 50px;
            background-color: var(--primary-main);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 22px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: transform 0.2s;
            z-index: 800;
        }

        .fab-chatbot:hover {
            transform: scale(1.1);
        }
    </style>

    <!-- FontAwesome Library -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="theme-blue">
    <div class="dashboard-wrapper">
        <!-- FIXED SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand" onclick="refreshPage()">
                <i class="fas fa-shield-halved"></i>
                <span>Admin Console</span>
            </div>

            <nav class="sidebar-nav">
                <a class="nav-item active" onclick="loadModule('admin_mgmt', this)">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin Management</span>
                </a>
                <a class="nav-item" onclick="loadModule('teacher_mgmt', this)">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Teacher Management</span>
                </a>
                <a class="nav-item" onclick="loadModule('student_mgmt', this)">
                    <i class="fas fa-user-graduate"></i>
                    <span>Student Management</span>
                </a>
                <a class="nav-item" onclick="loadModule('career_mgmt', this)">
                    <i class="fas fa-route"></i>
                    <span>Career Management</span>
                </a>
                <a class="nav-item" onclick="loadModule('study_mgmt', this)">
                    <i class="fas fa-book-bookmark"></i>
                    <span>Study Material</span>
                </a>
                <a class="nav-item" onclick="loadModule('opp_mgmt', this)">
                    <i class="fas fa-briefcase"></i>
                    <span>Opportunities</span>
                </a>
                <a class="nav-item" onclick="loadModule('resume_mgmt', this)">
                    <i class="fas fa-file-invoice"></i>
                    <span>Resume Management</span>
                </a>
                <a class="nav-item" onclick="loadModule('announcement_mgmt', this)">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcement</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <!-- Settings loads in iframe -->
                <a class="nav-item" onclick="openIndependentPage('settings.php', 'System Settings', this)">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <!-- Logout redirect with SweetAlert2 -->
                <a class="nav-item" onclick="confirmLogout(event)">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTAINER AREA -->
        <div class="main-wrapper">
            <!-- FIXED TOP HEADER -->
            <header class="top-header">
                <div class="header-left">
                    <button class="toggle-btn" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 id="dashboard-title" onclick="refreshPage()">
                        Admin Dashboard
                    </h1>
                </div>

                <div class="header-right">
                    <div class="search-bar">
                        <input type="text" placeholder="Search Console..." />
                        <i class="fas fa-search"></i>
                    </div>

                    <!-- Notification Bell Button -->
                    <button class="notification-btn" onclick="toggleNotificationShutter(event)" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge" id="notif-badge">3</span>
                    </button>

                    <!-- NOTIFICATION SHUTTER PANEL OVERLAY -->
                    <div class="notification-shutter" id="notification-shutter" onclick="event.stopPropagation()">
                        <div class="shutter-header">
                            <span><i class="fas fa-bell me-1"></i> System Logs</span>
                            <span class="badge bg-danger" style="font-size:10px;">Top 5 Latest</span>
                        </div>

                        <div class="shutter-body" id="shutter-body">
                            <!-- Notifications injected via JavaScript -->
                        </div>

                        <div class="shutter-footer">
                            <button class="shutter-btn close-btn" onclick="closeNotificationShutter()">
                                <i class="fas fa-times"></i> Close
                            </button>
                            <button class="shutter-btn" onclick="viewAllNotifications()">
                                View All <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Profile Button (Loads profile.php in iframe) -->
                    <div class="user-profile" onclick="openIndependentPage('profile.php', 'Admin Profile', null)" title="View Profile">
                        <img
                            src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['fullname'] ?? 'Admin'); ?>&background=1565c0&color=fff"
                            alt="Admin Profile" />
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($_SESSION['fullname'] ?? 'System Admin'); ?></span>
                            <span class="user-role"><?= htmlspecialchars($_SESSION['role'] ?? 'Admin'); ?></span>
                        </div>
                    </div>

                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="main-content">
                <div class="sub-header">
                    <h2 id="active-module-title">1. Admin Management</h2>
                </div>

                <!-- Dynamic Submenu Bullet Options -->
                <div class="submenu-grid" id="submenu-container">
                    <!-- Dynamic Buttons Injected via JavaScript -->
                </div>

                <!-- Embedded View Box / Frame Page -->
                <div class="view-container">
                    <iframe id="content-iframe" src="about:blank"></iframe>
                </div>

                <!-- Floating Action Button -->
                <button
                    class="fab-chatbot"
                    onclick="triggerAssistantClick()"
                    title="Admin System Assistant">
                    <i class="fas fa-robot"></i>
                </button>
            </main>
        </div>
    </div>

    <!-- DASHBOARD SCRIPT & LOGIC -->
    <script>
        // PDF Admin Modules, Specific FontAwesome Icons, and dedicated .php Files Mapping
        const adminData = {
            admin_mgmt: {
                title: "1. Admin Management",
                options: [{
                        name: "View All Admin",
                        icon: "fas fa-users-gear",
                        file: "view_all_admin.php"
                    },
                    {
                        name: "Add Admin",
                        icon: "fas fa-user-plus",
                        file: "add_admin.php"
                    },
                    {
                        name: "Update Details",
                        icon: "fas fa-user-pen",
                        file: "update_details.php"
                    }
                ],
            },
            teacher_mgmt: {
                title: "2. Teacher Management",
                options: [{
                        name: "Add Teacher",
                        icon: "fas fa-chalkboard-user",
                        file: "add_teacher.php"
                    },
                    {
                        name: "Update Teacher Details",
                        icon: "fas fa-address-card",
                        file: "update_teacher_details.php"
                    },
                    {
                        name: "View Teacher List",
                        icon: "fas fa-list-check",
                        file: "view_teacher_list.php"
                    }
                ],
            },
            student_mgmt: {
                title: "3. Student Management",
                options: [{
                        name: "View Student List",
                        icon: "fas fa-users",
                        file: "view_student_list.php"
                    },
                    {
                        name: "View Student Progress",
                        icon: "fas fa-chart-line",
                        file: "view_student_progress.php"
                    },
                    {
                        name: "Add Student",
                        icon: "fas fa-user-graduate",
                        file: "add_student.php"
                    },
                    {
                        name: "Manage Student Accounts",
                        icon: "fas fa-user-gear",
                        file: "manage_student_accounts.php"
                    }
                ],
            },
            career_mgmt: {
                title: "4. Career Management",
                options: [{
                        name: "Add Career Paths",
                        icon: "fas fa-road",
                        file: "add_career_paths.php"
                    },
                    {
                        name: "Update Career Roadmaps",
                        icon: "fas fa-map-location-dot",
                        file: "update_career_roadmaps.php"
                    }
                ],
            },
            study_mgmt: {
                title: "5. Study Material Management",
                options: [{
                        name: "View Materials",
                        icon: "fas fa-folder-open",
                        file: "view_materials.php"
                    },
                    {
                        name: "Approve Materials",
                        icon: "fas fa-file-circle-check",
                        file: "approve_materials.php"
                    },
                    {
                        name: "Delete Materials",
                        icon: "fas fa-file-circle-xmark",
                        file: "delete_materials.php"
                    }
                ],
            },
            opp_mgmt: {
                title: "6. Opportunities Management",
                options: [{
                        name: "Add Internship Details",
                        icon: "fas fa-building-user",
                        file: "add_internship_details.php"
                    },
                    {
                        name: "Add Scholarships",
                        icon: "fas fa-graduation-cap",
                        file: "add_scholarships.php"
                    },
                    {
                        name: "Add Hackathon Details",
                        icon: "fas fa-laptop-code",
                        file: "add_hackathon_details.php"
                    }
                ],
            },
            resume_mgmt: {
                title: "7. Resume Management",
                options: [{
                        name: "View Submitted Resumes",
                        icon: "fas fa-file-csv",
                        file: "view_submitted_resumes.php"
                    },
                    {
                        name: "Manage Resume Templates",
                        icon: "fas fa-sliders",
                        file: "manage_resume_templates.php"
                    }
                ],
            },
            announcement_mgmt: {
                title: "8. Announcement Management",
                options: [{
                        name: "Add Announcement",
                        icon: "fas fa-bullhorn",
                        file: "add_announcement.php"
                    },
                    {
                        name: "Edit/Delete Announcement",
                        icon: "fas fa-pen-to-square",
                        file: "edit_delete_announcement.php"
                    }
                ],
            },
        };

        // Top Latest System Notifications Array Object
        const notificationsData = [{
                id: 1,
                title: "New Teacher Registration",
                desc: "Prof. Sarah Jenkins requested account activation.",
                time: "5 mins ago",
                unread: true
            },
            {
                id: 2,
                title: "Study Material Pending Approval",
                desc: "3 new CS PDFs uploaded for approval.",
                time: "1 hour ago",
                unread: true
            },
            {
                id: 3,
                title: "System Backup Completed",
                desc: "Automated database cloud backup successful.",
                time: "4 hours ago",
                unread: false
            }
        ];

        function refreshPage() {
            window.location.reload();
        }

        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
        }

        /* SWEETALERT2 LOGOUT CONFIRMATION */
        function confirmLogout(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Logout Confirmation',
                text: 'Are you sure you want to exit Admin Console?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1565c0',
                cancelButtonColor: '#ef4444',
                confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Yes, Logout',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                borderRadius: '12px',
                customClass: {
                    popup: 'animated fadeInDown'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../logout.php";
                }
            });
        }

        /* SMOOTH ANIMATED NOTIFICATION SHUTTER CONTROLS */
        function toggleNotificationShutter(event) {
            event.stopPropagation();
            const shutter = document.getElementById("notification-shutter");

            if (shutter.classList.contains("active")) {
                closeNotificationShutter();
            } else {
                renderNotifications();
                shutter.classList.add("active");
            }
        }

        function closeNotificationShutter() {
            const shutter = document.getElementById("notification-shutter");
            shutter.classList.remove("active");
        }

        function renderNotifications() {
            const body = document.getElementById("shutter-body");
            body.innerHTML = "";

            if (notificationsData.length === 0) {
                body.innerHTML = `<div style="padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;">No system logs</div>`;
                return;
            }

            notificationsData.slice(0, 5).forEach(notif => {
                const item = document.createElement("div");
                item.className = `notification-item ${notif.unread ? 'unread' : ''}`;
                item.innerHTML = `
            <div class="notification-title">${notif.title}</div>
            <div class="notification-desc">${notif.desc}</div>
            <div class="notification-time">${notif.time}</div>
          `;
                item.onclick = () => {
                    closeNotificationShutter();
                    openIndependentPage('notifications.php', 'Notifications', null);
                };
                body.appendChild(item);
            });
        }

        function viewAllNotifications() {
            closeNotificationShutter();
            openIndependentPage('notifications.php', 'Notifications', null);
        }

        // Close shutter smoothly when clicking anywhere outside
        window.addEventListener("click", () => {
            closeNotificationShutter();
        });

        // Load module and generate sub-option buttons with custom icons
        function loadModule(moduleKey, element) {
            const module = adminData[moduleKey];
            if (!module) return;

            // 1. Update active class on Sidebar
            const navItems = document.querySelectorAll(".sidebar-nav .nav-item, .sidebar-bottom .nav-item");
            navItems.forEach((item) => item.classList.remove("active"));

            if (element) {
                element.classList.add("active");
            }

            // 2. Update Sub Header Title
            document.getElementById("active-module-title").innerText = module.title;

            // 3. Render Submenu Cards
            const submenuContainer = document.getElementById("submenu-container");
            submenuContainer.innerHTML = "";
            submenuContainer.style.display = "grid";

            module.options.forEach((option) => {
                const card = document.createElement("div");
                card.className = "submenu-card";
                card.innerHTML = `<i class="${option.icon || 'fas fa-circle-dot'}"></i><span>${option.name}</span>`;

                // Click handler to load specific PHP file in iframe
                card.onclick = function() {
                    selectSubOption(card, option.file);
                };

                submenuContainer.appendChild(card);
            });

            // Auto-select first sub-option
            if (submenuContainer.children.length > 0) {
                selectSubOption(submenuContainer.children[0], module.options[0].file);
            }
        }

        // Handle Submenu Option selection & highlight
        function selectSubOption(cardElement, fileUrl) {
            const allCards = document.querySelectorAll(".submenu-card");
            allCards.forEach((card) => card.classList.remove("active-sub"));

            if (cardElement) {
                cardElement.classList.add("active-sub");
            }

            loadIframe(fileUrl);
        }

        // Open standalone pages like Profile, Settings, or Notifications
        function openIndependentPage(fileUrl, pageTitle, navElement) {
            if (navElement) {
                const navItems = document.querySelectorAll(".sidebar-nav .nav-item, .sidebar-bottom .nav-item");
                navItems.forEach((item) => item.classList.remove("active"));
                navElement.classList.add("active");
            }

            document.getElementById("active-module-title").innerText = pageTitle;

            // Hide submenus for standalone pages
            const submenuContainer = document.getElementById("submenu-container");
            submenuContainer.innerHTML = "";
            submenuContainer.style.display = "none";

            loadIframe(fileUrl);
        }

        function loadIframe(fileUrl) {
            const iframe = document.getElementById("content-iframe");
            iframe.src = fileUrl;
        }

        function triggerAssistantClick() {
            openIndependentPage('admin_ai_assistant.php', 'Admin AI Assistant', null);
        }

        // Initial page setup load
        window.onload = () => {
            const firstNavItem = document.querySelector(".sidebar-nav .nav-item");
            loadModule("admin_mgmt", firstNavItem);
        };
    </script>
</body>

</html>