# SCHOOL SUPPLY CHAIN MANAGEMENT SYSTEM (SSCMS)
## COMPLETE PROJECT SPECIFICATION - NOTEPAD FRIENDLY FORMAT

================================================================================
PROJECT METADATA
================================================================================
Project Name: School Supply Chain Management System (SSCMS)
Version: 1.0
Date: February 27, 2026
Tech Stack: PHP Native, MySQL, Bootstrap 4.6, AdminLTE 3.2, Font Awesome 6.4
Target Users: School Administrators, Inventory Managers, Store Keepers
Development Approach: Role-Based Access Control (RBAC), Modular Architecture

================================================================================
1. PROJECT OVERVIEW
================================================================================

1.1 System Purpose
A comprehensive web-based system to manage the complete supply chain lifecycle 
for school inventory:
- Procurement: From suppliers to school storage
- Inventory Management: Real-time stock tracking
- Distribution: To departments/students
- Supply Chain Visibility: End-to-end tracking from raw materials to end-user

1.2 Core Objectives
- Automate manual inventory processes
- Reduce stockouts and overstocking
- Provide visibility across supply chain partners
- Streamline purchase request and approval workflows
- Generate actionable reports for decision making
- Maintain audit trails for all transactions

1.3 Key Stakeholders
Role                Responsibilities
System Administrator Full system access, user management, configuration
Inventory Manager    Approve requests, manage stock, generate reports
Store Keeper         Receive stock, issue items, update inventory
Department Head      Create purchase requests, view department inventory
Teacher              View available items, request supplies

================================================================================
2. TECHNOLOGY STACK
================================================================================

2.1 Core Technologies
Component           Technology      Version     Purpose
Backend             PHP             8.0+        Server-side logic, business rules
Database            MySQL           8.0+        Data persistence, relationships
Frontend Framework  Bootstrap       4.6.2       Responsive UI components
Admin Template      AdminLTE        3.2.0       Dashboard UI framework
Icons               Font Awesome    6.4.0       Vector icons throughout UI
JavaScript          jQuery          3.6.0       DOM manipulation, AJAX
Session Management  PHP Sessions    Native      User authentication state

2.2 Project Structure
school_scm/
├── config/
│   └── database.php          (DB connection & helper functions)
├── includes/
│   ├── header.php            (Top navigation bar)
│   ├── sidebar.php           (Left sidebar menu)
│   ├── footer.php            (Page footer)
│   └── functions.php         (Common utility functions)
├── assets/
│   ├── css/
│   │   └── custom.css
│   └── js/
│       └── main.js
├── login.php                 (Authentication entry point)
├── dashboard.php             (Main dashboard)
├── logout.php                (Session termination)
├── items/                    (Items management module)
│   ├── index.php             (Items listing)
│   ├── add.php               (Add new item)
│   ├── edit.php              (Edit item)
│   └── delete.php            (Delete item - soft delete)
├── vendors/                  (Vendors management module)
│   ├── index.php
│   ├── add.php
│   ├── edit.php
│   └── delete.php
└── scm/                      (Supply Chain Management module)
    ├── index.php             (SCM Dashboard)
    ├── suppliers.php         (Suppliers CRUD)
    ├── manufacturers.php     (Manufacturers CRUD)
    ├── distributors.php      (Distributors CRUD)
    ├── retailers.php         (Retailers CRUD)
    ├── purchase_requests.php (Request workflow)
    └── purchase_orders.php   (Order management)

================================================================================
3. DATABASE SCHEMA
================================================================================

3.1 Core Tables
Table                   Description                     Key Relationships
users                   System users with roles         -
departments             School departments              -
items                   Inventory items catalog         category_id -> item_categories
vendors                 Direct suppliers to school      -
suppliers               Raw material suppliers          -
manufacturers           Item manufacturers              -
distributors            Distribution partners           -
retailers               Retail partners                 -
purchase_requests       Internal item requests          department_id, item_id, requested_by
purchase_orders         Purchase orders to partners     request_id, supplier_id, etc.
stock_transactions      All stock movements             item_id, po_id, performed_by
supply_chain_flow       End-to-end tracking             item_id, partner IDs

