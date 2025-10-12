<?php
require_once 'config.php';
require_once 'helpers.php';
require_guest();
include 'partials/header.php';

$guest_id = $_SESSION['user']['id'];
$event_id = intval($_GET['event_id'] ?? 0);

if (!$event_id) {
    echo "<div class='error'>Invalid event id.</div>";
    include 'partials/footer.php';
    exit;
}

// load event
$st = $conn->prepare("SELECT * FROM events WHERE id=?");
$st->bind_param("i",$event_id);
$st->execute();
$event = $st->get_result()->fetch_assoc();
if (!$event) {
    echo "<div class='error'>Event not found.</div>";
    include 'partials/footer.php';
    exit;
}

// check existing booking
$ch = $conn->prepare("SELECT id, payment_status FROM bookings WHERE event_id=? AND guest_id=? LIMIT 1");
$ch->bind_param("ii",$event_id,$guest_id);
$ch->execute();
$existing = $ch->get_result()->fetch_assoc();

// handle simulated payment process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    if ($existing) {
        echo "<div class='error'>You already booked this event.</div>";
    } else {
        // create booking
        $ins = $conn->prepare("INSERT INTO bookings (event_id, guest_id, slot, payment_status) VALUES (?,?,?,'pending')");
        $ins->bind_param("iis",$event_id,$guest_id,$event['slot']);
        if ($ins->execute()) {
            $booking_id = $ins->insert_id;
            // simulate payment: we'll mark completed and insert payment record
            $amount = 50.00; // fixed demo amount
            $p = $conn->prepare("INSERT INTO payments (booking_id, amount, status, paid_at) VALUES (?,?, 'completed', NOW())");
            $p->bind_param("id", $booking_id, $amount);
            if ($p->execute()) {
                // update booking status
                $up = $conn->prepare("UPDATE bookings SET payment_status='paid' WHERE id=?");
                $up->bind_param("i", $booking_id);
                $up->execute();
                echo "<div class='success'>Payment completed & booking confirmed.</div>";
            } else {
                echo "<div class='error'>Payment failed.</div>";
            }
        } else {
            echo "<div class='error'>Booking failed.</div>";
        }
    }
}

?>

<div class="card">
  <h2>Book Event: <?php echo e($event['title']); ?></h2>
  <p><strong>Date:</strong> <?php echo e($event['event_date']); ?> — <strong>Slot:</strong> <?php echo e($event['slot']); ?></p>
  <p><strong>Location:</strong> <?php echo e($event['location']); ?></p>

  <?php if ($existing): ?>
    <p class="success">You already booked this event. Payment status: <?php echo e($existing['payment_status']); ?></p>
  <?php else: ?>
    <form method="post" class="center-form">
      <p>Booking cost: <strong>$50.00</strong> (simulated)</p>
      <button name="pay" type="submit">Pay & Confirm Booking</button>
    </form>
  <?php endif; ?>
</div>

<?php include 'partials/footer.php'; ?>
