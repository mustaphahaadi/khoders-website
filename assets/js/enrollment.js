/**
 * Enrollment Handler - Khoders World
 * JavaScript for handling event registration and course enrollment
 */

class EnrollmentHandler {
    constructor() {
        this.init();
    }

    init() {
        // Bind event registration buttons
        document.querySelectorAll('.btn-register-event').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleEventRegistration(e));
        });

        // Bind course enrollment buttons
        document.querySelectorAll('.btn-enroll-course').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleCourseEnrollment(e));
        });

        // Bind program enrollment buttons
        document.querySelectorAll('.btn-enroll-program').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleProgramEnrollment(e));
        });
    }

    async handleEventRegistration(e) {
        e.preventDefault();
        const btn = e.target.closest('.btn-register-event');
        const eventId = btn.dataset.eventId;
        const csrfToken = btn.dataset.csrf || document.querySelector('input[name="csrf_token"]')?.value;

        await this.enroll('api/register-event.php', { event_id: eventId }, csrfToken, btn, 'event');
    }

    async handleCourseEnrollment(e) {
        e.preventDefault();
        const btn = e.target.closest('.btn-enroll-course');
        const courseId = btn.dataset.courseId;
        const csrfToken = btn.dataset.csrf || document.querySelector('input[name="csrf_token"]')?.value;

        await this.enroll('api/enroll-course.php', { course_id: courseId }, csrfToken, btn, 'course');
    }

    async handleProgramEnrollment(e) {
        e.preventDefault();
        const btn = e.target.closest('.btn-enroll-program');
        const programId = btn.dataset.programId;
        const csrfToken = btn.dataset.csrf || document.querySelector('input[name="csrf_token"]')?.value;

        await this.enroll('api/enroll-program.php', { program_id: programId }, csrfToken, btn, 'program');
    }

    async enroll(url, data, csrfToken, btn, type) {
        // Disable button
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';

        try {
            // Add CSRF token
            const formData = new FormData();
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }
            formData.append('csrf_token', csrfToken);

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                this.showMessage('success', result.message);
                
                // Update button
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Registered';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                
                // Optionally redirect to dashboard after 2 seconds
                setTimeout(() => {
                    window.location.href = 'index.php?page=dashboard';
                }, 2000);
            } else {
                // Show error
                this.showMessage('error', result.message);
                
                // If not logged in, redirect to login
                if (result.message.includes('login')) {
                    setTimeout(() => {
                        window.location.href = 'index.php?page=member-login';
                    }, 2000);
                } else {
                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }
        } catch (error) {
            console.error('Enrollment error:', error);
            this.showMessage('error', 'An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    showMessage(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.enrollment-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'} enrollment-alert alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert at top of main content
        const mainContent = document.querySelector('main .container') || document.querySelector('main');
        if (mainContent) {
            mainContent.insertBefore(alert, mainContent.firstChild);
            
            // Scroll to alert
            alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new EnrollmentHandler());
} else {
    new EnrollmentHandler();
}
