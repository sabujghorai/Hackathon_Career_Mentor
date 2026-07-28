<?php
// Start session to access session variables
session_start();

// Check if user is logged in and is a Teacher
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Career Mentor AI - Teacher Dashboard</title>

    <!-- SweetAlert2 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap");

        :root {
            /* Teacher Green Theme */
            --primary-dark: #0a4f27;
            --primary-main: #2e7d32;
            --primary-light: #e8f5e9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --sidebar-width: 240px;
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
            gap: 5px;
            width: 100%;
            padding: 10px 0;
        }

        .nav-item {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
            transition: all 0.2s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        .nav-item i {
            font-size: 18px;
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
            border-left: 4px solid #a5d6a7;
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
            background-color: #f1f8e9;
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
            background-color: #c8e6c9;
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
            border-color: #a5d6a7;
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
            text-transform: capitalize;
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
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
            border-color: var(--primary-main);
        }

        .submenu-card.active-sub {
            background-color: var(--primary-main);
            border-color: var(--primary-dark);
            box-shadow: 0 4px 10px rgba(46, 125, 50, 0.25);
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

<body class="theme-green">
    <div class="dashboard-wrapper">
        <!-- FIXED SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand" onclick="refreshPage()">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Teacher Portal</span>
            </div>

            <nav class="sidebar-nav">
                <a class="nav-item active" onclick="loadModule('roadmap_mgmt', this)">
                    <i class="fas fa-map-signs"></i>
                    <span>Roadmap</span>
                </a>
                <a class="nav-item" onclick="loadModule('study_materials', this)">
                    <i class="fas fa-folder-plus"></i>
                    <span>Study Materials</span>
                </a>
                <a class="nav-item" onclick="loadModule('interview_questions', this)">
                    <i class="fas fa-clipboard-question"></i>
                    <span>Interview Questions</span>
                </a>
                <a class="nav-item" onclick="loadModule('skill_resources', this)">
                    <i class="fas fa-laptop-code"></i>
                    <span>Skill Resources</span>
                </a>
                <a class="nav-item" onclick="loadModule('student_progress', this)">
                    <i class="fas fa-user-check"></i>
                    <span>Student Progress</span>
                </a>
                <a class="nav-item" onclick="loadModule('opp_mgmt', this)">
                    <i class="fas fa-briefcase"></i>
                    <span>Opportunities</span>
                </a>
                <a class="nav-item" onclick="loadModule('announcements', this)">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <!-- Settings loads in iframe -->
                <a class="nav-item" onclick="openIndependentPage('settings.php', 'Settings', this)">
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
                        Teacher Dashboard
                    </h1>
                </div>

                <div class="header-right">
                    <div class="search-bar">
                        <input type="text" placeholder="Search Portal..." />
                        <i class="fas fa-search"></i>
                    </div>

                    <!-- Notification Bell Button -->
                    <button class="notification-btn" onclick="toggleNotificationShutter(event)" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge" id="notif-badge">4</span>
                    </button>

                    <!-- NOTIFICATION SHUTTER PANEL OVERLAY -->
                    <div class="notification-shutter" id="notification-shutter" onclick="event.stopPropagation()">
                        <div class="shutter-header">
                            <span><i class="fas fa-bell me-1"></i> Notifications</span>
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
                    <div class="user-profile" onclick="openIndependentPage('profile.php', 'User Profile', null)" title="View Profile">
                        <img
                            src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['fullname'] ?? 'Teacher'); ?>&background=2e7d32&color=fff"
                            alt="Teacher Profile" />
                        <div class="user-info">
                            <span class="user-name">Prof. <?= htmlspecialchars($_SESSION['fullname'] ?? 'Teacher User'); ?></span>
                            <span class="user-role"><?= htmlspecialchars($_SESSION['role'] ?? 'Teacher'); ?></span>
                        </div>
                    </div>

                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="main-content">
                <div class="sub-header">
                    <h2 id="active-module-title">1. Roadmap Management</h2>
                </div>

                <!-- Dynamic Submenu Bullet Options -->
                <div class="submenu-grid" id="submenu-container">
                    <!-- Dynamic Buttons Injected via JavaScript -->
                </div>

                <!-- Embedded View Box / Frame Page -->
                <div class="view-container">
                    <iframe id="content-iframe" src="about:blank"></iframe>
                </div>

                <!-- Floating Chatbot Action Button -->
                <button
                    class="fab-chatbot"
                    onclick="triggerAssistantClick()"
                    title="Teaching Assistant">
                    <i class="fas fa-robot"></i>
                </button>
            </main>
        </div>
    </div>

    <!-- DASHBOARD SCRIPT & LOGIC -->
    <script>
        // PDF Teacher Modules, Icons, and dedicated .php Files Mapping
        const teacherData = {
            roadmap_mgmt: {
                title: "1. Roadmap Management",
                options: [{
                        name: "Add Roadmap",
                        icon: "fas fa-plus-circle",
                        file: "add_roadmap.php"
                    },
                    {
                        name: "Edit Roadmap",
                        icon: "fas fa-edit",
                        file: "edit_roadmap.php"
                    },
                    {
                        name: "Delete Roadmap",
                        icon: "fas fa-trash-alt",
                        file: "delete_roadmap.php"
                    }
                ],
            },
            study_materials: {
                title: "2. Study Materials",
                options: [{
                        name: "Upload Materials",
                        icon: "fas fa-file-upload",
                        file: "upload_materials.php"
                    },
                    {
                        name: "Update Materials",
                        icon: "fas fa-sync-alt",
                        file: "update_materials.php"
                    },
                    {
                        name: "Delete Materials",
                        icon: "fas fa-file-excel",
                        file: "delete_materials.php"
                    }
                ],
            },
            interview_questions: {
                title: "3. Interview Questions",
                options: [{
                        name: "Add HR Questions",
                        icon: "fas fa-user-tie",
                        file: "add_hr_questions.php"
                    },
                    {
                        name: "Add Technical Questions",
                        icon: "fas fa-code",
                        file: "add_technical_questions.php"
                    },
                    {
                        name: "Manage Questions",
                        icon: "fas fa-tasks",
                        file: "manage_questions.php"
                    }
                ],
            },
            skill_resources: {
                title: "4. Skill Resources",
                options: [{
                        name: "Add Skills",
                        icon: "fas fa-tools",
                        file: "add_skills.php"
                    },
                    {
                        name: "Add Learning Resources",
                        icon: "fas fa-book-medical",
                        file: "add_learning_resources.php"
                    },
                    {
                        name: "Update Resources",
                        icon: "fas fa-folder-minus",
                        file: "update_resources.php"
                    }
                ],
            },
            student_progress: {
                title: "5. Student Progress",
                options: [{
                        name: "View Student Progress",
                        icon: "fas fa-chart-line",
                        file: "view_student_progress.php"
                    },
                    {
                        name: "View Completed Tasks",
                        icon: "fas fa-tasks-alt",
                        file: "view_completed_tasks.php"
                    },
                    {
                        name: "View Performance",
                        icon: "fas fa-tachometer-alt",
                        file: "view_performance.php"
                    }
                ],
            },
            opp_mgmt: {
                title: "6. Opportunities Management",
                options: [{
                        name: "Add Internship",
                        icon: "fas fa-building-user",
                        file: "add_internship.php"
                    },
                    {
                        name: "Add Scholarship",
                        icon: "fas fa-graduation-cap",
                        file: "add_scholarship.php"
                    },
                    {
                        name: "Add Hackathon Details",
                        icon: "fas fa-laptop-code",
                        file: "add_hackathon_details.php"
                    }
                ],
            },
            announcements: {
                title: "7. Announcements",
                options: [{
                        name: "Add Notices",
                        icon: "fas fa-note-sticky",
                        file: "add_notices.php"
                    },
                    {
                        name: "Send Updates",
                        icon: "fas fa-paper-plane",
                        file: "send_updates.php"
                    }
                ],
            },
        };

        // Top Latest Notifications Array Object
        const notificationsData = [{
                id: 1,
                title: "Student Submitted Assignment",
                desc: "Alex Morgan submitted Technical Assessment Quiz.",
                time: "15 mins ago",
                unread: true
            },
            {
                id: 2,
                title: "New Roadmap Approval",
                desc: "Full Stack Roadmap requested review by Admin.",
                time: "2 hours ago",
                unread: true
            },
            {
                id: 3,
                title: "Study Material Uploaded",
                desc: "Data Structures PDF updated successfully.",
                time: "5 hours ago",
                unread: true
            },
            {
                id: 4,
                title: "Weekly Performance Report",
                desc: "85% students completed weekly assigned tasks.",
                time: "1 day ago",
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
                text: 'Are you sure you want to end your teacher session?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2e7d32',
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
                body.innerHTML = `<div style="padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;">No new notifications</div>`;
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

        // Load module and generate sub-option buttons with icons
        function loadModule(moduleKey, element) {
            const module = teacherData[moduleKey];
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
            openIndependentPage('teacher_ai_assistant.php', 'Teaching AI Assistant', null);
        }

        // Initial page setup load
        window.onload = () => {
            const firstNavItem = document.querySelector(".sidebar-nav .nav-item");
            loadModule("roadmap_mgmt", firstNavItem);
        };
    </script>
</body>

</html>