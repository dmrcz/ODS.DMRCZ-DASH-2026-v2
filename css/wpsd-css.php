<?php
include_once('css-base.php');
?>

.container {
    width: 100%;
    text-align: left;
    margin: auto;
    background : <?php echo $backgroundContent; ?>;
}

body, font {
    font: <?php echo $bodyFontSize; ?>px 'Source Sans Pro', sans-serif;
    color: #ffffff;
    -webkit-text-size-adjust: none;
    -moz-text-size-adjust: none;
    -ms-text-size-adjust: none;
    text-size-adjust: none;
}

.center {
    text-align: center !important;
}

.middle {
    vertical-align: middle;
}

.header {
    background : <?php echo $backgroundBanners; ?>;
    text-decoration : none;
    color : <?php echo $textBanners; ?>;
    font-family : 'Source Sans Pro', sans-serif;
    text-align : left;
    padding : 5px 0 0 0;
    margin: 0 10px;
}

.header h1 {
    margin-top:-10px;
    font-size: <?php echo $headerFontSize; ?>px;
}

.headerClock {
    font-size: 0.9em;
    text-align: left;
    padding-left: 8px;
    padding-top: 5px;
    float: left;
}

.nav {
    float: left;
    margin : -12px 0 0 0;
    padding : 0 3px 3px 10px;
    width : 230px;
    background : <?php echo $backgroundNavPanel; ?>;
    font-weight : normal;
    min-height : 100%;
}

.content {
    margin : 0 0 0 250px;
    padding : 0 10px 5px 3px;
    color : <?php echo $textSections; ?>;
    background : <?php echo $backgroundContent; ?>;
    text-align: center;
}

.contentwide {
    padding: 10px;
    color: <?php echo $textSections; ?>;
    background: <?php echo $backgroundContent; ?>;
    text-align: center;
    margin: 5px 0 10px;
}

.contentwide h2 {
    color: <?php echo $textSections; ?>;
    font: 1em 'Source Sans Pro', sans-serif;
    text-align: center;
    font-weight: bold;
    padding: 0px;
    margin: 0px;
}

.divTableCellSans h2 {
    color: <?php echo $textContent; ?>;
}

.divTableCellMono {
    font: 1.3em 'Inconsolata', monospace !important;
}

td.divTableCellMono a:hover {
    text-decoration: underline !important;
}

h2.ConfSec {
    font-size: 1.6em;
    text-align: left;
    padding-bottom: 1rem;
}

h3.ConfSec {
    font-size: 1.4em;
    text-align: left;
}


.left {
    text-align: left;
}

.footer {
    background : <?php echo $backgroundBanners; ?>;
    text-decoration : none;
    color : <?php echo $textBanners; ?>;
    font-family : 'Source Sans Pro', sans-serif;
    font-size : .9rem;
    text-align : center;
    padding : 10px 0 10px 0;
    clear : both;
    margin: 10px;
}

.footer a {
    text-decoration: underline !important;
    color : <?php echo $textBanners; ?> !important;
}

tt, code, kbd, pre {
        font-family: 'Inconsolata', monospace !important;
}

.mono {
    font: <?php echo $mainFontSize; ?>px 'Inconsolata', monospace !important;
}

.SmallHeader {
    font-family: 'Inconsolata', monospace !important;
    font-size: 12px;
}
.shRight {
    text-align: right;
    padding-right: 8px;
}
.shLeft {
    text-align: left;
    padding-left: 8px;
    float: left;
}

#tail {
    font-family: 'Inconsolata', monospace;
    height: 640px;
    overflow-y: scroll;
    overflow-x: scroll;
    color: #4DEEEA;
    background: #000000;
    font-size: 18px;
    padding: 1em;
    text-align: left;
    scrollbar-width: none;  /* Firefox */
    -ms-overflow-style: none;  /* IE and Edge */
}

/* For Webkit browsers like Chrome/Safari */
#tail::-webkit-scrollbar {
    display: none;
}

table {
    vertical-align: middle;
    text-align: center;
    empty-cells: show;
    padding: 0px;
    border-collapse:collapse;
    border-spacing: 5px;
    border: .5px solid <?php echo $tableBorderColor; ?>;
    text-decoration: none;
    background: #000000;
    font-family: 'Source Sans Pro', sans-serif;
    width: 100%;
    white-space: nowrap;
}

table th {
    font-family:  'Source Sans Pro', sans-serif;
    border: .5px solid <?php echo $tableBorderColor; ?>;
    font-weight: 600;
    text-decoration: none;
    color : <?php echo $textBanners; ?>;
    background: <?php echo $backgroundBanners; ?>;
    padding: 5px;
}

table tr:nth-child(even) {
    background: <?php echo $tableRowEvenBg; ?>;
}

table tr:nth-child(odd) {
    background: <?php echo $tableRowOddBg; ?>;
}

table td {
    color: <?php echo $textContent; ?>;
    text-decoration: none;
    border: .5px solid <?php echo $tableBorderColor; ?>;
    padding: 5px;
    font-size: <?php echo "$mainFontSize"; ?>px;
}