3.2 Critical Relationships (Text Description)
- Users create purchase requests
- Departments own purchase requests
- Items are requested in purchase requests
- Approved purchase requests generate purchase orders
- Purchase orders link to partners (suppliers/manufacturers/distributors/retailers)
- Items have stock transactions (in/out/adjustment)
- Purchase orders trigger stock transactions when received
- Supply chain flow tracks item journey through all partners

3.3 Sample Data Requirements
- Users: 3 default accounts (admin, manager, user)
- Departments: 5 departments (Admin, Science, Arts, Sports, Library)
- Items: 11 sample items across categories
- Vendors: 5 sample vendors
- SCM Partners: 3 suppliers, 3 manufacturers, 3 distributors, 3 retailers

================================================================================
4. ROLE-BASED ACCESS CONTROL (RBAC)
================================================================================

4.1 Role Definitions
Role        Permissions                                      Restrictions
Admin       - Full system access                             None
            - User management
            - All CRUD operations
            - View all reports
Manager     - Approve/reject requests                        Cannot delete users
            - View all inventory                             Cannot modify system settings
            - Generate reports
            - Manage vendors/partners
User        - Create purchase requests                       Cannot approve requests
            - View own department items                      Cannot delete records
            - Receive/issue stock                            Limited to own department
            - View low stock alerts

4.2 Page-Level Access Matrix
Page                    Admin   Manager   User
Dashboard               YES     YES       YES
Items Management        FULL    VIEW/EDIT VIEW ONLY
Vendors Management      FULL    VIEW/EDIT NO
SCM Dashboard           YES     YES       YES
Suppliers CRUD          FULL    VIEW/EDIT NO
Manufacturers CRUD      FULL    VIEW/EDIT NO
Distributors CRUD       FULL    VIEW/EDIT NO
Retailers CRUD          FULL    VIEW/EDIT NO
Purchase Requests       APPROVE APPROVE   CREATE/VIEW OWN
Purchase Orders         FULL    VIEW/CREATE NO
Stock Transactions      VIEW ALL VIEW ALL VIEW OWN DEPT
Reports                 ALL     DEPT      NO

================================================================================
5. MODULE SPECIFICATIONS
================================================================================

5.1 Authentication Module
Pages: login.php, logout.php
Features:
- Username/password authentication
- Session management with timeout
- Role-based redirection after login
- "Remember me" functionality
- Password validation (demo uses plain text; production should use hashing)

5.2 Dashboard Module
Page: dashboard.php
Components:
- Statistics Cards: Total items, low stock items, vendors, pending requests, SCM partners
- Recent Items Table: Last 5 added items with stock status
- Quick Actions: "Add Item", "Create Request" buttons
- Low Stock Alerts: Visual indicators for items below reorder level
- Responsive Layout: Adapts to desktop/tablet/mobile

5.3 Items Management Module
Pages: items/index.php, items/add.php, items/edit.php, items/delete.php
Features:
Listing Page:
- Filterable/searchable table
- Stock status badges (green=normal, red=low)
- Action buttons (Edit/Delete)
- "Add New Item" button
Add/Edit Form:
- Fields: Name, Code, Category, Unit Type, Price, Current Stock, Reorder Level, Location, Description
- Validation: Required fields, numeric checks
- Category dropdown with existing categories
Delete: Soft delete with confirmation dialog

5.4 Vendors Management Module
Pages: vendors/index.php, vendors/add.php, vendors/edit.php, vendors/delete.php
Features:
Listing Page:
- Vendor details table (name, contact, phone, email, city)
- Status badges (Active/Inactive)
- Action buttons
Add/Edit Form:
- Fields: Name, Code, Contact Person, Phone, Email, Address, City
- Validation: Email format, required fields

