<?php
require_once 'config.php';
require_once 'helpers.php';
require_login();
$user = $_SESSION['user'];
include 'partials/header.php';
?>
<div class="box card" style="padding:30px;">
  <h2>Dashboard</h2>
  <p>Welcome back, <?php echo e($user['username']); ?> — role: <?php echo e($user['role']); ?></p>

  <?php if ($user['role'] === 'manager'): ?>
    <div style="margin-bottom:20px;">
      <a class="home-btn" href="create_event.php">Create Event</a>
      <a class="home-btn" href="manage_slots.php">Manage Slots</a>
      <a class="home-btn" href="invitations_sent.php">Invitations</a>
      <a class="home-btn" href="gallery.php">Gallery</a>
    </div>

    <h3 style="margin-top:20px">Your Events</h3>
    <?php
    $stmt = $conn->prepare("SELECT * FROM events WHERE manager_id=? ORDER BY event_date DESC");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $evs = $stmt->get_result();
    if ($evs->num_rows):
    ?>
      <div class="box" style="overflow-x:auto;">
        <table>
          <tr><th>Title</th><th>Date</th><th>Slot</th><th>Location</th><th></th></tr>
          <?php while($e = $evs->fetch_assoc()): ?>
            <tr>
              <td><?php echo e($e['title']); ?></td>
              <td><?php echo e($e['event_date']); ?></td>
              <td><?php echo e($e['slot']); ?></td>
              <td><?php echo e($e['location']); ?></td>
              <td><a href="invitations_sent.php?event_id=<?php echo $e['id']; ?>">Invite Guests</a></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>
    <?php else: ?>
      <p>No events yet. <a href="create_event.php">Create one</a>.</p>
    <?php endif; ?>

  <?php else: // guest ?>
    <div style="margin-bottom:20px;">
      <a class="home-btn" href="activity.php">Find Events</a>
      <a class="home-btn" href="invitations_received.php">Invitations</a>
      <a class="home-btn" href="gallery.php">Gallery</a>
    </div>

    <h3 style="margin-top:20px">Your Bookings</h3>
    <?php
    $stmt = $conn->prepare("SELECT b.*, e.title, e.event_date, e.location FROM bookings b JOIN events e ON b.event_id=e.id WHERE b.guest_id=? ORDER BY b.created_at DESC");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $bk = $stmt->get_result();
    if ($bk->num_rows):
    ?>
      <div class="box" style="overflow-x:auto;">
        <table>
          <tr><th>Event</th><th>Date</th><th>Slot</th><th>Payment</th></tr>
          <?php while($r = $bk->fetch_assoc()): ?>
            <tr>
              <td><?php echo e($r['title']); ?></td>
              <td><?php echo e($r['event_date']); ?></td>
              <td><?php echo e($r['slot']); ?></td>
              <td><?php echo e($r['payment_status']); ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>
    <?php else: ?>
      <p>No bookings yet.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php include 'partials/footer.php'; ?>