#ccsConns table td, #activeLinks table td, #starNetGrps table td, #infotable td, table.poc-lh-table td {
    color: <?php echo $textContent; ?>;
    font-family: 'Inconsolata', monospace;
    font-weight: 500;
    text-decoration: none;
    border: .5px solid <?php echo $tableBorderColor; ?>;
    padding: 5px;
    font-size: <?php echo "$mainFontSize"; ?>px;
}

#liveCallerDeets table tr:hover td, #localTxs table tr:hover td, #lastHeard table tr:hover td, #bmLinks table tr:hover td,
#liveCallerDeets table tr:hover td a, #localTxs table tr:hover td a, #lastHeard table tr:hover td a, #bmLinks table tr:hover td a {
     background-color: <?php echo $backgroundDropdownHover; ?>;
     color: <?php echo $textDropdownHover; ?>;
}

.divTable{
    font-family:  'Source Sans Pro', sans-serif;
    display: table;
    border-collapse: collapse;
    width: 100%;
}

.divTableRow {
    display: table-row;
    width: auto;
    clear: both;
}

.divTableHead, .divTableHeadCell {
    color : <?php echo $textBanners; ?>;
    background: <?php echo $backgroundBanners; ?>;
    border: .5px solid <?php echo $tableBorderColor; ?>;
    font-weight: 600;
    text-decoration: none;
    padding: 5px;
    caption-side: top;
    display: table-caption;
    text-align: center;
    vertical-align: middle;
}

.divTableCellSans {
    font-size: <?php echo "$bodyFontSize"; ?>px;
    color: <?php echo $textContent; ?>;
}

.divTableCell {
    font-size: <?php echo "$bodyFontSize"; ?>px;
    border: .5px solid <?php echo $tableBorderColor; ?>;
    color: <?php echo $textContent; ?>;
}

.divTableCell, .divTableHeadCell {
    display: table-cell;
}

.divTableBody {
    display: table-row-group;
}

.divTableBody .divTableRow {
    background: <?php echo $tableRowEvenBg; ?>;
}

.divTableCell.cell_content {
    padding: 5px;
}

.info {
    background: <?php echo $tableRowEvenBg; ?>;
    font-size: <?php echo "$bodyFontSize"; ?>px;
    border: .5px solid <?php echo $tableBorderColor; ?>;
    color: <?php echo $textContent; ?>;
    padding: 10px;
}

.full-width-hint {
    text-align: left;
    margin: 8px 20px;
}

.inline-switch {
    display: inline-block;
    vertical-align: middle;
}

h2.page-header {
    text-align: left;
    margin: 10px 0;
}

h3.section-header {
    text-align: left;
    margin: 15px 0 10px 0;
}

.admin-table td, .admin-table th {
    padding: 4px 8px;
}

.admin-table input[type=button],
.admin-table input[type=submit] {
    padding: 4px 8px;
    margin: 2px;
}

.loader {
    width: 16px;
    height: 16px;
    margin: 4px;
    display: inline-block;
    vertical-align: middle;
    border: 3px solid transparent;
    border-top: 3px solid <?php echo $textContent; ?>;;
    border-bottom: 3px solid <?php echo $textContent; ?>;;
    border-radius: 50%;
    animation: loader-spin 0.8s linear infinite;
}

@keyframes loader-spin {
    0%   { transform: rotate(0deg); }
    50%  { transform: rotate(180deg); }
    100% { transform: rotate(360deg); }
}

body {
    background: <?php echo $backgroundPage; ?>;
    color: <?php echo $textContent; ?>;
}

a {
    text-decoration:none;

}

a:link, a:visited {
    text-decoration: none;
    color: <?php echo $textLinks; ?>
}

a.tooltip, a.tooltip:link, a.tooltip:visited, a.tooltip:active  {
    text-decoration: none;
    position: relative;
    color: <?php echo $textBanners; ?>;
}

a.tooltip:hover {
    text-decoration: none;
    background: transparent;
    color: <?php echo $textBanners; ?>;
    z-index: 6000;
}

a.tooltip span {
    text-decoration: none;
    display: none;
    font-size: <?php echo "$bodyFontSize"; ?>px;
    font-family:  'Source Sans Pro', sans-serif;
}

a.tooltip:hover span {
    font-size: <?php echo "$bodyFontSize"; ?>px;
    font-family:  'Source Sans Pro', sans-serif;
    text-decoration: none;
    display: block;
    position: absolute;
    top: 20px;
    left: 0;
    z-index: 6000;
    text-align: left;
    white-space: nowrap;
    border: none;
    color: #e9e9e9;
    background: rgba(0, 0, 0, .9);
    padding: 8px;
}

th:last-child a.tooltip:hover span {
    left: auto;
    right: 0;
}

a.tooltip span b {
    text-decoration: none;
    display: block;
    margin: 0;
    font-weight: bold;
    border: none;
    color: #e9e9e9;
    padding: 0px;
}