5.5 Supply Chain Management (SCM) Module
SCM Dashboard (scm/index.php):
- Statistics Cards: Counts of suppliers, manufacturers, distributors, retailers
- Recent Flow Table: Last 10 supply chain movements with stage/status
- Quick Actions: "Add Supplier", "Create Request", "Place Order", "Track Order"

Partners Management (scm/suppliers.php, etc.):
- Unified CRUD Interface for all partner types
- Partner-Specific Fields:
  * Suppliers: Material Type
  * Manufacturers: Manufacturing Capacity
  * Distributors: Distribution Area
  * Retailers: Store Type
- Status Management: Active/Inactive toggle

Purchase Requests (scm/purchase_requests.php):
- Request Creation:
  * Department selection
  * Item selection with current stock display
  * Quantity input
  * Description field
- Approval Workflow:
  * Status badges (Pending/Approved/Rejected/Completed)
  * Approve/Reject buttons for managers/admins
  * Approval timestamp tracking
- Department Filtering: View requests by department

Purchase Orders (scm/purchase_orders.php):
- Order Creation:
  * Link to approved request
  * Partner selection (supplier/manufacturer/distributor/retailer)
  * Order number auto-generation
  * Expected delivery date
- Order Tracking:
  * Status badges (Ordered/In Transit/Received/Cancelled)
  * "Receive Stock" action to update inventory
  * Link to stock transactions

Supply Chain Flow (scm/supply_chain_flow.php):
- End-to-End Tracking:
  * Visual representation of item journey
  * Stage indicators (Supplier → Manufacturer → Distributor → Retailer → School)
  * Status tracking per stage
  * Cost tracking at each stage
- Filtering: By item, date range, status

================================================================================
6. PAGE STRUCTURE SPECIFICATION
================================================================================

6.1 Universal Page Components (All Pages)
HEADER (includes/header.php)
- Left Navbar Toggle Button
- Breadcrumb Navigation (Module > Page)
- Right Navbar
  * User Profile Dropdown
    - User Name & Role Badge
    - Divider
    - Logout Link
  * (Optional) Notifications Icon

SIDEBAR (includes/sidebar.php)
- Brand Logo/Text ("School SCM")
- User Panel (Name + Role Badge)
- Navigation Menu
  * Dashboard Link
  * Items Management Section
  * Vendors Management Section
  * SUPPLY CHAIN MANAGEMENT Header
  * SCM Dashboard Link
  * Suppliers Link
  * Manufacturers Link
  * Distributors Link
  * Retailers Link
  * Purchase Requests Link
  * Purchase Orders Link

CONTENT AREA
- Content Header
  * Page Title with Icon
  * Action Buttons (e.g., "Add New")
- Main Content Section
  * [Page-Specific Content]
- (Optional) Breadcrumbs

FOOTER (includes/footer.php)
- Copyright Notice with Year

6.2 Page-Specific Content Structure
Dashboard Page (dashboard.php)
CONTENT AREA
- Statistics Row (2 rows of 4 cards each)
  * Total Items Card (Info)
  * Low Stock Items Card (Warning)
  * Total Vendors Card (Success)
  * Pending Requests Card (Danger)
  * Suppliers Card (Primary)
  * Manufacturers Card (Success)
  * Distributors Card (Warning)
  * Retailers Card (Info)
- Recent Items Section
  * Card Header with Title & "Add Item" Button
  * Responsive Table
    - Columns: ID, Item Name, Code, Category, Price, Stock, Status
    - Row Actions: Edit/Delete Buttons

Items Listing Page (items/index.php)
CONTENT AREA
- Page Header
  * Title "Items Management"
  * "Add New Item" Button (Primary)
- Success Alert (if msg parameter exists)
- Items Table
  * Columns: ID, Name, Code, Category, Unit Price, Stock, Location, Actions
  * Stock Column: Color-coded badges (green/red)
  * Status Column: "In Stock"/"Low Stock" badges
  * Actions Column: Edit (Warning) & Delete (Danger) buttons

