<?php
require_once 'config.php';
require_once 'helpers.php';
require_manager();
include 'partials/header.php';

$manager_id = $_SESSION['user']['id'];
$event_id = intval($_GET['event_id'] ?? 0);

// list manager events to choose
$stmt = $conn->prepare("SELECT * FROM events WHERE manager_id=? ORDER BY event_date DESC");
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$events = $stmt->get_result();

$guests = $conn->query("SELECT id,username,email FROM users WHERE role='guest' ORDER BY username ASC");

// handle invite post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guest_id']) && isset($_POST['event_id'])) {
    $g = intval($_POST['guest_id']);
    $e = intval($_POST['event_id']);
    // check duplicate
    $chk = $conn->prepare("SELECT id FROM invitations WHERE event_id=? AND guest_id=? LIMIT 1");
    $chk->bind_param("ii",$e,$g);
    $chk->execute();
    if ($chk->get_result()->num_rows) {
        echo "<div class='error'>Guest already invited.</div>";
    } else {
        $ins = $conn->prepare("INSERT INTO invitations (event_id, guest_id) VALUES (?,?)");
        $ins->bind_param("ii",$e,$g);
        if ($ins->execute()) {
            echo "<div class='success'>Invitation sent.</div>";
        } else {
            echo "<div class='error'>Failed to send invite.</div>";
        }
    }
}

// show invitations for selected event
$invited = [];
if ($event_id) {
    $q = $conn->prepare("SELECT u.username,u.email,i.status FROM invitations i JOIN users u ON i.guest_id=u.id WHERE i.event_id=?");
    $q->bind_param("i",$event_id);
    $q->execute();
    $invited = $q->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="card">
  <h2>Send Invitations</h2>

  <form method="get" class="center-form">
    <select name="event_id" onchange="this.form.submit()">
      <option value="">Select your event</option>
      <?php while($ev = $events->fetch_assoc()): ?>
        <option value="<?php echo $ev['id']; ?>" <?php if($event_id==$ev['id']) echo 'selected'; ?>>
          <?php echo e($ev['title'].' - '.$ev['event_date'].' ('.$ev['slot'].')'); ?>
        </option>
      <?php endwhile; ?>
    </select>
  </form>

  <?php if($event_id): ?>
    <form method="post" class="center-form">
      <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
      <select name="guest_id" required>
        <option value="">Select Guest</option>
        <?php
          $gq = $conn->query("SELECT id,username,email FROM users WHERE role='guest' ORDER BY username");
          while($g = $gq->fetch_assoc()):
        ?>
          <option value="<?php echo $g['id']; ?>"><?php echo e($g['username'].' ('.$g['email'].')'); ?></option>
        <?php endwhile; ?>
      </select>
      <button type="submit">Send Invitation</button>
    </form>

    <h3>Invited Guests</h3>
    <?php if ($invited): ?>
      <ul>
        <?php foreach($invited as $row): ?>
          <li><?php echo e($row['username'].' ('.$row['email'].') - '.$row['status']); ?></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No invitations yet.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php include 'partials/footer.php'; ?>