a.tooltip2, a.tooltip2:link, a.tooltip2:visited, a.tooltip2:active  {
    text-decoration: none;
    position: relative;
    font-weight: bold;
    color: <?php echo $textContent; ?>;
}

a.tooltip2:hover {
    text-decoration: none;
    background: transparent;
    color: <?php echo $textContent; ?>;
	z-index: 6000;
}

a.tooltip2 span {
    text-decoration: none;
    display: none;
}

a.tooltip2:hover span {
    text-decoration: none;
    display: block;
    position: absolute;
    top: 20px;
    left: 0;
    width: 202px;
    z-index: 6000;
    font: 16px 'Source Sans Pro', sans-serif;
    text-align: left;
    white-space: normal;
    border: none;
    color: #e9e9e9;
    background: rgba(0, 0, 0, .9);
    padding: 8px;
}

a.tooltip2 span b {
    text-decoration: none;
    font: 16px 'Source Sans Pro', sans-serif;
    display: block;
    margin: 0;
    font-weight: bold;
    border: none;
    color: #e9e9e9;
    padding: 0px;
}

ul {
    padding: 5px;
    margin: 10px 0;
    list-style: none;
    float: left;
}

ul li {
    float: left;
    display: inline; /*For ignore double margin in IE6*/
    margin: 0 10px;
}

ul li a {
    text-decoration: none;
    float:left;
    color: #999;
    cursor: pointer;
    font: 600 14px/22px 'Source Sans Pro', sans-serif;
}

ul li a span {
    margin: 0 10px 0 -10px;
    padding: 1px 8px 5px 18px;
    position: relative; /*To fix IE6 problem (not displaying)*/
    float:left;
}

ul.mmenu li a.current, ul.mmenu li a:hover {
    color: #0d5f83;
}

ul.mmenu li a.current span, ul.mmenu li a:hover span {
    color: #0d5f83;
}

h1 {
    text-align: center;
    font-weight: 600;
}

/* -------------------------------------------------------
   Modern Toggle Switches (Global) - Smaller Size
   ------------------------------------------------------- */

.toggle {
    position: absolute;
    margin-left: -9999px;
    z-index: 0;
}

.toggle + label {
    display: inline-block;
    position: relative;
    cursor: pointer;
    outline: none;
    user-select: none;
}

/* Modern Round Flat Toggle (Capsule Style) */
input.toggle-round-flat + label {
    padding: 0;
    margin: 2px;
    width: 40px;       /* Reduced from 50px */
    height: 20px;      /* Reduced from 26px */
    background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    border-radius: 20px; /* Adjusted radius */
    transition: background 0.4s, border-color 0.4s;
    vertical-align: middle;
}

/* Remove old legacy styles */
input.toggle-round-flat + label:before {
    display: none;
}

