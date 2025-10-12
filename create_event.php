<?php
require_once 'config.php';
require_once 'helpers.php';
require_manager();

$errors = [];
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $date = $_POST['event_date'] ?? '';
    $slot = $_POST['slot'] ?? '';

    if (!$title || !$location || !$date || !$slot) {
        $errors[] = 'All fields required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO events (manager_id,title,description,location,event_date,slot) VALUES (?,?,?,?,?,?)");
        $desc = trim($_POST['description'] ?? '');
        $stmt->bind_param("isssss", $_SESSION['user']['id'], $title, $desc, $location, $date, $slot);
        if ($stmt->execute()) {
            $success = 'Event created.';
        } else {
            $errors[] = 'Failed to create event.';
        }
    }
}

include 'partials/header.php';
?>
<div class="card">
  <h2>Create Event</h2>
  <?php foreach($errors as $e): ?><div class="error"><?php echo e($e); ?></div><?php endforeach; ?>
  <?php if($success): ?><div class="success"><?php echo e($success); ?></div><?php endif; ?>

  <form method="post" class="center-form">
    <input name="title" placeholder="Event title" required>
    <textarea name="description" placeholder="Description" rows="4"></textarea>
    <input name="location" placeholder="Location" required>
    <input name="event_date" type="date" required>
    <select name="slot" required>
      <option value="">Select Slot</option>
      <option value="Day">Day</option>
      <option value="Night">Night</option>
    </select>
    <button type="submit">Create</button>
  </form>
</div>
<?php include 'partials/footer.php'; ?>
