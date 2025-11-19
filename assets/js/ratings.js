/**
 * Ratings & Reviews System - JavaScript
 * Interactive star rating widgets and AJAX submission
 */

(function () {
    'use strict';

    // Star Rating Display Widget
    window.RatingDisplay = {
        render: function (container, rating, count) {
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;
            let html = '<div class="rating-display">';

            // Stars
            html += '<div class="stars">';
            for (let i = 0; i < fullStars; i++) {
                html += '<i class="bi bi-star-fill text-warning"></i>';
            }
            if (hasHalfStar) {
                html += '<i class="bi bi-star-half text-warning"></i>';
            }
            for (let i = fullStars + (hasHalfStar ? 1 : 0); i < 5; i++) {
                html += '<i class="bi bi-star text-muted"></i>';
            }
            html += '</div>';

            // Rating value and count
            html += `<span class="rating-value">${rating.toFixed(1)}</span>`;
            if (count) {
                html += `<span class="rating-count">(${count} ${count === 1 ? 'rating' : 'ratings'})</span>`;
            }
            html += '</div>';

            container.innerHTML = html;
        }
    };

    // Star Rating Input Widget
    window.RatingInput = {
        init: function (container, onRatingChange) {
            let selectedRating = 0;
            container.classList.add('rating-input');
            container.innerHTML = '';

            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('span');
                star.className = 'star';
                star.dataset.value = i;
                star.innerHTML = '☆';

                // Hover effect
                star.addEventListener('mouseenter', function () {
                    highlightStars(i);
                });

                // Click to select
                star.addEventListener('click', function () {
                    selectedRating = i;
                    if (onRatingChange) onRatingChange(i);
                });

                container.appendChild(star);
            }

            // Reset on mouse leave
            container.addEventListener('mouseleave', function () {
                highlightStars(selectedRating);
            });

            function highlightStars(count) {
                const stars = container.querySelectorAll('.star');
                stars.forEach((star, index) => {
                    star.innerHTML = index < count ? '★' : '☆';
                    star.classList.toggle('active', index < count);
                });
            }

            return {
                getValue: () => selectedRating,
                setValue: (value) => {
                    selectedRating = value;
                    highlightStars(value);
                }
            };
        }
    };

    // Rating Form Handler
    window.RatingForm = {
        init: function (formElement, options) {
            const ratingInputContainer = formElement.querySelector('[data-rating-input]');
            const reviewTextarea = formElement.querySelector('[name="review"]');
            const anonymousCheckbox = formElement.querySelector('[name="is_anonymous"]');
            const submitButton = formElement.querySelector('[type="submit"]');

            const ratingWidget = RatingInput.init(ratingInputContainer, function (rating) {
                // Enable submit when rating selected
                if (submitButton) {
                    submitButton.disabled = false;
                }
            });

            formElement.addEventListener('submit', function (e) {
                e.preventDefault();

                const rating = ratingWidget.getValue();
                if (rating === 0) {
                    alert('Please select a rating');
                    return;
                }

                const formData = new FormData();
                formData.append('csrf_token', options.csrfToken);
                formData.append('rateable_type', options.type);
                formData.append('rateable_id', options.id);
                formData.append('rating', rating);
                formData.append('review', reviewTextarea ? reviewTextarea.value : '');
                if (anonymousCheckbox && anonymousCheckbox.checked) {
                    formData.append('is_anonymous', '1');
                }

                // Disable submit button
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';

                fetch('/api/submit-rating.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showMessage('success', data.message);

                            // Reset form
                            formElement.reset();
                            ratingWidget.setValue(0);

                            // Reload ratings if callback provided
                            if (options.onSuccess) {
                                options.onSuccess(data);
                            }
                        } else {
                            showMessage('error', data.message);
                            submitButton.disabled = false;
                            submitButton.textContent = 'Submit Review';
                        }
                    })
                    .catch(error => {
                        showMessage('error', 'An error occurred. Please try again.');
                        submitButton.disabled = false;
                        submitButton.textContent = 'Submit Review';
                    });
            });

            function showMessage(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alertIcon = type === 'success' ? 'check-circle' : 'exclamation-circle';

                const alert = document.createElement('div');
                alert.className = `alert ${alertClass} alert-dismissible fade show`;
                alert.innerHTML = `
                    <i class="bi bi-${alertIcon}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                formElement.insertBefore(alert, formElement.firstChild);

                // Auto dismiss after 5 seconds
                setTimeout(() => alert.remove(), 5000);
            }
        }
    };

    // Load ratings for display
    window.loadRatings = function (type, id, container, options = {}) {
        const page = options.page || 1;
        const limit = options.limit || 10;

        fetch(`/api/get-ratings.php?type=${type}&id=${id}&page=${page}&limit=${limit}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderRatings(container, data.data, options);
                }
            })
            .catch(error => {
                console.error('Error loading ratings:', error);
            });
    };

    function renderRatings(container, data, options) {
        let html = '';

        // Statistics
        if (options.showStatistics !== false) {
            html += `
                <div class="rating-statistics mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h2 class="display-4 mb-0">${data.statistics.average_rating}</h2>
                                <div class="mb-2">`;

            const fullStars = Math.floor(data.statistics.average_rating);
            for (let i = 0; i < fullStars; i++) {
                html += '<i class="bi bi-star-fill text-warning"></i>';
            }
            for (let i = fullStars; i < 5; i++) {
                html += '<i class="bi bi-star text-muted"></i>';
            }

            html += `</div>
                                <p class="text-muted">${data.statistics.total_ratings} reviews</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            ${renderRatingDistribution(data.statistics.distribution, data.statistics.total_ratings)}
                        </div>
                    </div>
                </div>
            `;
        }

        // Reviews list
        html += '<div class="reviews-list">';
        if (data.ratings.length === 0) {
            html += '<p class="text-muted text-center py-4">No reviews yet. Be the first to review!</p>';
        } else {
            data.ratings.forEach(rating => {
                html += renderReview(rating);
            });
        }
        html += '</div>';

        // Pagination
        if (data.pagination.total_pages > 1) {
            html += renderPagination(data.pagination, options);
        }

        container.innerHTML = html;
    }

    function renderRatingDistribution(distribution, total) {
        let html = '<div class="rating-distribution">';
        for (let stars = 5; stars >= 1; stars--) {
            const count = distribution[`${stars}_star${stars !== 1 ? 's' : ''}`] || 0;
            const percentage = total > 0 ? (count / total * 100) : 0;

            html += `
                <div class="d-flex align-items-center mb-2">
                    <span class="me-2">${stars} <i class="bi bi-star-fill text-warning"></i></span>
                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: ${percentage}%"></div>
                    </div>
                    <span class="text-muted small">${count}</span>
                </div>
            `;
        }
        html += '</div>';
        return html;
    }

    function renderReview(rating) {
        return `
            <div class="review-item mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <strong>${rating.member_name}</strong>
                        <div class="review-stars">
                            ${renderStars(rating.rating)}
                        </div>
                    </div>
                    <small class="text-muted">${rating.created_at}</small>
                </div>
                ${rating.review ? `<p class="mb-0">${escapeHtml(rating.review)}</p>` : ''}
            </div>
        `;
    }

    function renderStars(count) {
        let html = '';
        for (let i = 0; i < count; i++) {
            html += '<i class="bi bi-star-fill text-warning"></i>';
        }
        for (let i = count; i < 5; i++) {
            html += '<i class="bi bi-star text-muted"></i>';
        }
        return html;
    }

    function renderPagination(pagination, options) {
        let html = '<nav class="mt-4"><ul class="pagination justify-content-center">';
        for (let i = 1; i <= pagination.total_pages; i++) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }
        html += '</ul></nav>';
        return html;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

})();