/* The Knob */
input.toggle-round-flat + label:after {
    display: block;
    position: absolute;
    content: "";
    top: 2px;          /* Reduced from 3px */
    left: 3px;         /* Reduced from 4px */
    width: 14px;       /* Reduced from 18px */
    height: 14px;      /* Reduced from 18px */
    background-color: #ffffff;
    border-radius: 50%;
    transition: transform 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

/* Active State */
input.toggle-round-flat:checked + label {
    background-color: #2ecc71; /* Modern Green */
    border-color: #27ae60;
}

/* Move Knob */
input.toggle-round-flat:checked + label:after {
    transform: translateX(20px); /* Reduced travel from 24px */
}

/* Focus State */
input.toggle-round-flat:focus + label {
    box-shadow: 0 0 4px #2ecc71;
}

/* Disabled State */
input.toggle-round-flat:disabled + label {
    opacity: 0.5;
    cursor: not-allowed;
    filter: grayscale(100%);
}

textarea, input[type='text'], input[type='password'] {
        font-size: <?php echo $bodyFontSize; ?>px;
        font-family: 'Inconsolata', monospace;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        padding: 5px;
        margin 3px;
        background: #e2e2e2;
}

textarea.fulledit {
    display: inline-block;
    margin: 0;
    padding: .2em;
    width: auto;
    min-width: 70%;
    max-width: 100%;
    height: auto;
    min-height: 600px;
    cursor: text;
    overflow: auto;
    resize: both;
}

input[type=button], input[type=submit], input[type=reset], input[type=radio], button {
    font-size: <?php echo $bodyFontSize; ?>px;
    font-family: 'Source Sans Pro', sans-serif;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    padding: 5px;
    text-decoration: none;
    margin: 3px;
    cursor: pointer;
    background: <?php echo $backgroundNavbar ?>;
    color: <?php echo $textNavbar ?>;
}

input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover, button:hover {
    color: <?php echo $textNavbarHover; ?>;
    background-color: <?php echo $backgroundNavbarHover; ?>;
}

input:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

button:disabled {
    cursor: not-allowed;
    color: <?php echo $textModeCellDisabledColor; ?>;
    background: <?php echo $backgroundModeCellDisabledColor; ?>;
}

input:disabled + label {
    color: #000;
    opacity: 0.6;
    cursor: not-allowed;
}

select {
    background: #e2e2e2;
    font-family: 'Inconsolata', monospace;
    font-size: <?php echo $bodyFontSize; ?>px;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    color: black;
    padding: 5px;
    text-decoration: none;
}

.select2-selection__rendered {
  font-family: 'Inconsolata', monospace;
  color: black !important;
  font-size: <?php echo $bodyFontSize; ?>px !important;
  background: #e2e2e2;
}

.select2-results__options{
  color: black;
  font-size:<?php echo $bodyFontSize; ?>px !important;
  font-family: 'Inconsolata', monospace;
  background: #e2e2e2;
}

[class^='select2'] {
  border-radius: 0px !important;
}

.select2-results__option {
  color: black !important;
}

.select2-results__option--selectable {
    min-width: 75%;
}

.navbar {
    overflow: hidden;
    background-color: <?php echo $backgroundNavbar; ?>;
    padding: 10px 10px 10px  2px;
}

.navbar a {
    float: right;
    font-family : 'Source Sans Pro', sans-serif;
    font-size: <?php echo $bodyFontSize; ?>px;
    color: <?php echo $textNavbar; ?>;
    text-align: center;
    padding: 5px 8px;
    text-decoration: none;
}

.dropdown .dropbutton {
    font-size: <?php echo $bodyFontSize; ?>px;
    border: none;
    outline: none;
    color: <?php echo $textNavbar; ?>;
    padding: 5px 8px;
    background-color: <?php echo $backgroundNavbar; ?>;
    font-family: inherit;
    margin: 0;
}

.navbar a:hover, .dropdown:hover .dropbutton {
    color: <?php echo $textNavbarHover; ?>;
    background-color: <?php echo $backgroundNavbarHover; ?>;
}

.lnavbar {
    overflow: hidden;
    background-color: <?php echo $backgroundNavbar; ?>;
    padding-bottom: 10px;
    margin-top: -0.6rem;
}

/* Advanced menus */
.mainnav {
    display: inline-block;
    list-style: none;
    padding: 0;
    margin: 0 auto;
    width: 100%;
    background: <?php echo $backgroundNavbar; ?>;
    overflow: hidden;
}

.dropdown {
    position: absolute;
    top: 134px;
    width: 270px;
    opacity: 0;
    visibility: hidden;
}

.mainnav ul {
    padding: 0;
    list-style: none;
}

.mainnav li {
    display: block;
    float: left;
    font-size: 0;
    margin: 0;
    background: <?php echo $backgroundNavbar; ?>;
}

.mainnav li a {
    list-style: none;
    padding: 0;
    display: inline-block;
    padding: 1px 10px;
    font-family : 'Source Sans Pro', sans-serif;
    font-size: <?php echo $bodyFontSize; ?>px;
    color: <?php echo $textNavbar; ?>;
    text-align: center;
    text-decoration: none;
}

.mainnav .has-subs a:after {
    content: "\f0d7";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-left: 1em;
}

.mainnav .has-subs .dropdown .subs a:after {
    content: "";
}

.mainnav li:hover {
    background: <?php echo $backgroundNavbarHover; ?>;
}

.mainnav li:hover a {
    color: <?php echo $textNavbarHover; ?>;
    background-color: <?php echo $backgroundNavbarHover; ?>;
}

/* First Level */
.subs {
    position: relative;
    width: 270px;
}

.has-subs:hover .dropdown,
.has-subs .has-subs:hover .dropdown {
    opacity: 1;
    visibility: visible;
}

.mainnav ul li,
.mainav ul li ul li  a {
    color: <?php echo $textDropdown; ?>;
    background-color: <?php echo $backgroundDropdown; ?>;
}

.mainnav li:hover ul a,
.mainnav li:hover ul li ul li a {
    color: <?php echo $textDropdown; ?>;
    background-color: <?php echo $backgroundDropdown; ?>;
}

.mainnav li ul li:hover,
.mainnav li ul li ul li:hover {
    background-color: <?php echo $backgroundDropdownHover; ?>;
}

.mainnav li ul li:hover a,
.mainnav li ul li ul li:hover a {
    color: <?php echo $textDropdownHover; ?>;
    background-color: <?php echo $backgroundDropdownHover; ?>;
}

.mainnav .has-subs .dropdown .has-subs a:after {
    content: "\f0da";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    position: absolute;
    top: 1px;
    right: 9px;
}

/* Second Level */
.has-subs .has-subs .dropdown .subs {
    position: relative;
    top: -144px;
    width: 270px;
    border-style: none none none solid;
    border-width: 1px;
    border-color: <?php echo $backgroundDropdownHover; ?>;
}

.has-subs .has-subs .dropdown .subs a:after {
    content:"";
}

.has-subs .has-subs .dropdown {
    position: absolute;
    width: 270px;
    left: 270px;
    opacity: 0;
    visibility: hidden;
}

.menuhwinfo, .menuprofile, .menuconfig, .menuadmin, .menudashboard, .menusimple,
.menucaller, .menulive, .menuupdate, .menupower, .menulogs,
.menubackup, .menuadvanced, .menureset, .menusysinfo, .menuradioinfo,
.menuappearance, .menuwifi {
    position: relative;
}

.menuprofile:before {
    content: "\f0c0";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menuappearance:before {
    content: "\f1fc";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menucastmemory:before {
    content: "\f0cb";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menuradioinfo:before {
    content: "\f012";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menuconfig:before {
    content: "\f1de";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menuadmin:before {
    content: "\f023";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menuupdate:before {
    content: "\f0ed";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menupower:before {
    content: "\f011";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menulogs:before {
    content: "\f06e";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menudashboard:before {
    content: "\f0e4";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menusimple:before {
    content: "\f0ce";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menulive:before {
    content: "\f2a0";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menucaller:before {
    content: "\f098";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menuwifi:before {
    content: "\f1eb";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.grid-item.filter-activity:before {
    content: "\f131";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

tr.good-activity.even {
  background: <?php echo $tableRowEvenBg; ?>;
}
tr.good-activity.odd {
  background: <?php echo $tableRowOddBg; ?>;
}

input.filter-activity-max {
  background-color: <?php echo $tableRowEvenBg; ?>;
  color: <?php echo $textContent; ?>;
  border: 2px solid <?php echo $backgroundContent; ?>;
  border-radius: 5px;
  height: 22px;
  position: relative;
  padding: 0 3px;
}

.filter-activity-max-wrap .ms {
  position: absolute;
  top: 1px;
  right: 23px;
}

.filter-activity-max-wrap {
  display: inline-block;
  position: relative;
  width: 60px;
}

.menutgnames:before {
    content: "\f0e6";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menuhwinfo:before {
    content: "\f03a";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menubackup:before {
    content: "\f187";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menuadvanced:before {
    content: "\f013";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.menureset:before {
    content: "\f1cd";
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    padding-right: 0.2em;
}

.disabled-service-cell {
    color: <?php echo $textModeCellDisabledColor; ?>;
    background: <?php echo $backgroundModeCellDisabledColor; ?>;
}

.active-service-cell {
    color: <?php echo $textServiceCellActiveColor; ?>;
    background: <?php echo $backgroundServiceCellActiveColor; ?>;
}

.inactive-service-cell {
    color: <?php echo $textServiceCellInactiveColor; ?>;
    background: <?php echo $backgroundServiceCellInactiveColor; ?>;
}

.disabled-mode-cell {
    color: <?php echo $textModeCellDisabledColor; ?>;
    padding:2px;
    text-align: center;
    border:0;
    background: <?php echo $backgroundModeCellDisabledColor; ?>;
}

.active-mode-cell {
    color: <?php echo $textModeCellActiveColor; ?>;
    border:0;
    text-align: center;
    padding:2px;
    background: <?php echo $backgroundModeCellActiveColor; ?>;
}

.inactive-mode-cell {
    color: <?php echo $textModeCellInactiveColor; ?>;
    border:0;
    text-align: center;
    padding:2px;
    background: <?php echo $backgroundModeCellInactiveColor; ?>;
}

.paused-mode-cell {
    color: <?php echo $textModeCellActiveColor; ?>;
    border:0;
    text-align: center;
    padding:2px;
    background: <?php echo $backgroundModeCellPausedColor; ?>;
}

.paused-mode-span {
    background: <?php echo $backgroundModeCellPausedColor; ?>;
}

.error-state-cell {
    color: <?php echo $textModeCellInactiveColor; ?>;
    text-align: center;
    border:0;
    background: <?php echo $backgroundModeCellInactiveColor; ?>;
}

.table-container {
    position: relative;
}

.config_head {
    font-size: 1.5em;
    font-weight: normal;
    text-align: left;
}

/* Tame Firefox Buttons */
@-moz-document url-prefix() {
    select,
    input {
        margin : 0;
        padding : 0;
        border-width : 1px;
        font : 14px 'Inconsolata', monospace;
    }
    input[type="button"], button, input[type="submit"] {
        padding : 0px 3px 0px 3px;
        border-radius : 3px 3px 3px 3px;
        -moz-border-radius : 3px 3px 3px 3px;
    }
}

hr {
  display: block;
  height: 1px;
  border: 0;
  border-top: 1px solid <?php echo $tableBorderColor; ?>;
  margin: 1em 0;
  padding: 0;
}

.status-grid {
  display: grid;
  grid-template-columns: auto auto auto auto auto auto;
  grid-template-rows: auto auto auto auto auto;
  margin:0;
  padding:0;
}


.status-grid .grid-item {
  padding: 1px;
  border: .5px solid <?php echo $tableBorderColor; ?>;
  text-align: center;
}

@-webkit-keyframes Pulse {
  from {
    opacity: 0;
  }

  50% {
    opacity: 1;
  }

  to {
    opacity: 0;
  }
}

@keyframes Pulse {
  from {
    opacity: 0;
  }

  50% {
    opacity: 1;
  }

  to {
    opacity: 0;
  }
}

td.lookatme {
  display: table-cell;
}

a.lookatme {
  color: steelblue;
  opacity: 1;
  position: relative;
  display: inline-block;
  font-weight:bold;
  font-size:10px;
  padding:1px;
  margin: 0 0 0 1px;
}

/* this pseudo element will be faded in and out in front /*
/* of the lookatme element to create an efficient animation. */
.lookatme:after {
  color: white;
  text-shadow: 0 0 5px #e33100;
  /* in the html, the lookatme-text attribute must */
  /* contain the same text as the .lookatme element */
  content: attr(lookatme-text);
  padding: inherit;
  position: absolute;
  inset: 0 0 0 0;
  z-index: 1;
  /* 20 steps / 2 seconds = 10fps */
  -webkit-animation: 2s infinite Pulse steps(20);
  animation: 2s infinite Pulse steps(20);
}

#hwInfoTable {
  margin-top: -2px;
}

/* indicators */

.red_dot {
    height: 15px;
    width: 15px;
    background-color: red;
    border-radius: 50%;
    display: inline-block;
}

.green_dot {
    height: 15px;
    width: 15px;
    background-color: limegreen;
    border-radius: 50%;
    display: inline-block;
}

/* RSSI meters */
meter {
  --background: #999;
  --optimum: limegreen;
  --sub-optimum: orange;
  --sub-sub-optimum: crimson;
  border-radius: 3px;
}

/* The gray background in Chrome, etc. */
meter::-webkit-meter-bar {
  background: var(--background);
  border-radius: 3px;
  height: 10px;
}

/* The green (optimum) bar in Firefox */
meter:-moz-meter-optimum::-moz-meter-bar {
  background: var(--optimum);
}

/* The green (optimum) bar in Chrome etc. */
meter::-webkit-meter-optimum-value {
  background: var(--optimum);
}

/* The yellow (sub-optimum) bar in Firefox */
meter:-moz-meter-sub-optimum::-moz-meter-bar {
  background: var(--sub-optimum);
}

/* The yellow (sub-optimum) bar in Chrome etc. */
meter::-webkit-meter-suboptimum-value {
  background: var(--sub-optimum);
}

/* The red (even less good) bar in Firefox */
meter:-moz-meter-sub-sub-optimum::-moz-meter-bar {
  background: var(--sub-sub-optimum);
}

/* The red (even less good) bar in Chrome etc. */
meter::-webkit-meter-even-less-good-value {
  background: var(--sub-sub-optimum);
}

.aprs-preview-container {
    display: flex;
    align-items: center;
    text-align: center;
    margin-top: 10px;
    margin-bottom: 10px;
}

.aprs-preview-text {
    margin: 0 10px 0 5px;
}

.aprs-symbol-preview {
    /* add'l/ any futureg styles for the symbol preview? */
}

/* Spinner animation for config pagei */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.spinner {
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top: 4px solid #666666;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
  display: inline-block;
  margin-left: 8px;
}

/* Config page unsaved changes alert stuff */
#unsavedChanges {
  display: none;
  position: fixed;
  top: 20px; /* Add top margin */
  left: 50%;
  transform: translateX(-50%); /* Center the div horizontally */
  width: calc(100% - 40px);
  height: 80px;
  overflow: hidden;
  background-color: #000;
  color: #fff;
  padding: 34px 10px 0px 10px;
  text-align: center;
  z-index: 1000;
  font-size: 1.4rem;
  border: 1px solid #fff;
  border-radius: 10px;
  max-width: 95%;
}

#applyButton {
  background-color: #37803A;
  border: 2px solid #73A675;
  margin-left: 10px;
  color: #fff;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
  transition: color 0.3s;
  font-weight: bold;
}

#applyButton:hover {
  background-color: #4caf50;
  border: 2px solid #37803A;
  color: black;
}

#revertButton {
  background-color: #e65100;
  border: 2px solid #ffab40;
  margin-left: 10px;
  color: #fff;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
  transition: color 0.3s;
  font-weight: bold;
}

#revertButton:hover {
  background-color: #ff9800;
  border: 2px solid #e65100;
  color: black;
}

.smaller {
    font-size: smaller;
}

.larger {
    font-size: 1.5rem;
}

table td.sans {
    font-family: 'Source Sans Pro', sans-serif !important;
}

div.network, div.wifiinfo, div.intinfo, div.infoheader {
    border: none !important;
}

/* -------------------------------------------------------
   Modern Dashboard Elements (Cards & Pills)
   ------------------------------------------------------- */

/* Top Area Container */
.dashboard-header-stats {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 10px;
    padding: 0;
}

/* Individual Stat Card */
.stat-card {
    flex: 1;
    min-width: 120px;
    background: <?php echo $tableRowEvenBg; ?>; /* Uses theme color */
    border: 1px solid <?php echo $tableBorderColor; ?>;
    border-radius: 4px;
    padding: 10px 5px;
    text-align: center;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

/* Label (Top of card) */
.stat-label {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    opacity: 0.8;
    margin-bottom: 4px;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: <?php echo $textBanners; ?>; /* Uses theme header text color for contrast labels */
    background: <?php echo $backgroundBanners; ?>;
    width: 100%;
    margin-top: -10px;
    padding: 3px;
    border-radius: 3px;
    margin-bottom: 8px;
}

/* Value (Bottom of card) */
.stat-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: <?php echo $textContent; ?>;
    width: 100%;
    word-break: break-word;
}

.stat-value a {
    color: <?php echo $textContent; ?> !important;
    text-decoration: none;
}

/* Sidebar Section Headers */
.sidebar-section-title {
    font-family: 'Source Sans Pro', sans-serif;
    color: <?php echo $textBanners; ?>;
    background: <?php echo $backgroundBanners; ?>;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    padding: 6px;
    font-weight: 600;
    text-align: center;
    border-radius: 4px;
    margin-top: 15px;
    margin-bottom: 3px; /* Reduced to tighten spacing */
    font-size: 0.95rem;
}

/* Sub-Sections (Related) Header Style */
.sidebar-section-title.related {
    margin-top: 5px; /* Tighter spacing for related groups */
}

/* Sidebar Grid */
.sidebar-status-grid {
    display: grid;
    grid-template-columns: 1fr 1fr; /* Two columns for pills */
    gap: 6px;
}

/* Status Pills */
.status-pill {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 8px;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
    color: <?php echo $textContent; ?>;
    background: <?php echo $tableRowEvenBg; ?>;
    transition: all 0.2s ease;
    cursor: default;
}

.status-pill.active {
    border-left: 4px solid #2ecc71; /* Green */
}

.status-pill.inactive {
    border-left: 4px solid #95a5a6; /* Grey */
    opacity: 0.8;
}

.status-pill.paused {
    border-left: 4px solid #f1c40f; /* Yellow */
}

.status-pill.error {
    border-left: 4px solid #e74c3c; /* Red */
}

/* Icons inside pills */
.status-pill i {
    margin-left: 5px;
}
.status-pill .fa-check-circle { color: #2ecc71; }
.status-pill .fa-times-circle { color: #e74c3c; }
.status-pill .fa-pause-circle { color: #f1c40f; }
.status-pill .fa-circle-o { color: #95a5a6; }

/* Status Badges for Radio Info (TX/RX) */
.badge-tx {
    background-color: #d11141;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    display: inline-block;
    animation: Pulse 2s infinite;
}
.badge-rx {
    background-color: #2ecc71;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    display: inline-block;
}
.badge-wait {
    background-color: #ffc425;
    color: black;
    padding: 2px 6px;
    border-radius: 3px;
    display: inline-block;
}

/* -------------------------------------------------------
   Sidebar Toggle & Collapsed State Logic
   ------------------------------------------------------- */

/* Sidebar Container Transition */
.nav {
    position: relative;
    transition: width 0.3s ease;
}

.content {
    transition: margin-left 0.3s ease;
}

/* Sidebar Toggle Strip */
.sidebar-toggle {
    position: absolute;
    top: 50%;
    right: 0;
    width: 20px; /* Width of the docked strip */
    height: 60px; /* Handle height */
    margin-top: -30px; /* Center handle */
    background: <?php echo $backgroundBanners; ?>;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    border-right: none; /* Blend */
    border-radius: 4px 0 0 4px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    color: <?php echo $textBanners; ?>;
    opacity: 0;
    box-shadow: -1px 0 4px rgba(0,0,0,0.1);
    transition: all 0.2s;
}

.sidebar-toggle:hover {
    background-color: <?php echo $backgroundNavbarHover; ?>;
    color: <?php echo $textNavbarHover; ?>;
    opacity: 1;
}

/* Collapsed State:
   This overrides default .nav styles when the 'collapsed' class is added by JS
*/
.nav.collapsed {
    width: 20px !important;
    padding: 0 !important; /* CRITICAL: Removes internal padding so width is exactly 20px */
    overflow: hidden;      /* Hides internal content */
    background: <?php echo $backgroundNavPanel; ?>; /* Ensure BG persists */
    cursor: pointer;       /* Whole strip indicates clickability */
}

/* When collapsed, the toggle button fills the strip */
.nav.collapsed .sidebar-toggle {
    width: 100%;
    height: 100%; /* Full height strip when collapsed */
    top: 0;
    margin-top: 0;
    border: none;
    border-right: 1px solid <?php echo $tableBorderColor; ?>;
    border-radius: 0;
    justify-content: center; /* Keep arrow centered */
}

/* Hide the inner content div when collapsed */
.nav.collapsed #repeaterInfo {
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
}

/* -------------------------------------------------------
   Modern Data Tables
   ------------------------------------------------------- */

.table-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    margin-bottom: 10px;
    padding: 0 5px;
    flex-wrap: wrap;
    gap: 10px;
}

.table-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: <?php echo $textContent; ?>;
    font-family: 'Source Sans Pro', sans-serif;
}

.table-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    font-size: 0.9em;
}

.modern-table-container {
    background: <?php echo $backgroundContent; ?>;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid <?php echo $tableBorderColor; ?>;
    overflow: visible;
    margin-bottom: 25px;
}

table.modern-table {
    width: 100%;
    border-collapse: collapse;
    border: none;
    margin: 0;
    font-family: 'Source Sans Pro', sans-serif;
    font-size: 0.95em;
    background: transparent;
}

table.modern-table th {
    background: <?php echo $backgroundBanners; ?>;
    color: <?php echo $textBanners; ?>;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8em;
    letter-spacing: 0.05em;
    padding: 12px 15px;
    text-align: left; /* Default Left */
    border: none;
    border-bottom: 2px solid <?php echo $tableBorderColor; ?>;
    white-space: nowrap;
}

table.modern-table td {
    padding: 10px 15px;
    border: none;
    border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
    vertical-align: middle;
    text-align: left; /* Default Left */
}

table.modern-table tr:last-child td {
    border-bottom: none;
}

/* Alignment Utility */
.text-center {
    text-align: center !important;
}

/* Striping & Hover */
table.modern-table tr:nth-child(even) {
    background-color: <?php echo $tableRowEvenBg; ?>;
}
table.modern-table tr:nth-child(odd) {
    background-color: <?php echo $tableRowOddBg; ?>;
}

table.modern-table tr:hover td {
    background-color: <?php echo $backgroundDropdownHover; ?> !important;
    color: <?php echo $textDropdownHover; ?> !important;
    transition: background 0.15s ease;
}

/* Links */
table.modern-table a {
    text-decoration: none;
    font-weight: 600;
    color: inherit;
    transition: opacity 0.2s;
}
table.modern-table a:hover {
    text-decoration: underline;
    opacity: 0.8;
}

/* Specific Cell Styles */
.divTableCellMono {
    font-family: 'Inconsolata', monospace !important;
    font-size: 1.1em;
}

/* Toggle Switch Alignment in Toolbar */
.table-actions .grid-container {
    display: inline-flex !important;
    align-items: center;
    gap: 8px;
}

/* Status Pills Data Container */
.pill-data {
    display: flex;
    align-items: center;
}
.pill-value {
    margin-right: 5px; /* Space between value and icon */
}

/* Stacked Pill Variant (Label on top, Value on bottom) */
.status-pill.stacked {
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 8px 5px;
    height: auto; /* Allow height to adjust */
}

.status-pill.stacked > span:first-child {
    width: 100%;
    font-size: 0.75em;
    text-transform: uppercase;
    opacity: 0.7;
    margin-bottom: 2px;
    line-height: 1;
    display: block;
}

.status-pill.stacked .pill-data {
    width: 100%;
    justify-content: center;
    font-size: 1.1em;
}

.status-pill.stacked .pill-value {
    margin-right: 0; /* Center value without right margin offset */
}

/* Ensure D-Star RPT/POCSAG cells have enough height */
.sidebar-status-grid {
    align-items: stretch; /* Make cells same height in row */
}

/* -------------------------------------------------------
   Modern CSS Grid Dashboard Layout
   ------------------------------------------------------- */

.mode_flex {
    display: grid;
    /* Auto-fit columns: Create as many columns as fit, min-width 220px */
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px; /* Consistent space between all cards */
    padding: 10px 0;
    width: 100%;
}

/* "display: contents" unboxes the nested divs. 
   The browser acts like the <button> is a direct child of .mode_flex 
*/
.mode_flex .row,
.mode_flex .column {
    display: contents;
}

.mode_flex button {
    /* Reset margins/widths because Grid handles the spacing now */
    margin: 0;
    width: 100%;
    height: 100%; /* Forces all buttons to equal height */
    
    /* Keep the visual style we created previously */
    background: <?php echo $backgroundNavbar; ?>;
    color: <?php echo $textNavbar; ?>;
    border: 1px solid <?php echo $tableBorderColor; ?>;
    border-radius: 8px; /* Slightly softer corners */
    padding: 20px 15px;
    
    /* Typography & Layout */
    font-weight: 700;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    justify-content: center;
    
    /* Effects */
    box-shadow: 0 4px 6px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s;
    cursor: pointer;
}

.mode_flex button > span {
    pointer-events: none;
    line-height: 1.3;
}

/* Hover Effects */
.mode_flex button:hover {
    background: <?php echo str_replace('##', '#', $backgroundNavbarHover); ?>;
    color: <?php echo $textNavbarHover; ?>;
    border-color: <?php echo $textNavbarHover; ?>;
    transform: translateY(-5px); /* Stronger lift */
    box-shadow: 0 12px 20px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,0.1);
    z-index: 10; /* Ensure it floats above others */
}

.mode_flex button:active {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.mode_flex button:disabled {
    background: <?php echo $backgroundModeCellDisabledColor; ?>;
    color: <?php echo $textModeCellDisabledColor; ?>;
    opacity: 0.5;
    transform: none;
    box-shadow: none;
    cursor: not-allowed;
    border: 1px solid transparent;
}
