<?php
require_once 'middleware.php';
require_manager();
require_once 'config.php';
include 'partials/header.php';

$manager_id = $_SESSION['user']['id'];
$event_id = $_GET['event_id'] ?? null;

// Fetch manager's events
$events_stmt = $conn->prepare("SELECT * FROM events WHERE manager_id=? ORDER BY event_date DESC");
$events_stmt->bind_param("i", $manager_id);
$events_stmt->execute();
$events_result = $events_stmt->get_result();

// If an event is selected
if ($event_id) {
    // Fetch all guests
    $guests_result = $conn->query("SELECT * FROM users WHERE role='guest'");

    // Handle sending invite
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guest_id'])) {
        $guest_id = $_POST['guest_id'];

        // Check if already invited
        $check = $conn->prepare("SELECT id FROM invitations WHERE event_id=? AND guest_id=?");
        $check->bind_param("ii", $event_id, $guest_id);
        $check->execute();
        $already = $check->get_result();

        if ($already->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO invitations (event_id, guest_id) VALUES (?,?)");
            $insert->bind_param("ii", $event_id, $guest_id);
            if ($insert->execute()) {
                echo "<p class='success'>Invitation sent successfully!</p>";
            } else {
                echo "<p class='error'>Failed to send invitation.</p>";
            }
        } else {
            echo "<p class='error'>This guest has already been invited.</p>";
        }
    }
}
?>

<div class="box card" style="padding:30px;">
    <h2>Send Invitations</h2>

    <div class="box" style="margin-top:20px;">
        <h3>Your Events</h3>
        <ul>
        <?php while ($event = $events_result->fetch_assoc()): ?>
            <li>
                <?php echo htmlspecialchars($event['title'])." (".$event['event_date']." - ".$event['slot'].")"; ?>
                - <a class="home-btn" href="send_invite.php?event_id=<?php echo $event['id']; ?>">Send Invitations</a>
            </li>
        <?php endwhile; ?>
        </ul>
    </div>

    <?php if ($event_id): ?>
        <div class="box center-form" style="margin-top:20px;">
            <h3>Invite a Guest</h3>
            <form method="post">
                <select name="guest_id" required>
                    <option value="">Select Guest</option>
                    <?php while ($guest = $guests_result->fetch_assoc()): ?>
                        <option value="<?php echo $guest['id']; ?>">
                            <?php echo htmlspecialchars($guest['username'])." (".$guest['email'].")"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="home-btn">Send Invite</button>
            </form>
        </div>

        <div class="box" style="margin-top:20px;">
            <h3>Already Invited Guests</h3>
            <ul>
            <?php
                $invites = $conn->prepare("SELECT u.username, u.email, i.status 
                                           FROM invitations i 
                                           JOIN users u ON i.guest_id=u.id 
                                           WHERE i.event_id=?");
                $invites->bind_param("i", $event_id);
                $invites->execute();
                $invited = $invites->get_result();
                while ($row = $invited->fetch_assoc()) {
                    echo "<li>".htmlspecialchars($row['username'])." (".$row['email'].") - ".$row['status']."</li>";
                }
            ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<?php include 'partials/footer.php'; ?>