Add/Edit Item Page (items/add.php, items/edit.php)
CONTENT AREA
- Page Header
  * Title ("Add New Item" / "Edit Item")
  * Breadcrumb Navigation
- Error Alert (if validation fails)
- Card Container
  * Card Header: "Item Information"
  * Card Body
    - Form (method="POST")
      * Row 1: Item Name (required), Item Code (required)
      * Row 2: Category (dropdown), Unit Type (dropdown)
      * Row 3: Unit Price, Current Stock
      * Row 4: Reorder Level (with helper text), Location
      * Row 5: Description (textarea)
      * Action Buttons: Cancel (Secondary), Save (Primary)
    - Form Validation: Required fields marked with red asterisk

SCM Dashboard (scm/index.php)
CONTENT AREA
- SCM Statistics Row (6 cards)
  * Suppliers Count
  * Manufacturers Count
  * Distributors Count
  * Retailers Count
  * Pending Requests Count
  * Low Stock Items Count
- Recent Supply Chain Activity Section
  * Card Header: "Recent Supply Chain Activity" + "View All" Button
  * Responsive Table
    - Columns: Date, Item, Stage, Partner, Quantity, Status
    - Stage Column: Color-coded badges per stage
    - Status Column: Color-coded status badges
- Quick Actions Row (4 cards)
  * "Create Purchase Request" Card (Primary)
  * "Place Order" Card (Success)
  * "Add Supplier" Card (Warning)
  * "Track Order" Card (Info)

================================================================================
7. SECURITY & BEST PRACTICES
================================================================================

7.1 Security Measures
- Input Sanitization: All user inputs escaped using mysqli_real_escape_string()
- SQL Injection Prevention: Parameterized queries recommended for production
- Session Security:
  * Session regeneration on login
  * Session timeout after 30 minutes of inactivity
  * Session validation on every page load
- XSS Prevention: Output escaping with htmlspecialchars() for all dynamic content
- CSRF Protection: Recommended for production (token-based)

7.2 Data Validation Rules
Field Type      Validation Rules
Text Inputs     Trim whitespace, min/max length checks
Numbers         Min value = 0, decimal precision for currency
Emails          Format validation using filter_var()
Required Fields Server-side validation with error messages
Unique Fields   Database uniqueness constraint + server validation

7.3 Error Handling
- User-Facing Errors: Friendly messages in alert boxes
- Database Errors: Logged to file (not shown to users)
- Form Validation: Inline error messages below fields
- Success Messages: Green alerts with confirmation text

7.4 Performance Considerations
- Database Indexing: Primary keys, foreign keys, and frequently queried columns
- Query Optimization: Avoid SELECT *, use LIMIT for large tables
- Asset Loading: CDN for libraries, minified CSS/JS in production
- Session Management: Store minimal data in session

================================================================================
8. IMPLEMENTATION CHECKLIST
================================================================================

PHASE 1: FOUNDATION
[ ] Create database and import schema
[ ] Set up project folder structure
[ ] Implement database connection (config/database.php)
[ ] Create reusable components (includes/header.php, sidebar.php, footer.php)
[ ] Implement authentication system (login.php, logout.php)

PHASE 2: CORE MODULES
[ ] Build main dashboard with statistics
[ ] Implement Items Management module (CRUD)
[ ] Implement Vendors Management module (CRUD)
[ ] Create low stock alert system

PHASE 3: SCM MODULES
[ ] Build SCM Dashboard with partner statistics
[ ] Implement Suppliers CRUD
[ ] Implement Manufacturers CRUD
[ ] Implement Distributors CRUD
[ ] Implement Retailers CRUD
[ ] Build Purchase Requests workflow
[ ] Build Purchase Orders management
[ ] Implement Supply Chain Flow tracking

