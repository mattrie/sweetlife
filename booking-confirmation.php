<?php
require_once 'config/config.php';
require_once 'classes/Booking.php';
require_once 'classes/Payment.php';

$page_title = 'Booking Confirmation';
$body_class = 'inner_page';

// Get booking reference from URL
$reference = $_GET['ref'] ?? '';

if (empty($reference)) {
    header('Location: index.php');
    exit;
}

$payment = new Payment();
$booking = new Booking();

// Get payment details
$payment_data = $payment->getPaymentByReference($reference);

if (!$payment_data) {
    header('Location: index.php');
    exit;
}

// Get booking details
$booking_data = $booking->getBookingById($payment_data['booking_id']);

include 'includes/header.php';
?>

<div class="back_re">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h2>Booking Confirmation</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="contact" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" style="border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div class="card-header text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                        <h3><i class="fas fa-check-circle"></i> Booking Confirmed!</h3>
                        <p class="mb-0">Thank you for choosing SweetLife Hotel</p>
                    </div>
                    <div class="card-body" style="padding: 40px;">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Booking Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Booking Reference:</strong></td>
                                        <td><?php echo htmlspecialchars($booking_data['booking_reference']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Room:</strong></td>
                                        <td><?php echo htmlspecialchars($booking_data['room_number'] . ' - ' . $booking_data['category_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Check-in:</strong></td>
                                        <td><?php echo date('M d, Y', strtotime($booking_data['check_in_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Check-out:</strong></td>
                                        <td><?php echo date('M d, Y', strtotime($booking_data['check_out_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Guests:</strong></td>
                                        <td><?php echo $booking_data['adults']; ?> Adults, <?php echo $booking_data['children']; ?> Children</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Nights:</strong></td>
                                        <td><?php echo $booking_data['total_nights']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Payment Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Transaction Ref:</strong></td>
                                        <td><?php echo htmlspecialchars($payment_data['transaction_reference']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount Paid:</strong></td>
                                        <td>₦<?php echo number_format($payment_data['amount']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Status:</strong></td>
                                        <td>
                                            <span class="badge badge-success">
                                                <?php echo ucfirst($payment_data['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Date:</strong></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($payment_data['payment_date'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <?php if (!empty($booking_data['special_requests'])): ?>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5>Special Requests</h5>
                                <p class="alert alert-info"><?php echo htmlspecialchars($booking_data['special_requests']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-info-circle"></i> Important Information</h5>
                                    <p class="mb-0">
                                        A confirmation email has been sent to <strong><?php echo htmlspecialchars($booking_data['user_email']); ?></strong>
                                        <br>Please arrive at the hotel with a valid ID for check-in.
                                        <br>Check-in time: 2:00 PM | Check-out time: 12:00 PM
                                    </p>
                                </div>
                                
                                <div class="btn-group" role="group">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-home"></i> Back to Home
                                    </a>
                                    <button onclick="window.print()" class="btn btn-primary">
                                        <i class="fas fa-print"></i> Print Receipt
                                    </button>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="my-bookings.php" class="btn btn-info">
                                        <i class="fas fa-list"></i> My Bookings
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>