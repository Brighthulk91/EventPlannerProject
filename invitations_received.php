<?php
require_once 'config.php';
require_once 'helpers.php';
require_guest();
include 'partials/header.php';

$guest_id = $_SESSION['user']['id'];

// accept/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_id'], $_POST['action'])) {
    $id = intval($_POST['invite_id']);
    $action = $_POST['action'] === 'accept' ? 'accepted' : 'rejected';
    $u = $conn->prepare("UPDATE invitations SET status=? WHERE id=? AND guest_id=?");
    $u->bind_param("sii",$action,$id,$guest_id);
    $u->execute();
    echo "<div class='success'>Invitation updated.</div>";
}

// list pending invites
$stmt = $conn->prepare("SELECT i.id as invite_id, e.title, e.event_date, e.slot, u.username as manager_name FROM invitations i JOIN events e ON i.event_id=e.id JOIN users u ON e.manager_id=u.id WHERE i.guest_id=? AND i.status='pending' ORDER BY i.created_at DESC");
$stmt->bind_param("i",$guest_id);
$stmt->execute();
$invites = $stmt->get_result();
?>

<div class="card">
  <h2>Your Invitations</h2>
  <?php if ($invites->num_rows): ?>
    <?php while($row = $invites->fetch_assoc()): ?>
      <div class="card" style="margin-bottom:8px">
        <strong><?php echo e($row['title']); ?></strong><br>
        Date: <?php echo e($row['event_date']); ?> — Slot: <?php echo e($row['slot']); ?><br>
        From: <?php echo e($row['manager_name']); ?>
        <form method="post" style="margin-top:8px">
          <input type="hidden" name="invite_id" value="<?php echo $row['invite_id']; ?>">
          <button name="action" value="accept">Accept & Book</button>
          <button name="action" value="reject" class="secondary">Reject</button>
        </form>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No pending invitations.</p>
  <?php endif; ?>
</div>
<?php include 'partials/footer.php'; ?>
