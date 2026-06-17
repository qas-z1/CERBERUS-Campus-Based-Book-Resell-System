<?php
session_start();
include 'conn.php';




if (!isset($_SESSION['username'])) {
    echo "<p style='color:red;'>Username not set in session.</p>";
    exit();
}

$uname = $_SESSION['username'];
$stuID = $_SESSION['studentID'];
$book = null;

// Load specific book data when clicked from listings.php
if (isset($_GET['id'])) {
    $bookid = intval($_GET['id']);
    $book_query = "SELECT * FROM books WHERE Book_id='$bookid' AND studentID='$stuID'";
    $book_result = mysqli_query($conn, $book_query);
    $book = mysqli_fetch_assoc($book_result);
}

// Update book
if (isset($_POST['update'])) {
    $bookid = $_POST['bookid'];
    $fields = [];

    if (!empty($_POST['title'])) $fields[] = "title='" . $_POST['title'] . "'";
    if (!empty($_POST['course_code'])) $fields[] = "course_code='" . $_POST['course_code'] . "'";
    if (!empty($_POST['subject'])) $fields[] = "subject='" . $_POST['subject'] . "'";
    if (!empty($_POST['semester'])) $fields[] = "semester='" . $_POST['semester'] . "'";
    if (!empty($_POST['price'])) $fields[] = "price='" . $_POST['price'] . "'";
    if (!empty($_POST['cond'])) $fields[] = "book_condition='" . $_POST['cond'] . "'";
    if (!empty($_POST['description'])) $fields[] = "description='" . $_POST['description'] . "'";
    if (!empty($_POST['status'])) $fields[] = "status='" . $_POST['status'] . "'";

    if (!empty($_FILES['book_image']['name'])) {
        $targetDir = "image/";
        $filename = basename($_FILES["book_image"]["name"]);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $targetFile)) {
            $fields[] = "image_url='" . $targetFile . "'";
        }
    }

    if (!empty($fields)) {
        $update_query = "UPDATE books SET " . implode(", ", $fields) . " WHERE Book_id='$bookid' AND studentID='$stuID'";
        mysqli_query($conn, $update_query);

        // Re-fetch updated book data
        $book_query = "SELECT * FROM books WHERE Book_id='$bookid' AND studentID='$stuID'";
        $book_result = mysqli_query($conn, $book_query);
        $book = mysqli_fetch_assoc($book_result);

        echo "<script>alert('Book updated successfully!');</script>";
    } else {
        echo "<script>alert('No fields to update.');</script>";
    }
}

// Delete book and image
if (isset($_POST['delete'])) {
    $bookid = $_POST['bookid'];

    $img_query = mysqli_query($conn, "SELECT image_url FROM books WHERE Book_id='$bookid' AND studentID='$stuID'");
    $img_data = mysqli_fetch_assoc($img_query);
    $img_path = $img_data['image_url'];

    $delete_query = "DELETE FROM books WHERE Book_id='$bookid' AND studentID='$stuID'";
    mysqli_query($conn, $delete_query);

    if (!empty($img_path) && file_exists($img_path)) {
        unlink($img_path);
    }

    echo "<script>alert('Book deleted successfully!'); window.location='listings.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Book - CampusBooks</title>
  <style>
  :root {
    --primary: #39336C;
    --primary-light: #4d4685;
    --secondary: #FFC107;
    --light: #FFFFFF;
    --dark: #2d1a53;
    --glass: rgba(255, 255, 255, 0.15);
    --glass-dark: rgba(57, 51, 108, 0.7);
    --radius: 16px;
    --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    --transition: all 0.3s ease;
  }

  * {
    box-sizing: border-box;
    transition: var(--transition);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  body {
    margin: 0;
    background: linear-gradient(135deg, #1a103f, #2a1a5e);
    color: var(--light);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 2rem;
  }

  h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--light);
    text-align: center;
  }

  p {
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 2rem;
  }

  .form-container {
    max-width: 700px;
    margin: auto;
    background: var(--glass);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow);
  }

  .book-img {
    width: 100%;
    max-width: 200px;
    height: auto;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--light);
  }

  .form-group input,
  .form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius);
    background: rgba(255, 255, 255, 0.1);
    color: var(--light);
    font-size: 1rem;
  }

  .form-group input:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: var(--secondary);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
  }

  .form-group textarea {
    resize: vertical;
    min-height: 100px;
  }

  button {
    padding: 12px 20px;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 6px 15px rgba(57, 51, 108, 0.3);
  }

  button:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
  }

  .submit-btn {
    background: var(--primary-light);
    color: var(--light);
  }

  .submit-btn:hover {
    background: var(--primary);
    color: var(--light);
  }

  .delete-btn {
    background: red;
    color: white;
  }

  .delete-btn:hover {
    background: darkred;
    color: white;
  }

  .back-btn {
    background: #aaa;
    color: #2d1a53;
    text-decoration: none;
  }

  .back-btn:hover {
    background: #888;
    color: #fff;
  }

  .button-group {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
  }

  @media (max-width: 768px) {
    .form-container {
      padding: 1.5rem;
    }
    .button-group {
      flex-direction: column;
      align-items: stretch;
    }
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
</head>
<body>
<h2>Update or Delete Book</h2>
<p>Seller: <?php echo htmlspecialchars($uname); ?> (<?php echo $stuID; ?>)</p>

<?php if ($book): ?>
  <img src="<?php echo $book['image_url']; ?>" alt="Book Cover" class="book-img">
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="bookid" value="<?php echo $book['Book_id']; ?>">

    <div class="form-group">
      <label>Title</label>
      <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>">
    </div>

    <div class="form-group">
      <label>Course Code</label>
      <input type="text" name="course_code" value="<?php echo htmlspecialchars($book['course_code']); ?>">
    </div>

    <div class="form-group">
      <label>Subject</label>
      <input type="text" name="subject" value="<?php echo htmlspecialchars($book['subject']); ?>">
    </div>

    <div class="form-group">
      <label>Semester</label>
      <input type="number" name="semester" value="<?php echo htmlspecialchars($book['semester']); ?>">
    </div>

    <div class="form-group">
      <label>Price (RM)</label>
      <input type="text" name="price" value="<?php echo htmlspecialchars($book['price']); ?>">
    </div>

    <div class="form-group">
      <label>Condition</label>
      <input type="text" name="cond" value="<?php echo htmlspecialchars($book['book_condition']); ?>">
    </div>

    <div class="form-group">
      <label>Description</label>
      <textarea name="description"><?php echo htmlspecialchars($book['description']); ?></textarea>
    </div>

    <div class="form-group">
      <label>Status</label>
      <input type="text" name="status" value="<?php echo htmlspecialchars($book['status']); ?>">
    </div>

    <div class="form-group">
      <label>Update Book Image</label>
      <input type="file" name="book_image">
    </div>

    <div class="button-group">
  <button type="submit" class="submit-btn" name="update">
    <i class="fas fa-edit"></i> Update Book
  </button>
  <button type="submit" class="submit-btn delete-btn" name="delete">
    <i class="fas fa-trash-alt"></i> Delete Book
  </button>
  <a href="listings.php" class="back-btn">
    <button type="button"><i class="fas fa-arrow-left"></i> Back to Listings</button>
  </a>
</div>

  </form>
<?php else: ?>
  <p style="color:red;">No book selected or book not found.</p>
<?php endif; ?>
</body>
</html>
