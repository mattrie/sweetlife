// SweetLife Hotel Management System - Frontend JavaScript

class HotelBookingSystem {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadRooms();
    }

    bindEvents() {
        // Login form
        document.getElementById('loginForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleLogin(e.target);
        });

        // Register form
        document.getElementById('registerForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleRegister(e.target);
        });

        // Booking form
        document.getElementById('bookingForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleBooking(e.target);
        });

        // Newsletter form
        document.getElementById('newsletterForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleNewsletter(e.target);
        });

        // Room selection
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-select-room')) {
                this.selectRoom(e.target.dataset.roomId);
            }
        });
    }

    async loadRooms(type = null) {
        try {
            const url = type ? `api/rooms.php?type=${type}` : 'api/rooms.php';
            const response = await fetch(url);
            const rooms = await response.json();
            
            this.displayRooms(rooms);
        } catch (error) {
            console.error('Error loading rooms:', error);
            this.showAlert('Error loading rooms. Please try again.', 'danger');
        }
    }

    displayRooms(rooms) {
        const container = document.getElementById('roomsContainer');
        if (!container) return;

        container.innerHTML = '';

        rooms.forEach(room => {
            const images = room.images || [];
            const mainImage = images.length > 0 ? images[0] : 'images/room1.jpg';
            
            const roomCard = `
                <div class="col-md-4 col-sm-6">
                    <div class="room-card">
                        <div class="room_img">
                            <figure><img src="${mainImage}" alt="${room.category_name}" style="width: 100%; height: 250px; object-fit: cover;"/></figure>
                        </div>
                        <div class="bed_room" style="padding: 20px;">
                            <h3>${room.category_name}</h3>
                            <p class="room-price">₦${parseFloat(room.price_per_night).toLocaleString()}/night</p>
                            <p><strong>Room:</strong> ${room.room_number}</p>
                            <p><strong>Capacity:</strong> ${room.max_adults} Adults, ${room.max_children} Children</p>
                            <p>${room.description || 'Comfortable accommodation with modern amenities'}</p>
                            <button class="btn btn-book btn-select-room" data-room-id="${room.id}">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.innerHTML += roomCard;
        });
    }

    async selectRoom(roomId) {
        try {
            const response = await fetch(`api/rooms.php?id=${roomId}`);
            const room = await response.json();
            
            if (room) {
                this.showBookingModal(room);
            }
        } catch (error) {
            console.error('Error loading room details:', error);
            this.showAlert('Error loading room details. Please try again.', 'danger');
        }
    }

    showBookingModal(room) {
        const modalHtml = `
            <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Book ${room.category_name} - Room ${room.room_number}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="${room.images?.[0] || 'images/room1.jpg'}" alt="${room.category_name}" class="img-fluid" style="border-radius: 10px;">
                                    <h4 class="mt-3">₦${parseFloat(room.price_per_night).toLocaleString()}/night</h4>
                                    <p><strong>Capacity:</strong> ${room.max_adults} Adults, ${room.max_children} Children</p>
                                    <p>${room.description}</p>
                                </div>
                                <div class="col-md-6">
                                    <form id="bookingForm">
                                        <input type="hidden" name="room_id" value="${room.id}">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" class="form-control" name="user_name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="user_email" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="tel" class="form-control" name="user_phone" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Check-in Date</label>
                                                    <input type="date" class="form-control" name="check_in_date" required min="${new Date().toISOString().split('T')[0]}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Check-out Date</label>
                                                    <input type="date" class="form-control" name="check_out_date" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Adults</label>
                                                    <select class="form-control" name="adults" required>
                                                        ${Array.from({length: room.max_adults}, (_, i) => 
                                                            `<option value="${i + 1}">${i + 1}</option>`
                                                        ).join('')}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Children</label>
                                                    <select class="form-control" name="children">
                                                        ${Array.from({length: room.max_children + 1}, (_, i) => 
                                                            `<option value="${i}">${i}</option>`
                                                        ).join('')}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Special Requests (Optional)</label>
                                            <textarea class="form-control" name="special_requests" rows="3" placeholder="Any special requests or requirements..."></textarea>
                                        </div>
                                        <div id="bookingTotal" class="alert alert-info" style="display: none;">
                                            <strong>Total: ₦<span id="totalAmount">0</span></strong>
                                            <br><small><span id="totalNights">0</span> nights</small>
                                        </div>
                                        <button type="submit" class="btn btn-book btn-block">
                                            Proceed to Payment
                                        </button>
                                    </form>
                                    <div class="loading" id="bookingLoading">
                                        <div class="spinner"></div>
                                        <p>Processing booking...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal
        const existingModal = document.getElementById('bookingModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add new modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal
        $('#bookingModal').modal('show');
        
        // Bind date change events
        this.bindDateChangeEvents(room);
    }

    bindDateChangeEvents(room) {
        const checkInInput = document.querySelector('#bookingModal input[name="check_in_date"]');
        const checkOutInput = document.querySelector('#bookingModal input[name="check_out_date"]');
        
        const updateTotal = () => {
            const checkIn = checkInInput.value;
            const checkOut = checkOutInput.value;
            
            if (checkIn && checkOut) {
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    const total = nights * parseFloat(room.price_per_night);
                    document.getElementById('totalNights').textContent = nights;
                    document.getElementById('totalAmount').textContent = total.toLocaleString();
                    document.getElementById('bookingTotal').style.display = 'block';
                    
                    // Check availability
                    this.checkAvailability(room.id, checkIn, checkOut);
                }
            }
        };

        checkInInput.addEventListener('change', () => {
            // Set minimum checkout date to day after checkin
            const checkInDate = new Date(checkInInput.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            checkOutInput.min = checkInDate.toISOString().split('T')[0];
            updateTotal();
        });

        checkOutInput.addEventListener('change', updateTotal);
    }

    async checkAvailability(roomId, checkIn, checkOut) {
        try {
            const formData = new FormData();
            formData.append('action', 'check_availability');
            formData.append('room_id', roomId);
            formData.append('check_in', checkIn);
            formData.append('check_out', checkOut);

            const response = await fetch('api/rooms.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (!result.available) {
                this.showAlert('Room not available for selected dates. Please choose different dates.', 'warning');
                document.querySelector('#bookingModal button[type="submit"]').disabled = true;
            } else {
                document.querySelector('#bookingModal button[type="submit"]').disabled = false;
            }
        } catch (error) {
            console.error('Error checking availability:', error);
        }
    }

    async handleBooking(form) {
        const formData = new FormData(form);
        formData.append('action', 'create_booking');
        
        const loadingDiv = document.getElementById('bookingLoading');
        loadingDiv.style.display = 'block';
        form.style.display = 'none';

        try {
            const response = await fetch('api/bookings.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Initialize payment
                this.initializePayment(result.booking_id, result.total_amount, formData.get('user_email'));
            } else {
                this.showAlert(result.message || 'Booking failed. Please try again.', 'danger');
                loadingDiv.style.display = 'none';
                form.style.display = 'block';
            }
        } catch (error) {
            console.error('Error creating booking:', error);
            this.showAlert('Error creating booking. Please try again.', 'danger');
            loadingDiv.style.display = 'none';
            form.style.display = 'block';
        }
    }

    async initializePayment(bookingId, amount, email) {
        try {
            const formData = new FormData();
            formData.append('action', 'initialize_payment');
            formData.append('booking_id', bookingId);

            const response = await fetch('api/payments.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                const handler = PaystackPop.setup({
                    key: 'pk_test_your_public_key_here', // Replace with your Paystack public key
                    email: email,
                    amount: amount * 100, // Convert to kobo
                    ref: result.reference,
                    callback: (response) => {
                        this.verifyPayment(response.reference);
                    },
                    onClose: () => {
                        this.showAlert('Payment cancelled', 'warning');
                        $('#bookingModal').modal('hide');
                    }
                });
                
                handler.openIframe();
            } else {
                this.showAlert('Payment initialization failed. Please try again.', 'danger');
            }
        } catch (error) {
            console.error('Error initializing payment:', error);
            this.showAlert('Payment initialization failed. Please try again.', 'danger');
        }
    }

    async verifyPayment(reference) {
        try {
            const formData = new FormData();
            formData.append('action', 'verify_payment');
            formData.append('reference', reference);

            const response = await fetch('api/payments.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Payment successful! Your booking has been confirmed.', 'success');
                $('#bookingModal').modal('hide');
                
                // Redirect to booking confirmation page
                setTimeout(() => {
                    window.location.href = `booking-confirmation.php?ref=${reference}`;
                }, 2000);
            } else {
                this.showAlert('Payment verification failed. Please contact support.', 'danger');
            }
        } catch (error) {
            console.error('Error verifying payment:', error);
            this.showAlert('Payment verification failed. Please contact support.', 'danger');
        }
    }

    async handleLogin(form) {
        const formData = new FormData(form);
        
        try {
            const response = await fetch('auth/login.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Login successful!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showAlert(result.message || 'Login failed', 'danger');
            }
        } catch (error) {
            console.error('Error during login:', error);
            this.showAlert('Login failed. Please try again.', 'danger');
        }
    }

    async handleRegister(form) {
        const formData = new FormData(form);
        
        try {
            const response = await fetch('auth/register.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Registration successful! Please login.', 'success');
                // Switch to login tab
                document.getElementById('login-tab').click();
                form.reset();
            } else {
                this.showAlert(result.message || 'Registration failed', 'danger');
            }
        } catch (error) {
            console.error('Error during registration:', error);
            this.showAlert('Registration failed. Please try again.', 'danger');
        }
    }

    async handleNewsletter(form) {
        const formData = new FormData(form);
        
        try {
            const response = await fetch('api/newsletter.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Successfully subscribed to newsletter!', 'success');
                form.reset();
            } else {
                this.showAlert(result.message || 'Subscription failed', 'danger');
            }
        } catch (error) {
            console.error('Error subscribing to newsletter:', error);
            this.showAlert('Subscription failed. Please try again.', 'danger');
        }
    }

    showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.custom-alert');
        existingAlerts.forEach(alert => alert.remove());

        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show custom-alert" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', alertHtml);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.custom-alert');
            if (alert) {
                $(alert).alert('close');
            }
        }, 5000);
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('en-NG', {
            style: 'currency',
            currency: 'NGN'
        }).format(amount);
    }
}

// Initialize the booking system when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.hotelBookingSystem = new HotelBookingSystem();
});

// Utility functions
function showLoading(element) {
    element.innerHTML = '<div class="spinner"></div>';
}

function hideLoading(element, originalContent) {
    element.innerHTML = originalContent;
}

// Date validation
function validateDates(checkIn, checkOut) {
    const checkInDate = new Date(checkIn);
    const checkOutDate = new Date(checkOut);
    const today = new Date();
    
    if (checkInDate < today) {
        return { valid: false, message: 'Check-in date cannot be in the past' };
    }
    
    if (checkOutDate <= checkInDate) {
        return { valid: false, message: 'Check-out date must be after check-in date' };
    }
    
    return { valid: true };
}