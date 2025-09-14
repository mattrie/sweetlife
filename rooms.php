<?php
require_once 'config/config.php';
require_once 'classes/Room.php';

$page_title = 'Hotel Rooms';
$page_description = 'Discover our luxury hotel rooms with modern amenities and exceptional service.';
$body_class = 'inner_page';

include 'includes/header.php';

$room = new Room();
$rooms = $room->getAllRooms('hotel_room');
?>

<div class="back_re">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h2>Hotel Rooms</h2>
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
                    <p class="margin_0">Choose from our selection of luxury hotel rooms designed for comfort and convenience</p>
                </div>
            </div>
        </div>
        <div class="row" id="roomsContainer">
            <?php foreach($rooms as $room): 
                $images = json_decode($room['images'], true) ?: [];
                $main_image = !empty($images) ? $images[0] : 'images/room1.jpg';
            ?>
            <div class="col-md-4 col-sm-6">
                <div class="room-card">
                    <div class="room_img">
                        <figure><img src="<?php echo htmlspecialchars($main_image); ?>" alt="<?php echo htmlspecialchars($room['category_name']); ?>" style="width: 100%; height: 250px; object-fit: cover;"/></figure>
                    </div>
                    <div class="bed_room" style="padding: 20px;">
                        <h3><?php echo htmlspecialchars($room['category_name']); ?></h3>
                        <p class="room-price">₦<?php echo number_format($room['price_per_night']); ?>/night</p>
                        <p><strong>Room:</strong> <?php echo htmlspecialchars($room['room_number']); ?></p>
                        <p><strong>Capacity:</strong> <?php echo $room['max_adults']; ?> Adults, <?php echo $room['max_children']; ?> Children</p>
                        <p><?php echo htmlspecialchars($room['description'] ?: 'Comfortable accommodation with modern amenities'); ?></p>
                        <?php if (!empty($room['amenities'])): ?>
                        <p><strong>Amenities:</strong> <?php echo htmlspecialchars($room['amenities']); ?></p>
                        <?php endif; ?>
                        <button class="btn btn-book btn-select-room" data-room-id="<?php echo $room['id']; ?>">
                            Book Now
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($rooms)): ?>
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="alert alert-info">
                    <h4>No rooms available at the moment</h4>
                    <p>Please check back later or contact us for assistance.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- end our_room -->

<?php include 'includes/footer.php'; ?>