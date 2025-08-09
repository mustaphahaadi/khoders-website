// KHODERS WORLD - Enhanced JavaScript Functionality
// Combining all contributors' interactive features

class KhodersWebsite {
    constructor() {
        this.init();
        this.bindEvents();
        this.initializeComponents();
    }

    init() {
        // Initialize theme
        this.currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', this.currentTheme);
        
        // Initialize notification system
        this.notificationQueue = [];
        this.isNotificationShowing = false;
        
        // Initialize loading state
        this.isLoading = false;
        
        // Initialize scroll position
        this.lastScrollTop = 0;
        
        // Initialize intersection observer for animations
        this.initIntersectionObserver();
        
        // Initialize statistics counter
        this.statsAnimated = false;
    }

    bindEvents() {
        // Navigation events
        this.bindNavigationEvents();
        
        // Modal events
        this.bindModalEvents();
        
        // Form events
        this.bindFormEvents();
        
        // Floating button events
        this.bindFloatingButtonEvents();
        
        // FAQ events
        this.bindFAQEvents();
        
        // Scroll events
        this.bindScrollEvents();
        
        // Theme events
        this.bindThemeEvents();
        
        // Search events
        this.bindSearchEvents();
    }

    initializeComponents() {
        // Initialize smooth scrolling
        this.initSmoothScrolling();
        
        // Initialize lazy loading
        this.initLazyLoading();
        
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize animations
        this.initAnimations();
        
        // Initialize performance monitoring
        this.initPerformanceMonitoring();
    }

    // Navigation functionality (Kamal's contribution)
    bindNavigationEvents() {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        const navLinks = document.querySelectorAll('.nav-menu a');

        if (hamburger && navMenu) {
            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('active');
                navMenu.classList.toggle('active');
                
                // Animate hamburger
                const spans = hamburger.querySelectorAll('span');
                spans.forEach((span, index) => {
                    if (hamburger.classList.contains('active')) {
                        if (index === 0) span.style.transform = 'rotate(45deg) translate(5px, 5px)';
                        if (index === 1) span.style.opacity = '0';
                        if (index === 2) span.style.transform = 'rotate(-45deg) translate(7px, -6px)';
                    } else {
                        span.style.transform = 'none';
                        span.style.opacity = '1';
                    }
                });
            });

            // Close mobile menu when clicking on links
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    hamburger.classList.remove('active');
                    navMenu.classList.remove('active');
                    
