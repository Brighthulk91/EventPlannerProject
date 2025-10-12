<?php
require_once 'middleware.php';
require_guest();
require_once 'config.php';
include 'partials/header.php';

$date = $_GET['date'] ?? '';
$slot = $_GET['slot'] ?? '';
$location = $_GET['location'] ?? '';

$sql = "SELECT * FROM events WHERE 1=1";
$params = [];
$types = "";

if ($date) {
    $sql .= " AND event_date=?";
    $types .= "s";
    $params[] = $date;
}
if ($slot) {
    $sql .= " AND slot=?";
    $types .= "s";
    $params[] = $slot;
}
if ($location) {
    $sql .= " AND location LIKE ?";
    $types .= "s";
    $params[] = "%$location%";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Search Events</h2>
<form method="get" class="center-form">
    <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
    <select name="slot">
        <option value="">All Slots</option>
        <option value="Day" <?php if ($slot=="Day") echo "selected"; ?>>Day</option>
        <option value="Night" <?php if ($slot=="Night") echo "selected"; ?>>Night</option>
    </select>
    <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
    <button type="submit">Search</button>
</form>

<h3>Available Events:</h3>
<table border="1" cellpadding="10">
<tr><th>Title</th><th>Date</th><th>Slot</th><th>Location</th><th>Action</th></tr>
<?php while ($event = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($event['title']); ?></td>
    <td><?php echo $event['event_date']; ?></td>
    <td><?php echo $event['slot']; ?></td>
    <td><?php echo htmlspecialchars($event['location']); ?></td>
    <td><a href="book.php?event_id=<?php echo $event['id']; ?>">Book</a></td>
</tr>
<?php endwhile; ?>
</table>

<?php include 'partials/footer.php'; ?>
