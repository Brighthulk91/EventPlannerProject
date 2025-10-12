<?php
require_once 'config.php';
require_once 'helpers.php';
require_manager();

include 'partials/header.php';

// Show manager events and allow toggling slot open/close by a simple approach:
// In our model Day/Night are fixed; manager can view events and mark slot as bookable - for simplicity bookings are allowed unless you implement availability counts.

$stmt = $conn->prepare("SELECT * FROM events WHERE manager_id=? ORDER BY event_date DESC");
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="box card" style="padding:30px;">
  <h2>Manage Slots (Day / Night)</h2>
  <p>Slots are fixed: Day and Night. Guests can book available events' specified slot.</p>

  <?php if ($res->num_rows): ?>
    <div class="box" style="overflow-x:auto; margin-top:20px;">
      <table>
        <tr><th>Title</th><th>Date</th><th>Slot</th><th>Location</th><th>Invitations</th></tr>
        <?php while($e = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo e($e['title']); ?></td>
            <td><?php echo e($e['event_date']); ?></td>
            <td><?php echo e($e['slot']); ?></td>
            <td><?php echo e($e['location']); ?></td>
            <td><a class="home-btn" href="invitations_sent.php?event_id=<?php echo $e['id']; ?>">Send</a></td>
          </tr>
        <?php endwhile; ?>
      </table>
    </div>
  <?php else: ?>
    <p>No events found. <a href="create_event.php">Create one</a>.</p>
  <?php endif; ?>
</div>
<?php include 'partials/footer.php'; ?>
