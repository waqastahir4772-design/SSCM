<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School SCM | Professional Supply Chain Management</title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="SSCMS provides an end-to-end supply chain management solution for schools, covering procurement, inventory, and distribution with real-time tracking.">
    <meta name="keywords" content="school inventory, supply chain, procurement, education management">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

    <!-- Navbar -->
    <nav id="mainNav">
        <div class="logo">School SCM</div>
        <ul class="nav-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#tracking">SCM Tracking</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <a href="login.php" class="btn-login">Launch App</a>
    </nav>

    <!-- Hero Section -->
    <?php
    require_once 'config/database.php';
    require_once 'includes/functions.php';
    
    // Fetch real-time data for the landing page hero
    $item_count = get_count('items', 'deleted_at IS NULL');
    $partner_count = get_count('scm_partners', 'status="Active"');
    $request_count = get_count('purchase_requests', '1=1');
    ?>

    <section class="hero">
        <div class="hero-content">
            <h1>Revolutionizing School <span>Supply Chains</span></h1>
            <p>Empower your institution with end-to-end visibility. From manufacturers to classrooms, track every item with precision and ease.</p>
            
            <!-- Quick Stats -->
            <div style="display: flex; gap: 2rem; margin-bottom: 2rem;">
                <div>
                    <h4 style="color: var(--primary); font-size: 1.5rem;"><?php echo number_format($item_count); ?>+</h4>
                    <p style="font-size: 0.8rem; color: var(--gray);">Items Tracked</p>
                </div>
                <div>
                    <h4 style="color: var(--secondary); font-size: 1.5rem;"><?php echo number_format($partner_count); ?>+</h4>
                    <p style="font-size: 0.8rem; color: var(--gray);">SCM Partners</p>
                </div>
            </div>

            <div class="hero-btns">
                <a href="login.php" class="btn-login" style="padding: 1rem 2.5rem; font-size: 1.1rem;">Get Started Free</a>
            </div>
        </div>
        <div class="hero-visual">
            <img src="assets/img/hero-bg.png" alt="Futuristic Supply Chain Visualization">
        </div>
    </section>

    <!-- How it Works Section -->
    <section style="padding: 100px 5%; background: var(--dark);">
        <div class="section-title">
            <h2>The Supply Chain Journey</h2>
            <p style="color: var(--gray);">See how School SCM manages the lifecycle of shared educational resources.</p>
        </div>

        <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 3rem;">
            <div style="text-align: center; max-width: 250px;">
                <div style="width: 80px; height: 80px; background: rgba(6, 182, 212, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--primary); font-size: 1.8rem; border: 1px dashed var(--primary);">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h4>1. Request</h4>
                <p style="color: var(--gray); font-size: 0.9rem;">Internal staff submit purchase requests based on classroom needs.</p>
            </div>
            
            <div style="text-align: center; max-width: 250px;">
                <div style="width: 80px; height: 80px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--secondary); font-size: 1.8rem; border: 1px dashed var(--secondary);">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <h4>2. Source</h4>
                <p style="color: var(--gray); font-size: 0.9rem;">Orders are sent directly to manufacturers and distributors globally.</p>
            </div>

            <div style="text-align: center; max-width: 250px;">
                <div style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #10b981; font-size: 1.8rem; border: 1px dashed #10b981;">
                    <i class="fas fa-school"></i>
                </div>
                <h4>3. Deliver</h4>
                <p style="color: var(--gray); font-size: 0.9rem;">Supplies arrive at the school and inventory is updated instantly.</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-title">
            <h2>Powerful Features</h2>
            <p style="color: var(--gray);">Everything you need to manage your school's inventory lifecycle.</p>
        </div>
        
        <div class="grid">
            <div class="card">
                <i class="fas fa-boxes"></i>
                <h3>Intelligent Inventory</h3>
                <p>Real-time stock level tracking with automated low-stock alerts and smart reordering levels.</p>
            </div>
            
            <div class="card">
                <i class="fas fa-project-diagram"></i>
                <h3>End-to-End Tracking</h3>
                <p>Full visibility from raw material suppliers to manufacturers, distributors, and finally your school.</p>
            </div>
            
            <div class="card">
                <i class="fas fa-user-shield"></i>
                <h3>Role-Based Security</h3>
                <p>Secure access for Admins, Managers, and Department Heads with granular permission controls.</p>
            </div>
            
            <div class="card">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>Automated Procurement</h3>
                <p>Seamless purchase request workflows with one-click approvals and direct connection to partners.</p>
            </div>
            
            <div class="card">
                <i class="fas fa-chart-bar"></i>
                <h3>Insightful Analytics</h3>
                <p>Comprehensive reporting on stock usage, procurement costs, and supply chain performance.</p>
            </div>
            
            <div class="card">
                <i class="fas fa-mobile-alt"></i>
                <h3>Responsive Design</h3>
                <p>Access your dashboard from anywhere. Optimized for desktops, tablets, and smartphones.</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" style="padding: 100px 5%; background: var(--dark-accent);">
        <div class="section-title">
            <h2>Get In Touch</h2>
            <p style="color: var(--gray);">Have questions? Our experts are here to help your school transition.</p>
        </div>

        <div style="max-width: 800px; margin: 0 auto; background: var(--glass); padding: 3rem; border-radius: 20px; border: 1px solid var(--glass-border);">
            <form action="javascript:alert('Thank you! Our team will contact you soon.')" style="display: grid; gap: 1.5rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="color: var(--gray); font-size: 0.9rem;">Full Name</label>
                        <input type="text" placeholder="John Doe" required style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 10px; color: white;">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="color: var(--gray); font-size: 0.9rem;">School Email</label>
                        <input type="email" placeholder="john@school.edu" required style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 10px; color: white;">
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="color: var(--gray); font-size: 0.9rem;">Message</label>
                    <textarea rows="4" placeholder="Tell us about your inventory needs..." required style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 10px; color: white;"></textarea>
                </div>
                <button type="submit" class="btn-login" style="padding: 1rem; font-size: 1.1rem; border: none; cursor: pointer;">Send Message</button>
            </form>
        </div>
    </section>

    <!-- CTA Section -->
    <section style="padding: 100px 5%; text-align: center; background: radial-gradient(circle at center, #1e293b, #0f172a);">
        <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Ready to upgrade your school?</h2>
        <p style="color: var(--gray); max-width: 600px; margin: 0 auto 2rem;">Join hundreds of institutions already optimizing their supply chain with School SCM.</p>
        <a href="login.php" class="btn-login" style="padding: 1rem 3rem;">Enter Dashboard</a>
    </section>

    <!-- Footer -->
    <footer style="padding: 3rem 5%; border-top: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div class="footer-logo" style="font-weight: 800; color: var(--gray);">© <?php echo date('Y'); ?> School SCM. All rights reserved.</div>
        <div class="socials" style="display: flex; gap: 1.5rem; font-size: 1.2rem; color: var(--gray);">
            <i class="fab fa-twitter"></i>
            <i class="fab fa-linkedin"></i>
            <i class="fab fa-github"></i>
        </div>
    </footer>

    <!-- Custom JS -->
    <script src="assets/js/landing.js"></script>
</body>
</html>
