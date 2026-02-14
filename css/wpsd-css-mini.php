<?php
include_once('css-base.php');
?>

/* ===================================
   Mobile / Mini CSS - Minimal Layout 
   =================================== */

/* Base Resets */
body {
    font-size: 14px;
    font-family: 'Source Sans Pro', sans-serif;
    color: <?php echo $textContent; ?>;
    background: <?php echo $backgroundPage; ?>;
    margin: 0;
    padding: 0;
    -webkit-text-size-adjust: none;
}

a { text-decoration: none; color: <?php echo $textLinks; ?>; }
a:hover { color: <?php echo $textBanners; ?>; }

/* 1. LAYOUT CONTAINERS */

.container {
    width: 100%;
    margin: 0;
    padding: 0;
    text-align: center;
}

.header {
    background: <?php echo $backgroundBanners; ?>;
    color: <?php echo $textBanners; ?>;
    padding: 10px 0;
    text-align: center;
}

.header h1 {
    font-size: 1.2em;
    font-weight: 600;
    margin: 0;
    padding: 0;
}

/* Header Elements to Hide on Mobile */
.headerClock,
.SmallHeader {
    display: none !important;
}

/* 2. NAVIGATION BAR - RESTRICTED ITEMS */

.navbar {
    background-color: <?php echo $backgroundNavbar; ?>;
    padding: 5px;
    text-align: center;
    overflow: hidden;
}

/* Hide ALL navbar links by default */
.navbar a {
    display: none !important; 
}

/* Show ONLY Profiles and Live Caller */
.navbar a.menuprofile,
.navbar a.menulive {
    display: inline-block !important;
    float: none;
    padding: 8px 15px;
    margin: 0 5px;
    font-size: 16px;
    font-weight: bold;
    color: <?php echo $textNavbar; ?>;
    background: rgba(0,0,0,0.2);
    border-radius: 4px;
}

/* 3. SIDEBAR - HIDDEN */

.nav, 
.sidebar-toggle,
#repeaterInfo {
    display: none !important;
}

/* 4. CONTENT AREA */

.content {
    margin: 0 !important;
    padding: 5px;
    width: 100%;
    box-sizing: border-box;
    background: transparent;
    float: none;
}

.contentwide {
    padding: 5px;
    margin: 0;
    text-align: center;
}

/* 5. MODEM STATUS (Radio Info) & SYSTEM INFO */

#hwInfo {
    display: none !important;
}

/* Styles for the "Card" layout used in Radio Info */
.dashboard-header-stats {
    display: flex;
    justify-content: center;
    margin-bottom: 10px;
    padding: 0;
}

/* HIDE all cards except the first one (Modem Status) */
.dashboard-header-stats .stat-card:not(:first-child) {
    display: none !important;
}

/* Style the SINGLE remaining card (Modem Status) */
.stat-card {
    flex: 0 0 90%;
    max-width: 400px;
    background: <?php echo $tableRowEvenBg; ?>;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    border-radius: 4px;
    padding: 10px 5px;
    text-align: center;
    box-sizing: border-box;
    margin: 0 auto 5px auto;
}

.stat-label {
    display: block;
    font-size: 0.8em;
    text-transform: uppercase;
    opacity: 0.9;
    margin-bottom: 5px;
    color: <?php echo $textBanners; ?>;
    background: <?php echo $backgroundBanners; ?>;
    margin-top: -10px;
    padding: 4px 0;
    border-radius: 3px 3px 0 0;
    font-weight: bold;
}

.stat-value {
    font-size: 1.4em;
    font-weight: 700;
    color: <?php echo $textContent; ?>;
    padding: 5px 0;
}

/* Badges */
.badge-tx { background-color: #d11141; color: white; padding: 4px 10px; border-radius: 4px; display: inline-block; }
.badge-rx { background-color: #2ecc71; color: white; padding: 4px 10px; border-radius: 4px; display: inline-block; }
.badge-wait { background-color: #ffc425; color: black; padding: 4px 10px; border-radius: 4px; display: inline-block; }

/* 6. TABLES (Gateway & Local RF) */

/* Hide Current/Last Caller Details on Mobile */
#liveCallerDeets {
    display: none !important;
}

.table-header-bar {
    margin-top: 15px;
    margin-bottom: 5px;
    text-align: left;
    padding-left: 5px;
}

.table-title {
    font-size: 1.2em;
    font-weight: bold;
    color: <?php echo $textContent; ?>;
}

/* Hide table controls */
.table-actions {
    display: none !important;
}

.modern-table-container {
    background: <?php echo $backgroundContent; ?>;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    border-radius: 4px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 20px;
}

table.modern-table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;
}

table.modern-table th {
    background: <?php echo $backgroundBanners; ?>;
    color: <?php echo $textBanners; ?>;
    font-weight: bold;
    padding: 8px 5px;
    text-align: left;
    font-size: 0.9em;
    white-space: nowrap;
}

/* Hide Tooltip Content in Headers & Cells (redundant text) */
table.modern-table th a.tooltip span,
table.modern-table td a.tooltip span {
    display: none !important;
}

/* Fix super wide "target" column */
table.modern-table th[width="30%"] {
    width: 10%;
}

/* Force Left Alignment for data cells to match Headers */
table.modern-table td {
    padding: 8px 5px;
    border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
    font-size: 2vw;
    vertical-align: middle;
    text-align: left; /* Overrides .container center alignment */
}

/* Specific Centered Columns (overrides the left above) */
.text-center {
    text-align: center !important;
}

table.modern-table tr:nth-child(even) { background: <?php echo $tableRowEvenBg; ?>; }
table.modern-table tr:nth-child(odd) { background: <?php echo $tableRowOddBg; ?>; }

/* 7. UTILITIES & HIDDEN ELEMENTS */

.noMob { display: none !important; }
.footer { font-size: 0.8em; padding: 20px; color: #aaa; }
