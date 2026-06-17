<?php 
include 'conn.php';
session_start();
if (!isset($_SESSION['username'])) {
    echo "<p style='color:red;'>Username not set in session.</p>";
} else {
    $uname = $_SESSION['username'];
}
if (isset($_POST['done'])) {
    $title = $_POST['title'];
    $course_code = $_POST['course_code'];
    $subject = $_POST['subject'];
    $semester = $_POST['semester'];
    $price = $_POST['price'];
    $condition = $_POST['cond'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $stuID =$_SESSION['studentID'];

    $targetDir = "image/";
    $filename = basename($_FILES["book_image"]["name"]);
    $targetFile = $targetDir . $filename;

    if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $targetFile)) {
        $image_url = $targetFile;

        $query = "INSERT INTO books (title, course_code, subject, semester, price, book_condition, description, image_url, status, studentID) 
        VALUES ('$title','$course_code','$subject','$semester','$price','$condition','$description','$image_url','$status','$stuID')";

        mysqli_query($conn, $query);
        echo "<script>alert('Book uploaded successfully!');</script>";
        header("Location: seller.php?success=1");
        exit();
    } else {
        echo "<script>alert('File upload failed.');</script>";
    }
}

$stuID = $_SESSION['studentID'];
$book_query = "SELECT * FROM books WHERE studentID='$stuID'";
$book_result = mysqli_query($conn, $book_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CampusBooks - Book Details</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <style>
    :root {
      --primary: #39336C;
      --primary-light: #4d4685;
      --primary-dark: #2c2753;
      --secondary: #FFC107;
      --light: #FFFFFF;
      --dark: #2d1a53;
      --glass: rgba(255, 255, 255, 0.15);
      --radius: 16px;
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #1a103f, #2a1a5e);
      color: var(--light);
      min-height: 100vh;
    }

    header {
      background-color: var(--primary);
      color: var(--light);
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo {
      font-size: 28px;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      background: linear-gradient(90deg, var(--secondary), #ffd54f);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    nav {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    nav a {
      color: var(--light);
      text-decoration: none;
      font-weight: 500;
      padding: 0.7rem 1.2rem;
      border-radius: 50px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    nav a::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: rgba(255, 255, 255, 0.1);
      transition: var(--transition);
      z-index: -1;
    }

    nav a:hover::before {
      width: 100%;
    }

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-button {
      background-color: transparent;
      border: none;
      cursor: pointer;
      padding: 8px;
    }

    .dropdown-icon {
      font-size: 24px;
      color: var(--light);
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: var(--glass);
      min-width: 160px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      z-index: 1;
    }

    .dropdown-content a {
      color: var(--light);
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      transition: var(--transition);
    }

    .dropdown-content a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-content.show {
      display: block;
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    .title {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(90deg, var(--light), #d1c4e9);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
      margin-bottom: 2rem;
    }

    .title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 70px;
      height: 4px;
      background: var(--secondary);
      border-radius: 10px;
    }

    .user-info {
      display: flex;
      gap: 2rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }

    .user-info div {
      background: var(--glass);
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      min-width: 200px;
      text-align: center;
    }

    .book-listings {
      margin-top: 2rem;
      background: var(--glass);
      border-radius: var(--radius);
      padding: 2rem;
      box-shadow: var(--shadow);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--glass-dark);
      border-radius: var(--radius);
      overflow: hidden;
    }

    th, td {
      padding: 1.2rem;
      text-align: center;
    }

    th {
      background: var(--primary-light);
      color: var(--light);
    }

    tr:nth-child(even) {
      background: var(--glass);
    }

    tr:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    img {
      width: 60px;
      height: 80px;
      object-fit: cover;
      border-radius: var(--radius);
    }

    .form-container {
      margin-top: 2rem;
      background: var(--glass);
      border-radius: var(--radius);
      padding: 2rem;
      box-shadow: var(--shadow);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.8);
    }

    .form-group input[type="text"],
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 1rem 1.2rem;
      border-radius: var(--radius);
      border: none;
      background: rgba(255, 255, 255, 0.1);
      color: var(--light);
      font-size: 1rem;
      transition: var(--transition);
      border: 1px solid transparent;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: var(--secondary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
    }

    .form-group textarea {
      resize: vertical;
      height: 100px;
    }

    .upload-btn {
      display: inline-block;
      background: var(--glass);
      border: 2px dashed var(--secondary);
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      cursor: pointer;
      text-align: center;
      transition: var(--transition);
    }

    .upload-btn:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .upload-btn input {
      display: none;
    }

    .price-group {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .price-group span {
      background: var(--primary-light);
      padding: 1rem;
      border-radius: var(--radius) 0 0 var(--radius);
      font-weight: 600;
    }

    .price-group input {
      flex: 1;
      border-radius: 0 var(--radius) var(--radius) 0;
    }

    .submit-btn {
      display: block;
      margin: 0 auto;
      background: var(--primary-light);
      color: var(--light);
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      text-decoration: none;
      font-size: 1rem;
      position: relative;
      overflow: hidden;
    }

    .submit-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: rgba(255, 255, 255, 0.2);
      transition: var(--transition);
      z-index: -1;
    }

    .submit-btn:hover::before {
      width: 100%;
    }

    .submit-btn:hover {
      background: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(57, 51, 108, 0.5);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .animate {
      animation: fadeIn 0.6s ease-out forwards;
    }

    @media (max-width: 768px) {
      .user-info {
        flex-direction: column;
        gap: 1rem;
      }
      
      .form-container {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <header class="glass">
    <div class="logo">
      <i class="fas fa-book"></i>
      CampusBooks
    </div>
    <nav>
      <a href="homepage.php">Home Page</a>
      <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'seller') : ?>
        <a href="listings.php">My Book Listings</a>
        <a href="chatroom.php">Messages</a>
      <?php endif; ?>
    </nav>
  </header>

  <div class="container animate">
    <div class="title">Seller Dashboard</div>
    
    <div class="user-info">
      <div><strong>Seller Name:</strong> <?php echo $uname; ?></div>
      <div><strong>Seller Student ID:</strong> <?php echo $_SESSION['studentID']; ?></div>
    </div>

    <div class="book-listings">
      <h2>Your Book Listings</h2>
      <table>
        <thead>
          <tr>
            <th>Book ID</th>
            <th>Title</th>
            <th>Course Code</th>
            <th>Subject</th>
            <th>Semester</th>
            <th>Price</th>
            <th>Condition</th>
            <th>Description</th>
            <th>Status</th>
            <th>Image</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($book_result)): ?>
            <tr>
              <td><?php echo $row['Book_id']; ?></td>
              <td><?php echo $row['title']; ?></td>
              <td><?php echo $row['course_code']; ?></td>
              <td><?php echo $row['subject']; ?></td>
              <td><?php echo $row['semester']; ?></td>
              <td>RM <?php echo $row['price']; ?></td>
              <td><?php echo $row['book_condition']; ?></td>
              <td><?php echo $row['description']; ?></td>
              <td><?php echo $row['status']; ?></td>
              <td>
                <?php if (!empty($row['image_url'])): ?>
                  <img src="<?php echo $row['image_url']; ?>" alt="Book Image">
                <?php else: ?>
                  No Image
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="form-container animate" style="animation-delay: 0.2s;">
      <h2>Add New Book Listing</h2>
      <p style="color: red; font-weight: bold;">NOTICE!</p>
      <p>Please insert all the details for the new book listing.</p>
      
      <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" required>
        </div>

        <div class="form-group">
          <label>Course Code</label>
          <input type="text" name="course_code" required>
        </div>

        <div class="form-group">
          <label>Subject</label>
          <input type="text" name="subject" required>
        </div>

        <div class="form-group">
          <label>Semester</label>
          <input type="number" name="semester" required>
        </div>

        <div class="form-group">
          <label>Price</label>
          <div class="price-group">
            <span>RM</span>
            <input type="text" name="price" placeholder="00.00" required>
          </div>
        </div>

        <div class="form-group">
          <label>Condition</label>
          <input type="text" name="cond" required>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" required></textarea>
        </div>

        <div class="form-group">
          <div class="form-group">
      <label>Update Book Image</label>
      <input type="file" name="book_image">
    </div>
        </div>

        <div class="form-group">
          <label>Status</label>
          <div>
            <label style="display: flex; align-items: center; gap: 0.5rem;">
              <input type="radio" name="status" value="Available" checked> Available
            </label>
          </div>
        </div>

        <button type="submit" class="submit-btn" name="done">Add Book</button>
      </form>
    </div>
  </div>

  <script>
    function toggleDropdown() {
      const dropdown = document.getElementById("dropdownMenu");
      dropdown.classList.toggle("show");
    }
    
    window.onclick = function(event) {
      if (!event.target.matches('.dropdown-button') && !event.target.closest('.dropdown')) {
        const dropdown = document.getElementById("dropdownMenu");
        if (dropdown.classList.contains('show')) {
          dropdown.classList.remove('show');
        }
      }
    }
  </script>
</body>
</html>