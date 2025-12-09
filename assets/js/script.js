// EventHub JavaScript
// Clean, Functional Event Management System

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the application
    initApp();
});

function initApp() {
    // Initialize login tabs
    initLoginTabs();
    
    // Initialize form validation
    initFormValidation();
    
    // Initialize search functionality
    initSearch();
    
    // Initialize filters
    initFilters();
    
    // Initialize event booking
    initEventBooking();
    
    // Initialize order management
    initOrderManagement();
}

// Login Tab Functionality
function initLoginTabs() {
    const tabs = document.querySelectorAll('.login-tab');
    const tabContents = document.querySelectorAll('.login-tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
        
        // Email validation
        if (field.type === 'email' && field.value.trim()) {
            if (!isValidEmail(field.value)) {
                showFieldError(field, 'Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Password validation
        if (field.type === 'password' && field.value.trim()) {
            if (field.value.length < 6) {
                showFieldError(field, 'Password must be at least 6 characters long');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
    field.classList.add('error');
}

function clearFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
    field.classList.remove('error');
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Search Functionality
function initSearch() {
    const searchInput = document.querySelector('#search-input');
    const searchResults = document.querySelector('#search-results');
    
    if (searchInput && searchResults) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length > 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query, searchResults);
                }, 300);
            } else {
                searchResults.innerHTML = '';
                searchResults.style.display = 'none';
            }
        });
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }
}

function performSearch(query, searchResults) {
    // Show loading state
    searchResults.innerHTML = '<div style="padding: 1rem; text-align: center;">Searching...</div>';
    searchResults.style.display = 'block';
    
    // Simulate search (in real app, this would be an AJAX call)
    setTimeout(() => {
        const mockResults = [
            { title: 'Tech Conference 2024', type: 'Conference', location: 'Bangalore' },
            { title: 'Music Festival', type: 'Festival', location: 'Mumbai' },
            { title: 'Business Summit', type: 'Summit', location: 'Delhi' }
        ];
        
        const filteredResults = mockResults.filter(result => 
            result.title.toLowerCase().includes(query.toLowerCase()) ||
            result.type.toLowerCase().includes(query.toLowerCase()) ||
            result.location.toLowerCase().includes(query.toLowerCase())
        );
        
        if (filteredResults.length > 0) {
            searchResults.innerHTML = filteredResults.map(result => `
                <div class="search-result-item">
                    <h4>${result.title}</h4>
                    <p>${result.type} • ${result.location}</p>
                </div>
            `).join('');
        } else {
            searchResults.innerHTML = '<div style="padding: 1rem; text-align: center; color: #6b7280;">No events found</div>';
        }
    }, 200);
}

// Filters
function initFilters() {
    const filterForm = document.querySelector('#filter-form');
    const clearFiltersBtn = document.querySelector('#clear-filters');
    
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            clearFilters();
        });
    }
}

function applyFilters() {
    const eventType = document.querySelector('#event-type')?.value || '';
    const minPrice = document.querySelector('#min-price')?.value || '';
    const maxPrice = document.querySelector('#max-price')?.value || '';
    const organizer = document.querySelector('#organizer')?.value || '';
    
    // Show loading state
    const eventsContainer = document.querySelector('.events-grid');
    if (eventsContainer) {
        eventsContainer.style.opacity = '0.7';
        
        // Simulate filter application (in real app, this would be an AJAX call)
        setTimeout(() => {
            eventsContainer.style.opacity = '1';
            showNotification('Filters applied successfully!', 'success');
        }, 300);
    }
}

function clearFilters() {
    const filterForm = document.querySelector('#filter-form');
    if (filterForm) {
        filterForm.reset();
        applyFilters();
    }
}

// Notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        max-width: 300px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Order Management
function initOrderManagement() {
    const acceptButtons = document.querySelectorAll('.accept-order');
    const rejectButtons = document.querySelectorAll('.reject-order');
    
    acceptButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            updateOrderStatus(orderId, 'confirmed');
        });
    });
    
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            updateOrderStatus(orderId, 'cancelled');
        });
    });
}

function updateOrderStatus(orderId, status) {
    // Show loading state
    const button = document.querySelector(`[data-order-id="${orderId}"]`);
    if (button) {
        button.disabled = true;
        button.textContent = 'Processing...';
        
        // Make actual API call
        fetch('../api/handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': status === 'confirmed' ? 'accept' : 'reject',
                'order_id': orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const orderRow = document.querySelector(`[data-order-row="${orderId}"]`);
                if (orderRow) {
                    const statusBadge = orderRow.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                        statusBadge.className = `status-badge status-${status}`;
                    }
                    
                    // Hide action buttons
                    const actionButtons = orderRow.querySelector('.order-actions');
                    if (actionButtons) {
                        actionButtons.innerHTML = `<span style="color: var(--success-color); font-weight: 600;">Updated</span>`;
                    }
                }
                
                showNotification(`Order ${status} successfully!`, 'success');
                
                // Refresh the page after a short delay to update the pending orders count
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification('Failed to update order status', 'error');
                button.disabled = false;
                button.textContent = status === 'confirmed' ? 'Accept' : 'Reject';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error occurred', 'error');
            button.disabled = false;
            button.textContent = status === 'confirmed' ? 'Accept' : 'Reject';
        });
    }
}

// Event Booking
function initEventBooking() {
    const bookButtons = document.querySelectorAll('.book-event');
    
    bookButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            if (eventId) {
                window.location.href = `booking-detail.php?event_id=${eventId}`;
            }
        });
    });
}

// Utility Functions
function formatPrice(price) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 2
    }).format(price);
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-IN', options);
}

function debounce(func, wait) {
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

// Export functions for global use
window.EventHub = {
    showNotification,
    formatPrice,
    formatDate,
    debounce
};