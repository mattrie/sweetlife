<?php
require_once 'config/config.php';
require_once 'classes/Booking.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$page_title = 'My Bookings';
$body_class = 'inner_page';

$booking = new Booking();
$user_bookings = $booking->getUserBookings($_SESSION['user_id']);

include 'includes/header.php';
?>

<div class="back_re">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h2>My Bookings</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="contact" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <h4 class="mb-4">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h4>
                    
                    <?php if (!empty($user_bookings)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Room/Apartment</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Guests</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($user_bookings as $booking): 
                                $images = json_decode($booking['images'], true) ?: [];
                                $main_image = !empty($images) ? $images[0] : 'images/room1.jpg';
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['booking_reference']); ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($main_image); ?>" alt="Room" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($booking['room_number']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($booking['category_name']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_in_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_out_date'])); ?></td>
                                <td><?php echo $booking['adults']; ?> Adults<br><?php echo $booking['children']; ?> Children</td>
                                <td>₦<?php echo number_format($booking['total_amount']); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $booking['status'] === 'confirmed' ? 'success' : 
                                            ($booking['status'] === 'pending' ? 'warning' : 
                                            ($booking['status'] === 'checked_in' ? 'info' : 
                                            ($booking['status'] === 'checked_out' ? 'secondary' : 'danger'))); 
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $booking['payment_status'] === 'success' ? 'success' : 
                                            ($booking['payment_status'] === 'pending' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($booking['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group-vertical btn-group-sm">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewBookingDetails('<?php echo $booking['booking_reference']; ?>')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                        <button class="btn btn-outline-danger btn-sm" onclick="cancelBooking('<?php echo $booking['id']; ?>')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h4>No bookings found</h4>
                        <p class="text-muted">You haven't made any bookings yet.</p>
                        <a href="rooms.php" class="btn btn-book">Browse Rooms</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewBookingDetails(reference) {
    // Implement booking details modal
    alert('Booking details for: ' + reference);
}

function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        // Implement booking cancellation
        alert('Booking cancellation functionality to be implemented');
    }
}
</script>

<?php include 'includes/footer.php'; ?>