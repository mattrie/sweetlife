<?php
require_once 'config/config.php';
require_once 'classes/Room.php';

$page_title = 'Short Let Apartments';
$page_description = 'Fully furnished apartments perfect for extended stays, business trips, and family vacations.';
$body_class = 'inner_page';

include 'includes/header.php';

$room = new Room();
$apartments = $room->getAllRooms('short_let');
?>

<div class="back_re">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h2>Short Let Apartments</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- our_room -->
<div class="our_room">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage">
                    <p class="margin_0">Fully furnished apartments perfect for extended stays, business trips, and family vacations</p>
                </div>
            </div>
        </div>
        <div class="row" id="roomsContainer">
            <?php foreach($apartments as $apartment): 
                $images = json_decode($apartment['images'], true) ?: [];
                $main_image = !empty($images) ? $images[0] : 'images/room1.jpg';
            ?>
            <div class="col-md-4 col-sm-6">
                <div class="room-card">
                    <div class="room_img">
                        <figure><img src="<?php echo htmlspecialchars($main_image); ?>" alt="<?php echo htmlspecialchars($apartment['category_name']); ?>" style="width: 100%; height: 250px; object-fit: cover;"/></figure>
                    </div>
                    <div class="bed_room" style="padding: 20px;">
                        <h3><?php echo htmlspecialchars($apartment['category_name']); ?></h3>
                        <p class="room-price">₦<?php echo number_format($apartment['price_per_night']); ?>/night</p>
                        <p><strong>Unit:</strong> <?php echo htmlspecialchars($apartment['room_number']); ?></p>
                        <p><strong>Capacity:</strong> <?php echo $apartment['max_adults']; ?> Adults, <?php echo $apartment['max_children']; ?> Children</p>
                        <p><?php echo htmlspecialchars($apartment['description'] ?: 'Fully furnished apartment with modern amenities'); ?></p>
                        <?php if (!empty($apartment['amenities'])): ?>
                        <p><strong>Amenities:</strong> <?php echo htmlspecialchars($apartment['amenities']); ?></p>
                        <?php endif; ?>
                        <button class="btn btn-book btn-select-room" data-room-id="<?php echo $apartment['id']; ?>">
                            Book Now
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($apartments)): ?>
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="alert alert-info">
                    <h4>No apartments available at the moment</h4>
                    <p>Please check back later or contact us for assistance.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- end our_room -->

<?php include 'includes/footer.php'; ?>