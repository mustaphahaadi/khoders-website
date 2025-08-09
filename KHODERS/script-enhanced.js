// KHODERS WORLD - Enhanced JavaScript Functionality
// Version 2.0 - Comprehensive Improvements and Modern Features

class KhodersWebsiteEnhanced {
    constructor() {
        this.version = '2.0.0';
        this.debug = false;
        this.performance = {
            startTime: performance.now(),
            metrics: {}
        };
        
        // Initialize core systems
        this.init();
        this.bindEvents();
        this.initializeComponents();
        this.initializeAdvancedFeatures();
        
        this.log('KHODERS Website Enhanced initialized successfully!');
    }

    // ===== INITIALIZATION =====
    init() {
        // Theme system
        this.currentTheme = localStorage.getItem('khoders-theme') || 'light';
        this.applyTheme(this.currentTheme);
        
        // Notification system
        this.notificationQueue = [];
        this.isNotificationShowing = false;
        
        // Loading state management
        this.isLoading = false;
        this.loadingQueue = new Set();
        
        // Scroll management
        this.lastScrollTop = 0;
        this.scrollDirection = 'down';
        this.isScrolling = false;
        
        // Animation observers
        this.intersectionObserver = null;
        this.mutationObserver = null;
        
        // Statistics animation state
        this.statsAnimated = false;
        
        // Search functionality
        this.searchIndex = [];
        this.searchResults = [];
        
        // Performance monitoring
        this.performanceObserver = null;
        
        // User preferences
        this.userPreferences = this.loadUserPreferences();
        
        // Initialize error handling
        this.initErrorHandling();
    }

    bindEvents() {
        // Core event bindings
        this.bindNavigationEvents();
        this.bindModalEvents();
        this.bindFormEvents();
        this.bindFloatingButtonEvents();
        this.bindFAQEvents();
        this.bindScrollEvents();
        this.bindThemeEvents();
        this.bindSearchEvents();
        this.bindKeyboardEvents();
        this.bindTouchEvents();
        this.bindResizeEvents();
        
        // Advanced event bindings
        this.bindVisibilityEvents();
        this.bindNetworkEvents();
        this.bindPrintEvents();
    }

    initializeComponents() {
        // Core components
        this.initSmoothScrolling();
        this.initLazyLoading();
        this.initTooltips();
        this.initAnimations();
        this.initAccessibility();
        
        // Advanced components
        this.initServiceWorker();
        this.initWebVitals();
        this.initAnalytics();
        this.initPushNotifications();
        this.initOfflineSupport();
    }

    initializeAdvancedFeatures() {
        // Modern web features
        this.initIntersectionObserver();
        this.initMutationObserver();
        this.initPerformanceObserver();
        this.initWebShare();
        this.initClipboard();
        this.initGeolocation();
        this.initDeviceOrientation();
        this.initBatteryAPI();
        this.initNetworkInformation();
        
        // Build search index
        this.buildSearchIndex();
        
        // Initialize PWA features
        this.initPWAFeatures();
    }

