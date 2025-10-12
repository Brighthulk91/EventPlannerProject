<?php
require_once 'config.php';
require_once 'helpers.php';
require_login();
$user = $_SESSION['user'];
include 'partials/header.php';

// Ensure uploads directory exists
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $type = $_POST['type'];
    $file = $_FILES['image_file'];

    if ($file['error'] === 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        $target = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            $relativePath = 'uploads/' . $filename;
            $stmt = $conn->prepare("INSERT INTO gallery (event_id, type, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $event_id, $type, $relativePath);
            if ($stmt->execute()) {
                echo "<p class='success'>Image uploaded successfully!</p>";
            } else {
                echo "<p class='error'>Failed to save in database.</p>";
            }
        } else {
            echo "<p class='error'>Failed to move uploaded file.</p>";
        }
    } else {
        echo "<p class='error'>No file selected or upload error.</p>";
    }
}

// Fetch events for dropdown
$event_stmt = $conn->prepare("SELECT * FROM events ORDER BY event_date DESC");
$event_stmt->execute();
$events = $event_stmt->get_result();
?>

<div class="box">
    <h2>Upload Event Images</h2>
    <form method="post" enctype="multipart/form-data" class="center-form">
        <label>Select Event:</label>
        <select name="event_id" required>
            <option value="">--Select Event--</option>
            <?php while($ev = $events->fetch_assoc()): ?>
                <option value="<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['title']); ?> (<?php echo $ev['event_date']; ?>)</option>
            <?php endwhile; ?>
        </select>

        <label>Type:</label>
        <select name="type" required>
            <option value="">--Select Type--</option>
            <option value="pre">Pre Event</option>
            <option value="post">Post Event</option>
        </select>

        <label>Select Image:</label>
        <input type="file" name="image_file" accept="image/*" required>

        <button type="submit">Upload</button>
    </form>
</div>

<div class="box">
    <h2>Uploaded Images</h2>
    <div class="gallery-container">
        <?php
        $gallery_stmt = $conn->prepare("SELECT g.file_path, e.title, g.type 
                                        FROM gallery g 
                                        JOIN events e ON g.event_id=e.id 
                                        ORDER BY g.uploaded_at DESC");
        $gallery_stmt->execute();
        $gallery = $gallery_stmt->get_result();
        while($img = $gallery->fetch_assoc()): ?>
            <div class="image-card box">
                <img src="<?php echo htmlspecialchars($img['file_path']); ?>" alt="Event Image">
                <p><?php echo htmlspecialchars($img['title']); ?> - <?php echo htmlspecialchars($img['type']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