PHASE 4: POLISH & TESTING
[ ] Implement role-based access control on all pages
[ ] Add form validation and error handling
[ ] Test all CRUD operations
[ ] Verify responsive design on multiple devices
[ ] Import sample data for demonstration
[ ] Create user documentation

================================================================================
9. APPENDIX
================================================================================

9.1 Color Scheme (AdminLTE Default)
Element     Color Code    Usage
Primary     #3498db       Main actions, info elements
Success     #28a745       Positive actions, in-stock items
Warning     #ffc107       Low stock alerts, pending status
Danger      #dc3545       Delete actions, critical alerts
Info        #17a2b8       Informational elements
Secondary   #6c757d       Disabled elements, secondary text

9.2 Icon Usage Guide
Context         Recommended Icon        Example Usage
Dashboard       fas fa-tachometer-alt   Main dashboard link
Items           fas fa-boxes            Items management
Vendors         fas fa-truck            Vendors management
Suppliers       fas fa-industry         Suppliers section
Manufacturers   fas fa-cogs             Manufacturers section
Distributors    fas fa-truck-loading    Distributors section
Retailers       fas fa-store            Retailers section
Requests        fas fa-shopping-cart    Purchase requests
Orders          fas fa-file-invoice     Purchase orders
Flow            fas fa-project-diagram  Supply chain flow
Add             fas fa-plus             "Add New" buttons
Edit            fas fa-edit             Edit actions
Delete          fas fa-trash            Delete actions
View            fas fa-eye              View details
Approve         fas fa-check            Approval actions
Reject          fas fa-times            Rejection actions

9.3 Sample Data Guidelines
- Item Codes: Use prefix system (BK=Books, ST=Stationery, UN=Uniform, SP=Sports, LB=Lab)
- Partner Codes: Use prefix system (SUP=Supplier, MFG=Manufacturer, DIS=Distributor, RET=Retailer, VND=Vendor)
- Order Numbers: Auto-generate with prefix + date + sequence (e.g., PO-20260227-001)
- Status Values: Use consistent ENUM values across tables

================================================================================
10. FINAL NOTES FOR DEVELOPMENT AGENT
================================================================================

1. START SIMPLE
   - Implement core modules first (Authentication, Dashboard, Items), then SCM modules
   - Get basic CRUD working before adding complex workflows

2. CONSISTENCY IS KEY
   - Use same form structure across all CRUD pages
   - Maintain consistent button colors and placements
   - Follow AdminLTE component patterns exactly
   - All pages must include header.php, sidebar.php, footer.php

3. SECURITY FIRST
   - ALWAYS sanitize inputs before database operations: escape($data)
   - ALWAYS escape outputs to prevent XSS: htmlspecialchars($var)
   - Validate user roles on EVERY page load
   - Never trust user input - validate everything server-side

4. USER EXPERIENCE
   - Include confirmation dialogs for destructive actions (delete)
   - Show success/error messages after form submissions
   - Use intuitive icons with text labels
   - Make all buttons and links clearly visible

5. TESTING CHECKLIST
   - Test all CRUD operations for each module
   - Verify role-based access restrictions (login as each role)
   - Test responsive layout on mobile/tablet/desktop
   - Validate form inputs with edge cases (empty, special chars, etc.)
   - Check database constraints (unique fields, foreign keys)

6. PRODUCTION READINESS (FUTURE)
   - Replace plain text passwords with password_hash()/password_verify()
   - Implement proper error logging to file
   - Add CSRF protection tokens to all forms
   - Set up automated database backups
   - Configure session security settings (httponly, secure flags)

================================================================================
DOCUMENT PREPARED FOR: Development Implementation Team
PURPOSE: Comprehensive guide for building School Supply Chain Management System
NEXT STEP: Begin implementation following Phase 1 checklist
CONTACT: Project Manager for clarification on specifications
================================================================================

END OF SPECIFICATION DOCUMENT




@readme.txt this is our prject detail file, analyze it and make module wise implementation plan