    // ===== ENHANCED NAVIGATION =====
    bindNavigationEvents() {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        const navLinks = document.querySelectorAll('.nav-menu a');

        if (hamburger && navMenu) {
            hamburger.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMobileMenu(hamburger, navMenu);
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                    this.closeMobileMenu(hamburger, navMenu);
                }
            });

            // Close mobile menu on navigation
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    this.closeMobileMenu(hamburger, navMenu);
                });
            });
        }

        // Enhanced navbar scroll effects
        this.initNavbarScrollEffects();
        
        // Navigation analytics
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                this.trackEvent('navigation', 'click', link.getAttribute('href'));
            });
        });
    }

    toggleMobileMenu(hamburger, navMenu) {
        const isActive = hamburger.classList.contains('active');
        
        if (isActive) {
            this.closeMobileMenu(hamburger, navMenu);
        } else {
            this.openMobileMenu(hamburger, navMenu);
        }
    }

    openMobileMenu(hamburger, navMenu) {
        hamburger.classList.add('active');
        navMenu.classList.add('active');
        hamburger.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        
        // Animate hamburger
        this.animateHamburger(hamburger, true);
        
        // Focus first menu item
        const firstMenuItem = navMenu.querySelector('a');
        if (firstMenuItem) {
            setTimeout(() => firstMenuItem.focus(), 300);
        }
        
        this.trackEvent('navigation', 'mobile_menu_open');
    }

    closeMobileMenu(hamburger, navMenu) {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        
        // Reset hamburger animation
        this.animateHamburger(hamburger, false);
        
        this.trackEvent('navigation', 'mobile_menu_close');
    }

    animateHamburger(hamburger, isActive) {
        const spans = hamburger.querySelectorAll('span');
        spans.forEach((span, index) => {
            if (isActive) {
                if (index === 0) span.style.transform = 'rotate(45deg) translate(5px, 5px)';
                if (index === 1) span.style.opacity = '0';
                if (index === 2) span.style.transform = 'rotate(-45deg) translate(7px, -6px)';
            } else {
                span.style.transform = 'none';
                span.style.opacity = '1';
            }
        });
    }

    initNavbarScrollEffects() {
        let lastScrollY = window.scrollY;
        let ticking = false;

        const updateNavbar = () => {
            const navbar = document.querySelector('.navbar');
            if (!navbar) return;

            const currentScrollY = window.scrollY;
            const scrollDifference = Math.abs(currentScrollY - lastScrollY);

            // Only update if scroll difference is significant
            if (scrollDifference < 5) return;

            if (currentScrollY > 100) {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(20px)';
                navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
                navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
            }

            // Hide/show navbar based on scroll direction
            if (currentScrollY > lastScrollY && currentScrollY > 200) {
                // Scrolling down
                navbar.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                navbar.style.transform = 'translateY(0)';
            }

            lastScrollY = currentScrollY;
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        });
    }

    // ===== ENHANCED MODAL SYSTEM =====
    bindModalEvents() {
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        const loginBtn = document.querySelector('.login-btn');
        const registerLink = document.getElementById('registerLink');
        const loginLink = document.getElementById('loginLink');
        const closeBtns = document.querySelectorAll('.close');

        // Modal triggers
        if (loginBtn && loginModal) {
            loginBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.openModal(loginModal, 'login');
            });
        }

        // Modal switching
        if (registerLink && registerModal && loginModal) {
            registerLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchModal(loginModal, registerModal, 'register');
            });
        }

        if (loginLink && loginModal && registerModal) {
            loginLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchModal(registerModal, loginModal, 'login');
            });
        }

        // Close modal events
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal');
                if (modal) this.closeModal(modal);
            });
        });

        // Close modal on backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeModal(e.target);
            }
        });

        // Enhanced keyboard navigation
        document.addEventListener('keydown', (e) => {
            const openModal = document.querySelector('.modal[style*="block"]');
            if (!openModal) return;

            switch (e.key) {
                case 'Escape':
                    this.closeModal(openModal);
                    break;
                case 'Tab':
                    this.handleModalTabNavigation(e, openModal);
                    break;
            }
        });
    }

    openModal(modal, type = 'unknown') {
        modal.style.display = 'block';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-open');
        
        // Focus management
        const firstInput = modal.querySelector('input, button');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
        
        // Animation
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.9)';
        
        requestAnimationFrame(() => {
            modal.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            modal.style.opacity = '1';
            modal.style.transform = 'scale(1)';
        });
        
        this.trackEvent('modal', 'open', type);
    }

    closeModal(modal) {
        modal.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            document.body.classList.remove('modal-open');
        }, 300);
        
        this.trackEvent('modal', 'close');
    }

    switchModal(fromModal, toModal, type) {
        this.closeModal(fromModal);
        setTimeout(() => this.openModal(toModal, type), 300);
    }

    handleModalTabNavigation(e, modal) {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === firstElement) {
                lastElement.focus();
                e.preventDefault();
            }
        } else {
            if (document.activeElement === lastElement) {
                firstElement.focus();
                e.preventDefault();
            }
        }
    }

    // ===== ENHANCED FORM HANDLING =====
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

        // Enhanced form validation
        this.initAdvancedFormValidation();
        
        // Auto-save form data
        this.initFormAutoSave();
    }

    async handleContactForm(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Validate form data
        const validation = this.validateContactForm(data);
        if (!validation.isValid) {
            this.showNotification(validation.message, 'error');
            return;
        }
        
        this.showLoading('Sending message...');
        
        try {
            // Simulate API call with realistic delay
            await this.simulateAPICall(2000);
            
            // Store in offline queue if needed
            if (!navigator.onLine) {
                await this.storeOfflineForm('contact', data);
                this.showNotification('Message saved. Will send when online.', 'info');
            } else {
                this.showNotification('Message sent successfully! We\'ll get back to you soon.', 'success');
            }
            
            form.reset();
            this.trackEvent('form', 'submit', 'contact');
            
        } catch (error) {
            this.handleError(error, 'Contact form submission');
            this.showNotification('Failed to send message. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async handleNewsletterForm(form) {
        const email = form.querySelector('input[type="email"]').value;
        
        if (!this.validateEmail(email)) {
            this.showNotification('Please enter a valid email address.', 'error');
            return;
        }
        
        this.showLoading('Subscribing...');
        
        try {
            await this.simulateAPICall(1500);
            
            // Store subscription
            this.storeNewsletterSubscription(email);
            
            this.showNotification('Successfully subscribed to our newsletter!', 'success');
            form.reset();
            this.trackEvent('form', 'submit', 'newsletter');
            
        } catch (error) {
            this.handleError(error, 'Newsletter subscription');
            this.showNotification('Subscription failed. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async handleLoginForm(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        const validation = this.validateLoginForm(data);
        if (!validation.isValid) {
            this.showNotification(validation.message, 'error');
            return;
        }
        
        this.showLoading('Signing in...');
        
        try {
            await this.simulateAPICall(2000);
            
            // Store user session
            this.storeUserSession(data.email);
            
            this.closeModal(document.getElementById('loginModal'));
            this.showNotification('Welcome back to KHODERS!', 'success');
            form.reset();
            this.trackEvent('auth', 'login', 'success');
            
        } catch (error) {
            this.handleError(error, 'Login');
            this.showNotification('Login failed. Please check your credentials.', 'error');
            this.trackEvent('auth', 'login', 'failed');
        } finally {
            this.hideLoading();
        }
    }

    async handleRegisterForm(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        const validation = this.validateRegistrationForm(data);
        if (!validation.isValid) {
            this.showNotification(validation.message, 'error');
            return;
        }
        
        this.showLoading('Creating account...');
        
        try {
            await this.simulateAPICall(2500);
            
            // Store user data
            this.storeUserSession(data.email);
            
            this.closeModal(document.getElementById('registerModal'));
            this.showNotification('Account created successfully! Welcome to KHODERS!', 'success');
            form.reset();
            this.trackEvent('auth', 'register', 'success');
            
        } catch (error) {
            this.handleError(error, 'Registration');
            this.showNotification('Registration failed. Please try again.', 'error');
            this.trackEvent('auth', 'register', 'failed');
        } finally {
            this.hideLoading();
        }
    }

    // ===== ENHANCED VALIDATION =====
    validateContactForm(data) {
        if (!data.name || data.name.trim().length < 2) {
            return { isValid: false, message: 'Please enter your full name (at least 2 characters).' };
        }
        
        if (!this.validateEmail(data.email)) {
            return { isValid: false, message: 'Please enter a valid email address.' };
        }
        
        if (!data.message || data.message.trim().length < 10) {
            return { isValid: false, message: 'Please enter a message (at least 10 characters).' };
        }
        
        return { isValid: true };
    }

    validateLoginForm(data) {
        if (!this.validateEmail(data.email)) {
            return { isValid: false, message: 'Please enter a valid email address.' };
        }
        
        if (!data.password || data.password.length < 6) {
            return { isValid: false, message: 'Password must be at least 6 characters long.' };
        }
        
        return { isValid: true };
    }

    validateRegistrationForm(data) {
        if (!data.fullName || data.fullName.trim().length < 2) {
            return { isValid: false, message: 'Please enter your full name.' };
        }
        
        if (!this.validateEmail(data.email)) {
            return { isValid: false, message: 'Please enter a valid email address.' };
        }
        
        if (!this.validatePassword(data.password)) {
            return { isValid: false, message: 'Password must be at least 8 characters with uppercase, lowercase, and number.' };
        }
        
        if (data.password !== data.confirmPassword) {
            return { isValid: false, message: 'Passwords do not match.' };
        }
        
        return { isValid: true };
    }

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    validatePassword(password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/;
        return passwordRegex.test(password);
    }

    initAdvancedFormValidation() {
        const inputs = document.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            // Real-time validation
            input.addEventListener('input', this.debounce(() => {
                this.validateFieldRealTime(input);
            }, 300));
            
            // Blur validation
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
            
            // Focus handling
            input.addEventListener('focus', () => {
                this.clearFieldError(input);
            });
        });
    }

    validateFieldRealTime(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        // Clear previous validation
        this.clearFieldError(field);

        if (field.hasAttribute('required') && !value) {
            return; // Don't show error while typing
        }

        switch (field.type) {
            case 'email':
                if (value && !this.validateEmail(value)) {
                    isValid = false;
                    message = 'Please enter a valid email address.';
                }
                break;
            case 'password':
                if (value && !this.validatePassword(value)) {
                    isValid = false;
                    message = 'Password must be at least 8 characters with uppercase, lowercase, and number.';
                }
                break;
        }

        if (!isValid) {
            this.showFieldError(field, message);
        } else if (value) {
            this.showFieldSuccess(field);
        }
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        this.clearFieldError(field);

        if (field.hasAttribute('required') && !value) {
            isValid = false;
            message = 'This field is required.';
        } else if (field.type === 'email' && value && !this.validateEmail(value)) {
            isValid = false;
            message = 'Please enter a valid email address.';
        } else if (field.type === 'password' && value && !this.validatePassword(value)) {
            isValid = false;
            message = 'Password must be at least 8 characters with uppercase, lowercase, and number.';
        }

        if (!isValid) {
            this.showFieldError(field, message);
        } else if (value) {
            this.showFieldSuccess(field);
        }

        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('error');
        field.style.borderColor = 'var(--danger)';
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.textContent = message;
        errorElement.style.cssText = `
            color: var(--danger);
            font-size: var(--font-size-sm);
            margin-top: var(--space-1);
        `;
        field.parentNode.appendChild(errorElement);
    }

    showFieldSuccess(field) {
        field.classList.remove('error');
        field.classList.add('success');
        field.style.borderColor = 'var(--success)';
    }

    clearFieldError(field) {
        field.classList.remove('error', 'success');
        field.style.borderColor = '';
        
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // ===== ENHANCED SEARCH FUNCTIONALITY =====
    bindSearchEvents() {
        const searchInput = document.querySelector('.nav-search input');
        if (!searchInput) return;

        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.performSearch(e.target.value);
            }, 300);
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.performSearch(e.target.value);
            }
        });

        // Search button
        const searchButton = document.querySelector('.nav-search button');
        if (searchButton) {
            searchButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.performSearch(searchInput.value);
            });
        }
    }

    buildSearchIndex() {
        const searchableElements = document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, li');
        this.searchIndex = [];

        searchableElements.forEach((element, index) => {
            const text = element.textContent.trim();
            if (text.length > 3) {
                this.searchIndex.push({
                    id: index,
                    element: element,
                    text: text,
                    section: this.findParentSection(element),
                    keywords: this.extractKeywords(text)
                });
            }
        });

        this.log(`Search index built with ${this.searchIndex.length} entries`);
    }

    extractKeywords(text) {
        return text.toLowerCase()
            .replace(/[^\w\s]/g, '')
            .split(/\s+/)
            .filter(word => word.length > 2)
            .slice(0, 10); // Limit keywords per entry
    }

    performSearch(query) {
        if (!query || query.length < 2) {
            this.hideSearchResults();
            return;
        }

        const results = this.searchContent(query);
        this.displaySearchResults(results, query);
        this.trackEvent('search', 'query', query);
    }

    searchContent(query) {
        const searchTerms = query.toLowerCase().split(/\s+/);
        const results = [];

        this.searchIndex.forEach(item => {
            let score = 0;
            const text = item.text.toLowerCase();
            
            searchTerms.forEach(term => {
                // Exact match in text
                if (text.includes(term)) {
                    score += 10;
                }
                
                // Keyword match
                if (item.keywords.some(keyword => keyword.includes(term))) {
                    score += 5;
                }
                
                // Title match (higher score)
                if (item.element.tagName.match(/H[1-6]/) && text.includes(term)) {
                    score += 15;
                }
            });

            if (score > 0) {
                results.push({
                    ...item,
                    score: score,
                    snippet: this.generateSnippet(item.text, searchTerms)
                });
            }
        });

        return results.sort((a, b) => b.score - a.score).slice(0, 10);
    }

    generateSnippet(text, searchTerms) {
        const maxLength = 150;
        let snippet = text;
        
        // Find first occurrence of search term
        const firstTerm = searchTerms[0];
        const index = text.toLowerCase().indexOf(firstTerm);
        
        if (index > -1) {
            const start = Math.max(0, index - 50);
            const end = Math.min(text.length, start + maxLength);
            snippet = text.substring(start, end);
            
            if (start > 0) snippet = '...' + snippet;
            if (end < text.length) snippet = snippet + '...';
        }
        
        return snippet;
    }

    displaySearchResults(results, query) {
        // Create or update search results container
        let resultsContainer = document.getElementById('searchResults');
        if (!resultsContainer) {
            resultsContainer = this.createSearchResultsContainer();
        }

        if (results.length === 0) {
            resultsContainer.innerHTML = `
                <div class="search-no-results">
                    <p>No results found for "${query}"</p>
                    <p>Try different keywords or check spelling.</p>
                </div>
            `;
        } else {
            resultsContainer.innerHTML = `
                <div class="search-header">
                    <h3>Search Results (${results.length})</h3>
                    <button class="search-close" aria-label="Close search results">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="search-results-list">
                    ${results.map(result => this.createSearchResultHTML(result, query)).join('')}
                </div>
            `;
        }

        resultsContainer.style.display = 'block';
        
        // Bind close event
        const closeBtn = resultsContainer.querySelector('.search-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.hideSearchResults());
        }
        
        // Bind result click events
        resultsContainer.querySelectorAll('.search-result').forEach(result => {
            result.addEventListener('click', (e) => {
                e.preventDefault();
                const elementId = result.dataset.elementId;
                const element = this.searchIndex[elementId]?.element;
                if (element) {
                    this.scrollToElement(element);
                    this.hideSearchResults();
                }
            });
        });
    }

    createSearchResultHTML(result, query) {
        const highlightedSnippet = this.highlightSearchTerms(result.snippet, query);
        const sectionName = result.section || 'Unknown Section';
        
        return `
            <div class="search-result" data-element-id="${result.id}">
                <div class="search-result-title">${result.element.textContent.substring(0, 100)}</div>
                <div class="search-result-snippet">${highlightedSnippet}</div>
                <div class="search-result-section">${sectionName}</div>
            </div>
        `;
    }

    highlightSearchTerms(text, query) {
        const terms = query.toLowerCase().split(/\s+/);
        let highlightedText = text;
        
        terms.forEach(term => {
            const regex = new RegExp(`(${term})`, 'gi');
            highlightedText = highlightedText.replace(regex, '<mark>$1</mark>');
        });
        
        return highlightedText;
    }

    createSearchResultsContainer() {
        const container = document.createElement('div');
        container.id = 'searchResults';
        container.className = 'search-results-container';
        container.style.cssText = `
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 600px;
            max-height: 70vh;
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-2xl);
            z-index: var(--z-modal);
            overflow: hidden;
            display: none;
        `;
        
        document.body.appendChild(container);
        return container;
    }

    hideSearchResults() {
        const resultsContainer = document.getElementById('searchResults');
        if (resultsContainer) {
            resultsContainer.style.display = 'none';
        }
    }

    findParentSection(element) {
        let parent = element.parentElement;
        while (parent && parent !== document.body) {
            if (parent.tagName === 'SECTION') {
                return parent.id || parent.className || 'Section';
            }
            parent = parent.parentElement;
        }
        return 'Unknown';
    }

    // ===== ENHANCED SCROLL HANDLING =====
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
        
        // Scroll performance monitoring
        this.initScrollPerformanceMonitoring();
    }

    handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / scrollHeight) * 100;
        
        // Update scroll direction
        this.scrollDirection = scrollTop > this.lastScrollTop ? 'down' : 'up';
        
        // Back to top button
        this.updateBackToTopButton(scrollTop);
        
        // Statistics animation
        this.handleStatsAnimation();
        
        // Parallax effects
        this.handleParallaxEffects(scrollTop);
        
        // Reading progress
        this.updateReadingProgress(scrollPercent);
        
        // Lazy loading trigger
        this.triggerLazyLoading();
        
        this.lastScrollTop = scrollTop;
    }

    updateBackToTopButton(scrollTop) {
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            if (scrollTop > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        }
    }

    handleStatsAnimation() {
        if (!this.statsAnimated) {
            const statsSection = document.querySelector('.stats-section');
            if (statsSection && this.isElementInViewport(statsSection, 0.3)) {
                this.animateStatistics();
                this.statsAnimated = true;
            }
        }
    }

    handleParallaxEffects(scrollTop) {
        const hero = document.querySelector('.hero');
        if (hero && scrollTop < window.innerHeight) {
            const parallaxSpeed = scrollTop * 0.3;
            const heroBackground = hero.querySelector('.hero-background');
            if (heroBackground) {
                heroBackground.style.transform = `translateY(${parallaxSpeed}px)`;
            }
        }
    }

    updateReadingProgress(percent) {
        // Create reading progress bar if it doesn't exist
        let progressBar = document.getElementById('readingProgress');
        if (!progressBar) {
            progressBar = document.createElement('div');
            progressBar.id = 'readingProgress';
            progressBar.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: ${percent}%;
                height: 3px;
                background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
                z-index: var(--z-fixed);
                transition: width 0.1s ease;
            `;
            document.body.appendChild(progressBar);
        } else {
            progressBar.style.width = `${percent}%`;
        }
    }

    initScrollPerformanceMonitoring() {
        let scrollCount = 0;
        let scrollStartTime = 0;
        
        window.addEventListener('scroll', () => {
            if (scrollCount === 0) {
                scrollStartTime = performance.now();
            }
            scrollCount++;
            
            // Measure scroll performance every 100 scroll events
            if (scrollCount % 100 === 0) {
                const scrollDuration = performance.now() - scrollStartTime;
                const scrollFPS = 1000 / (scrollDuration / scrollCount);
                
                if (scrollFPS < 30) {
                    this.log('Scroll performance warning: Low FPS detected', 'warn');
                }
                
                scrollCount = 0;
            }
        });
    }

    // ===== ENHANCED ANIMATIONS =====
    animateStatistics() {
        const statNumbers = document.querySelectorAll('.stat-number');
        
        statNumbers.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target')) || parseInt(stat.textContent);
            const duration = 2000;
            const startTime = performance.now();
            
            const updateCounter = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function for smooth animation
                const easeOutCubic = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(target * easeOutCubic);
                
                stat.textContent = current.toLocaleString();
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    stat.textContent = target.toLocaleString();
                }
            };
            
            requestAnimationFrame(updateCounter);
        });
        
        this.trackEvent('animation', 'stats_animated');
    }

    initIntersectionObserver() {
        if (!('IntersectionObserver' in window)) return;

        const options = {
            threshold: [0.1, 0.3, 0.5, 0.7, 0.9],
            rootMargin: '0px 0px -50px 0px'
        };

        this.intersectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.handleElementInView(entry.target, entry.intersectionRatio);
                }
            });
        }, options);

        // Observe animated elements
        const animatedElements = document.querySelectorAll(
            '.service-card, .project-card, .team-member, .testimonial-card, .blog-card, .stat-item'
        );
        
        animatedElements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            this.intersectionObserver.observe(element);
        });
    }

    handleElementInView(element, ratio) {
        if (ratio > 0.1) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
            
            // Add staggered animation delay for grouped elements
            const siblings = Array.from(element.parentNode.children);
            const index = siblings.indexOf(element);
            element.style.transitionDelay = `${index * 0.1}s`;
            
            this.intersectionObserver.unobserve(element);
        }
    }

    // ===== ENHANCED THEME SYSTEM =====
    bindThemeEvents() {
        // Theme toggle buttons
        const themeToggles = document.querySelectorAll('#themeToggle, #themeToggleNav');
        themeToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        });

        // System theme detection
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', (e) => {
            if (!localStorage.getItem('khoders-theme')) {
                this.applyTheme(e.matches ? 'dark' : 'light');
            }
        });

        // Auto theme based on time
        this.initAutoTheme();
    }

    toggleTheme() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
        this.showNotification(`Switched to ${newTheme} theme`, 'success');
        this.trackEvent('theme', 'toggle', newTheme);
    }

    applyTheme(theme) {
        this.currentTheme = theme;
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('khoders-theme', theme);
        
        // Update theme toggle icons
        const themeToggles = document.querySelectorAll('#themeToggle i, #themeToggleNav i');
        themeToggles.forEach(icon => {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });
        
        // Update meta theme-color
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.content = theme === 'dark' ? '#1a1a1a' : '#2A4E6D';
        }
    }

    initAutoTheme() {
        const hour = new Date().getHours();
        const isNightTime = hour < 6 || hour > 18;
        
        if (!localStorage.getItem('khoders-theme') && isNightTime) {
            this.applyTheme('dark');
        }
    }

    // ===== ENHANCED NOTIFICATION SYSTEM =====
    showNotification(message, type = 'success', duration = 4000) {
        const notification = {
            id: Date.now(),
            message,
            type,
            duration,
            timestamp: new Date()
        };
        
        this.notificationQueue.push(notification);
        
        if (!this.isNotificationShowing) {
            this.processNotificationQueue();
        }
        
        // Store notification in history
        this.storeNotificationHistory(notification);
    }

    processNotificationQueue() {
        if (this.notificationQueue.length === 0) {
            this.isNotificationShowing = false;
            return;
        }
        
        this.isNotificationShowing = true;
        const notification = this.notificationQueue.shift();
        
        this.displayNotification(notification);
    }

    displayNotification(notification) {
        const notificationElement = document.getElementById('notification');
        const notificationText = document.getElementById('notificationText');
        
        if (!notificationElement || !notificationText) {
            this.log('Notification elements not found', 'warn');
            return;
        }
        
        // Set notification content
        notificationText.textContent = notification.message;
        notificationElement.className = `notification ${notification.type}`;
        
        // Add close button functionality
        const closeBtn = notificationElement.querySelector('.notification-close');
        if (closeBtn) {
            closeBtn.onclick = () => this.hideNotification(notificationElement);
        }
        
        // Show notification
        notificationElement.classList.add('show');
        
        // Auto-hide after duration
        setTimeout(() => {
            this.hideNotification(notificationElement);
        }, notification.duration);
        
        // Accessibility announcement
        this.announceToScreenReader(notification.message);
    }

    hideNotification(notificationElement) {
        notificationElement.classList.remove('show');
        
        setTimeout(() => {
            this.processNotificationQueue();
        }, 300);
    }

    announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    storeNotificationHistory(notification) {
        const history = JSON.parse(localStorage.getItem('khoders-notifications') || '[]');
        history.unshift(notification);
        
        // Keep only last 50 notifications
        if (history.length > 50) {
            history.splice(50);
        }
        
        localStorage.setItem('khoders-notifications', JSON.stringify(history));
    }

    // ===== ENHANCED LOADING SYSTEM =====
    showLoading(message = 'Loading...') {
        this.loadingQueue.add(message);
        this.updateLoadingDisplay();
    }

    hideLoading(message = null) {
        if (message) {
            this.loadingQueue.delete(message);
        } else {
            this.loadingQueue.clear();
        }
        this.updateLoadingDisplay();
    }

    updateLoadingDisplay() {
        const spinner = document.getElementById('loadingSpinner');
        const spinnerText = spinner?.querySelector('p');
        
        if (this.loadingQueue.size > 0) {
            this.isLoading = true;
            if (spinner) {
                spinner.classList.add('show');
                if (spinnerText) {
                    spinnerText.textContent = Array.from(this.loadingQueue)[0];
                }
            }
        } else {
            this.isLoading = false;
            if (spinner) {
                spinner.classList.remove('show');
            }
        }
    }

    // ===== UTILITY FUNCTIONS =====
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    isElementInViewport(element, threshold = 0.1) {
        const rect = element.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        const windowWidth = window.innerWidth || document.documentElement.clientWidth;
        
        const verticalVisible = (rect.top <= windowHeight * (1 - threshold)) && 
                               ((rect.top + rect.height) >= windowHeight * threshold);
        const horizontalVisible = (rect.left <= windowWidth * (1 - threshold)) && 
                                 ((rect.left + rect.width) >= windowWidth * threshold);
        
        return verticalVisible && horizontalVisible;
    }

    scrollToElement(element, offset = 80) {
        const elementTop = element.offsetTop - offset;
        
        window.scrollTo({
            top: elementTop,
            behavior: 'smooth'
        });
        
        // Highlight element briefly
        element.style.transition = 'background-color 0.3s ease';
        element.style.backgroundColor = 'rgba(241, 181, 33, 0.2)';
        
        setTimeout(() => {
            element.style.backgroundColor = '';
        }, 2000);
    }

    simulateAPICall(delay = 1000) {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Simulate occasional failures
                if (Math.random() < 0.1) {
                    reject(new Error('Simulated API failure'));
                } else {
                    resolve({ success: true });
                }
            }, delay);
        });
    }

    // ===== STORAGE UTILITIES =====
    storeUserSession(email) {
        const session = {
            email,
            loginTime: new Date().toISOString(),
            sessionId: this.generateSessionId()
        };
        
        localStorage.setItem('khoders-session', JSON.stringify(session));
        sessionStorage.setItem('khoders-active', 'true');
    }

    storeNewsletterSubscription(email) {
        const subscriptions = JSON.parse(localStorage.getItem('khoders-subscriptions') || '[]');
        subscriptions.push({
            email,
            subscribedAt: new Date().toISOString()
        });
        
        localStorage.setItem('khoders-subscriptions', JSON.stringify(subscriptions));
    }

    async storeOfflineForm(type, data) {
        const offlineData = JSON.parse(localStorage.getItem('khoders-offline-forms') || '[]');
        offlineData.push({
            type,
            data,
            timestamp: new Date().toISOString(),
            id: this.generateId()
        });
        
        localStorage.setItem('khoders-offline-forms', JSON.stringify(offlineData));
    }

    loadUserPreferences() {
        return JSON.parse(localStorage.getItem('khoders-preferences') || '{}');
    }

    saveUserPreferences(preferences) {
        const current = this.loadUserPreferences();
        const updated = { ...current, ...preferences };
        localStorage.setItem('khoders-preferences', JSON.stringify(updated));
        this.userPreferences = updated;
    }

    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    // ===== ERROR HANDLING =====
    initErrorHandling() {
        window.addEventListener('error', (e) => {
            this.handleError(e.error, 'Global error');
        });

        window.addEventListener('unhandledrejection', (e) => {
            this.handleError(e.reason, 'Unhandled promise rejection');
        });
    }

    handleError(error, context = 'Unknown') {
        const errorInfo = {
            message: error.message || error,
            stack: error.stack,
            context,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            url: window.location.href
        };
        
        this.log(`Error in ${context}:`, 'error', errorInfo);
        
        // Store error for debugging
        this.storeError(errorInfo);
        
        // Show user-friendly message
        if (context !== 'Global error') {
            this.showNotification('Something went wrong. Please try again.', 'error');
        }
        
        // Track error
        this.trackEvent('error', context, error.message);
    }

    storeError(errorInfo) {
        const errors = JSON.parse(localStorage.getItem('khoders-errors') || '[]');
        errors.unshift(errorInfo);
        
        // Keep only last 20 errors
        if (errors.length > 20) {
            errors.splice(20);
        }
        
        localStorage.setItem('khoders-errors', JSON.stringify(errors));
    }

    // ===== ANALYTICS AND TRACKING =====
    trackEvent(category, action, label = null, value = null) {
        if (this.debug) {
            this.log(`Track Event: ${category} - ${action}${label ? ` - ${label}` : ''}${value ? ` - ${value}` : ''}`);
        }
        
        // Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                event_category: category,
                event_label: label,
                value: value
            });
        }
        
        // Custom analytics
        this.storeAnalyticsEvent({
            category,
            action,
            label,
            value,
            timestamp: new Date().toISOString(),
            page: window.location.pathname
        });
    }

    storeAnalyticsEvent(event) {
        const events = JSON.parse(localStorage.getItem('khoders-analytics') || '[]');
        events.push(event);
        
        // Keep only last 1000 events
        if (events.length > 1000) {
            events.splice(0, events.length - 1000);
        }
        
        localStorage.setItem('khoders-analytics', JSON.stringify(events));
    }

    // ===== LOGGING =====
    log(message, level = 'info', data = null) {
        if (!this.debug && level === 'debug') return;
        
        const timestamp = new Date().toISOString();
        const logMessage = `[${timestamp}] [${level.toUpperCase()}] ${message}`;
        
        switch (level) {
            case 'error':
                console.error(logMessage, data);
                break;
            case 'warn':
                console.warn(logMessage, data);
                break;
            case 'debug':
                console.debug(logMessage, data);
                break;
            default:
                console.log(logMessage, data);
        }
    }

    // ===== PUBLIC API =====
    getVersion() {
        return this.version;
    }

    getPerformanceMetrics() {
        return {
            ...this.performance.metrics,
            uptime: performance.now() - this.performance.startTime
        };
    }

    enableDebugMode() {
        this.debug = true;
        this.log('Debug mode enabled');
    }

    disableDebugMode() {
        this.debug = false;
    }
}

// Initialize the enhanced website when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    try {
        window.khodersWebsite = new KhodersWebsiteEnhanced();
    } catch (error) {
        console.error('Failed to initialize KHODERS Website Enhanced:', error);
        
        // Fallback to basic functionality
        document.body.classList.add('js-error');
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = KhodersWebsiteEnhanced;
}