                    // Reset hamburger animation
                    const spans = hamburger.querySelectorAll('span');
                    spans.forEach(span => {
                        span.style.transform = 'none';
                        span.style.opacity = '1';
                    });
                });
            });
        }

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                if (window.scrollY > 100) {
                    navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    navbar.style.backdropFilter = 'blur(10px)';
                } else {
                    navbar.style.background = 'var(--white)';
                    navbar.style.backdropFilter = 'none';
                }
            }
        });
    }

    // Modal functionality (Frederick & Gyawu's contribution)
    bindModalEvents() {
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        const loginBtn = document.querySelector('.login-btn');
        const registerLink = document.getElementById('registerLink');
        const loginLink = document.getElementById('loginLink');
        const closeBtns = document.querySelectorAll('.close');

        // Open login modal
        if (loginBtn && loginModal) {
            loginBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.openModal(loginModal);
            });
        }

        // Switch between modals
        if (registerLink && registerModal && loginModal) {
            registerLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeModal(loginModal);
                setTimeout(() => this.openModal(registerModal), 300);
            });
        }

        if (loginLink && loginModal && registerModal) {
            loginLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeModal(registerModal);
                setTimeout(() => this.openModal(loginModal), 300);
            });
        }

        // Close modals
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal');
                if (modal) this.closeModal(modal);
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeModal(e.target);
            }
        });

        // Handle escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal[style*="block"]');
                if (openModal) this.closeModal(openModal);
            }
        });
    }

    openModal(modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Focus first input
        const firstInput = modal.querySelector('input');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }

    closeModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Form functionality (Abonopaya's contribution + enhancements)
    bindFormEvents() {
        // Contact form
        const contactForm = document.querySelector('.contact form');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleContactForm(contactForm);
            });
        }

        // Newsletter form
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleNewsletterForm(newsletterForm);
            });
        }

        // Login form
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleLoginForm(loginForm);
            });
        }

        // Register form
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleRegisterForm(registerForm);
            });
        }

        // Real-time form validation
        this.initFormValidation();
    }

    handleContactForm(form) {
        this.showLoading();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Simulate API call
        setTimeout(() => {
            this.hideLoading();
            this.showNotification('Message sent successfully! We\'ll get back to you soon.', 'success');
            form.reset();
        }, 2000);
    }

    handleNewsletterForm(form) {
        const email = form.querySelector('input[type="email"]').value;
        
        if (this.validateEmail(email)) {
            this.showLoading();
            
            // Simulate API call
            setTimeout(() => {
                this.hideLoading();
                this.showNotification('Successfully subscribed to our newsletter!', 'success');
                form.reset();
            }, 1500);
        } else {
            this.showNotification('Please enter a valid email address.', 'error');
        }
    }

    handleLoginForm(form) {
        const email = form.querySelector('#email').value;
        const password = form.querySelector('#password').value;
        
        if (this.validateEmail(email) && password.length >= 6) {
            this.showLoading();
            
            // Simulate API call
            setTimeout(() => {
                this.hideLoading();
                this.closeModal(document.getElementById('loginModal'));
                this.showNotification('Welcome back to KHODERS!', 'success');
                form.reset();
            }, 2000);
        } else {
            this.showNotification('Please check your credentials.', 'error');
        }
    }

    handleRegisterForm(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        if (this.validateRegistration(data)) {
            this.showLoading();
            
            // Simulate API call
            setTimeout(() => {
                this.hideLoading();
                this.closeModal(document.getElementById('registerModal'));
                this.showNotification('Account created successfully! Welcome to KHODERS!', 'success');
                form.reset();
            }, 2000);
        }
    }

    validateRegistration(data) {
        if (!data.fullName || data.fullName.length < 2) {
            this.showNotification('Please enter your full name.', 'error');
            return false;
        }
        
        if (!this.validateEmail(data.email)) {
            this.showNotification('Please enter a valid email address.', 'error');
            return false;
        }
        
        if (data.password.length < 6) {
            this.showNotification('Password must be at least 6 characters long.', 'error');
            return false;
        }
        
        if (data.password !== data.confirmPassword) {
            this.showNotification('Passwords do not match.', 'error');
            return false;
        }
        
        return true;
    }

    initFormValidation() {
        const inputs = document.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
            
            input.addEventListener('input', () => {
                this.clearFieldError(input);
            });
        });
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        // Remove existing error styling
        field.classList.remove('error');
        
        // Validation rules
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            message = 'This field is required.';
        } else if (field.type === 'email' && value && !this.validateEmail(value)) {
            isValid = false;
            message = 'Please enter a valid email address.';
        } else if (field.type === 'password' && value && value.length < 6) {
            isValid = false;
            message = 'Password must be at least 6 characters long.';
        }

        if (!isValid) {
            field.classList.add('error');
            field.style.borderColor = 'var(--danger)';
        } else {
            field.style.borderColor = 'var(--success)';
        }

        return isValid;
    }

    clearFieldError(field) {
        field.classList.remove('error');
        field.style.borderColor = '';
    }

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Floating buttons functionality
    bindFloatingButtonEvents() {
        const backToTop = document.getElementById('backToTop');
        const themeToggle = document.getElementById('themeToggle');
        const chatBot = document.getElementById('chatBot');

        // Back to top
        if (backToTop) {
            backToTop.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        // Theme toggle
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        }

        // Chat bot
        if (chatBot) {
            chatBot.addEventListener('click', () => {
                this.openChatBot();
            });
        }
    }

    // FAQ functionality
    bindFAQEvents() {
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            if (question) {
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    
                    // Close all other FAQ items
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                        }
                    });
                    
                    // Toggle current item
                    item.classList.toggle('active', !isActive);
                });
            }
        });
    }

    // Scroll events
    bindScrollEvents() {
        let ticking = false;
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const backToTop = document.getElementById('backToTop');
        
        // Show/hide back to top button
        if (backToTop) {
            if (scrollTop > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        }
        
        // Animate statistics when in view
        if (!this.statsAnimated) {
            const statsSection = document.querySelector('.stats-section');
            if (statsSection && this.isElementInViewport(statsSection)) {
                this.animateStatistics();
                this.statsAnimated = true;
            }
        }
        
        // Parallax effect for hero section
        const hero = document.querySelector('.hero');
        if (hero && scrollTop < window.innerHeight) {
            const parallaxSpeed = scrollTop * 0.5;
            hero.style.transform = `translateY(${parallaxSpeed}px)`;
        }
        
        this.lastScrollTop = scrollTop;
    }

    // Theme functionality
    bindThemeEvents() {
        // System theme detection
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addListener((e) => {
            if (!localStorage.getItem('theme')) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    toggleTheme() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.setTheme(newTheme);
        
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
        
        this.showNotification(`Switched to ${newTheme} theme`, 'success');
    }

    setTheme(theme) {
        this.currentTheme = theme;
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    }

    // Search functionality
    bindSearchEvents() {
        // Add search functionality if search input exists
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch(e.target.value);
                }, 300);
            });
        }
    }

    performSearch(query) {
        if (query.length < 2) return;
        
        // Simple search implementation
        const searchableElements = document.querySelectorAll('h1, h2, h3, p');
        const results = [];
        
        searchableElements.forEach(element => {
            if (element.textContent.toLowerCase().includes(query.toLowerCase())) {
                results.push({
                    element,
                    text: element.textContent,
                    section: this.findParentSection(element)
                });
            }
        });
        
        this.displaySearchResults(results, query);
    }

    findParentSection(element) {
        let parent = element.parentElement;
        while (parent && !parent.tagName.toLowerCase().includes('section')) {
            parent = parent.parentElement;
        }
        return parent ? parent.id || parent.className : 'unknown';
    }

    displaySearchResults(results, query) {
        // Implementation for displaying search results
        console.log(`Found ${results.length} results for "${query}"`);
    }

    // Smooth scrolling
    initSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                const targetId = link.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const offsetTop = targetElement.offsetTop - 80; // Account for fixed navbar
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // Lazy loading
    initLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            images.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback for older browsers
            images.forEach(img => {
                img.src = img.dataset.src;
            });
        }
    }

    // Tooltips
    initTooltips() {
        const tooltipElements = document.querySelectorAll('[title]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.getAttribute('title'));
            });
            
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: var(--dark-gray);
            color: var(--white);
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            z-index: 1000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
        
        setTimeout(() => tooltip.style.opacity = '1', 10);
        
        this.currentTooltip = tooltip;
    }

    hideTooltip() {
        if (this.currentTooltip) {
            this.currentTooltip.style.opacity = '0';
            setTimeout(() => {
                if (this.currentTooltip && this.currentTooltip.parentNode) {
                    this.currentTooltip.parentNode.removeChild(this.currentTooltip);
                }
                this.currentTooltip = null;
            }, 300);
        }
    }

    // Animations
    initAnimations() {
        // Add entrance animations to elements
        const animatedElements = document.querySelectorAll('.service-card, .project-card, .team-member, .testimonial-card');
        
        animatedElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            element.style.transitionDelay = `${index * 0.1}s`;
        });
    }

    initIntersectionObserver() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            const animatedElements = document.querySelectorAll('.service-card, .project-card, .team-member, .testimonial-card');
            animatedElements.forEach(element => observer.observe(element));
        }
    }

    // Statistics animation
    animateStatistics() {
        const statNumbers = document.querySelectorAll('.stat-number');
        
        statNumbers.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    stat.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    stat.textContent = target;
                }
            };
            
            updateCounter();
        });
    }

    // Utility functions
    isElementInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Notification system
    showNotification(message, type = 'success') {
        this.notificationQueue.push({ message, type });
        if (!this.isNotificationShowing) {
            this.processNotificationQueue();
        }
    }

    processNotificationQueue() {
        if (this.notificationQueue.length === 0) {
            this.isNotificationShowing = false;
            return;
        }
        
        this.isNotificationShowing = true;
        const { message, type } = this.notificationQueue.shift();
        
        const notification = document.getElementById('notification');
        const notificationText = document.getElementById('notificationText');
        
        if (notification && notificationText) {
            notificationText.textContent = message;
            notification.className = `notification ${type}`;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    this.processNotificationQueue();
                }, 300);
            }, 3000);
        }
    }

    // Loading system
    showLoading() {
        this.isLoading = true;
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.classList.add('show');
        }
    }

    hideLoading() {
        this.isLoading = false;
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.classList.remove('show');
        }
    }

    // Chat bot functionality
    openChatBot() {
        this.showNotification('Chat feature coming soon! For now, contact us via WhatsApp.', 'info');
        
        // Simulate chat bot opening
        setTimeout(() => {
            const whatsappLink = document.querySelector('.whatsapp-contact a');
            if (whatsappLink) {
                whatsappLink.click();
            }
        }, 1000);
    }

    // Performance monitoring
    initPerformanceMonitoring() {
        // Monitor page load performance
        window.addEventListener('load', () => {
            const loadTime = performance.now();
            console.log(`Page loaded in ${Math.round(loadTime)}ms`);
            
            // Show performance notification for slow loads
            if (loadTime > 3000) {
                this.showNotification('Page loaded slower than expected. Please check your connection.', 'warning');
            }
        });
        
        // Monitor memory usage (if available)
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                if (memory.usedJSHeapSize > 50 * 1024 * 1024) { // 50MB
                    console.warn('High memory usage detected');
                }
            }, 30000);
        }
    }

    // Error handling
    handleError(error, context = 'Unknown') {
        console.error(`Error in ${context}:`, error);
        this.showNotification('Something went wrong. Please try again.', 'error');
    }

    // Accessibility enhancements
    initAccessibility() {
        // Add skip link
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.className = 'skip-link';
        skipLink.textContent = 'Skip to main content';
        document.body.insertBefore(skipLink, document.body.firstChild);
        
        // Enhance keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });
        
        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    // Social media integration (Derrick's contribution)
    initSocialFeatures() {
        const socialButtons = document.querySelectorAll('.social-btn');
        
        socialButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const platform = button.classList[1]; // facebook, twitter, etc.
                this.shareToPlatform(platform);
            });
        });
    }

    shareToPlatform(platform) {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        const description = encodeURIComponent('Join KHODERS - Where coding dreams come true!');
        
        let shareUrl = '';
        
        switch (platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                break;
            case 'instagram':
                this.showNotification('Please share our Instagram page manually!', 'info');
                return;
            case 'github':
                shareUrl = 'https://github.com/khodersworld';
                break;
        }
        
        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    }

    // Error page functionality (Addo's contribution)
    showErrorPage() {
        const errorPage = document.getElementById('errorPage');
        if (errorPage) {
            errorPage.style.display = 'flex';
        }
    }

    hideErrorPage() {
        const errorPage = document.getElementById('errorPage');
        if (errorPage) {
            errorPage.style.display = 'none';
        }
    }
}

// Global error handler
window.addEventListener('error', (e) => {
    console.error('Global error:', e.error);
});

// Global unhandled promise rejection handler
window.addEventListener('unhandledrejection', (e) => {
    console.error('Unhandled promise rejection:', e.reason);
});

// Initialize the website when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    try {
        window.khodersWebsite = new KhodersWebsite();
        console.log('KHODERS Website initialized successfully!');
    } catch (error) {
        console.error('Failed to initialize KHODERS Website:', error);
    }
});

// Global functions for backward compatibility
function hideError() {
    if (window.khodersWebsite) {
        window.khodersWebsite.hideErrorPage();
    }
}

// Service Worker registration for PWA capabilities
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = KhodersWebsite;
}