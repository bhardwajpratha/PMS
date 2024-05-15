<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "prison_db";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions and database operations for the user module
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_user'])) {
        $name = $_POST['name'];
        $designation = $_POST['designation'];
        $dob = $_POST['dob'];
        $joining_date = $_POST['joining_date'];
        $gender = $_POST['gender'];

        // Calculate age
        $birthdate = new DateTime($dob);
        $today = new DateTime();
        $age = $birthdate->diff($today)->y;

        // Check if the user is above 18
        if ($age < 18) {
            echo "<script>alert('User must be at least 18 years old');</script>";
        } else {
            // Check if name and designation contain only alphabets
            if (!preg_match("/^[a-zA-Z\s]+$/", $name) ) {
                echo "<script>alert('Name should contain only alphabets');</script>";
            } else {
                // Insert new user
                $query = "INSERT INTO users (username, password, role, dob, joining_date, gender) VALUES ('$name', '', '$designation', '$dob', '$joining_date', '$gender')";
                if ($conn->query($query) === TRUE) {
                    echo "<script>alert('New user created successfully');</script>";
                } else {
                    echo "<script>alert('Error creating new user: " . $conn->error . "');</script>";
                }
            }
        }
    } elseif (isset($_POST['delete_user'])) {
        $id = $_POST['id'];

        // Delete user
        $query = "DELETE FROM users WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            echo "<script>alert('User deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting user: " . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['update_user'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $designation = $_POST['designation'];
        $dob = $_POST['dob'];
        $joining_date = $_POST['joining_date'];
        $gender = $_POST['gender'];
        
        // Calculate age
        $birthdate = new DateTime($dob);
        $today = new DateTime();
        $age = $birthdate->diff($today)->y;

        // Check if the user is above 18
        if ($age < 18) {
            echo "<script>alert('User must be at least 18 years old');</script>";
        } else {

        // Check if name and designation contain only alphabets
        if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            echo "<script>alert('Name should contain only alphabets');</script>";
        } else {
            // Update user
            $query = "UPDATE users SET username = '$name', role = '$designation', dob = '$dob', joining_date = '$joining_date', gender = '$gender' WHERE id = $id";
            if ($conn->query($query) === TRUE) {
                echo "<script>alert('User updated successfully');</script>";
            } else {
                echo "<script>alert('Error updating user: " . $conn->error . "');</script>";
            }
        }
    }
}
}

// Retrieve data for guards
$query = "SELECT id, username AS name, role AS designation, dob, joining_date, gender FROM users WHERE role = 'guard'";
$result = $conn->query($query);
$guards = $result->fetch_all(MYSQLI_ASSOC);

// Retrieve data for cooks
$query = "SELECT id, username AS name, role AS designation, dob, joining_date, gender FROM users WHERE role = 'cook'";
$result = $conn->query($query);
$cooks = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prison Management System - User Module</title>
    <link rel="stylesheet" type="text/css" href="user_module.css">
    
</head>

<body>
    <header><h1>User Module</h1></header><br>
    <h2>Guards</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Designation</th>
            <th>Date of Birth</th>
            <th>Joining Date</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($guards as $guard): ?>
        <tr>
            <td><?php echo $guard['name']; ?></td>
            <td><?php echo $guard['designation']; ?></td>
            <td><?php echo $guard['dob']; ?></td>
            <td><?php echo $guard['joining_date']; ?></td>
            <td><?php echo $guard['gender']; ?></td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $guard['id']; ?>">
                    <input type="submit" name="delete_user" value="Delete">
                </form>
                <button id="btn" onclick="editUser(<?php echo $guard['id']; ?>, '<?php echo $guard['name']; ?>', '<?php echo $guard['designation']; ?>', '<?php echo $guard['dob']; ?>', '<?php echo $guard['joining_date']; ?>', '<?php echo $guard['gender']; ?>')">Edit</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<br><br><br><br>
    <h2>Cooks</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Designation</th>
            <th>Date of Birth</th>
            <th>Joining Date</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($cooks as $cook): ?>
        <tr>
            <td><?php echo $cook['name']; ?></td>
            <td><?php echo $cook['designation']; ?></td>
            <td><?php echo $cook['dob']; ?></td>
            <td><?php echo $cook['joining_date']; ?></td>
            <td><?php echo $cook['gender']; ?></td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $cook['id']; ?>">
                    <input type="submit" name="delete_user" value="Delete">
                </form>
                <button id="btn" onclick="editUser(<?php echo $cook['id']; ?>, '<?php echo $cook['name']; ?>', '<?php echo $cook['designation']; ?>', '<?php echo $cook['dob']; ?>', '<?php echo $cook['joining_date']; ?>', '<?php echo $cook['gender']; ?>')">Edit</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<br><br><br><br>
    <h2>Create New User</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validateForm()">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" placeholder="Enter the name of User" required>
        <br>
        <label for="designation">Designation:</label>
        <select name="designation" id="designation" required>
            <option value="">Choose....</option>
            <option value="guard">Guard</option>
            <option value="cook">Cook</option>
        </select>
        <br>
        <label for="dob">Date of Birth:</label>
        <input type="date" name="dob" id="dob" required>
        <br>
        <label for="joining_date">Joining Date:</label>
        <input type="date" name="joining_date" id="joining_date" required>
        <br>
        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="">Choose....</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
        <br><br>
        <input type="submit" name="create_user" value="Create User">
    </form>

    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit User</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="id" id="editId">
                <label for="editName">Name:</label>
                <input type="text" name="name" id="editName" required>
                <br>
                <label for="editDesignation">Designation:</label>
                <select name="designation" id="editDesignation" required>
                    <option value="guard">Guard</option>
                    <option value="cook">Cook</option>
                </select>
                <br>
                <label for="editDob">Date of Birth:</label>
                <input type="date" name="dob" id="editDob" required>
                <br>
                <label for="editJoiningDate">Joining Date:</label>
                <input type="date" name="joining_date" id="editJoiningDate" required>
                <br>
                <label for="editGender">Gender:</label>
                <select name="gender" id="editGender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <br>
                <input type="submit" name="update_user" value="Update User">
            </form>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("editUserModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on the <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Function to open the edit user modal
        function editUser(id, name, designation, dob, joiningDate, gender) {
            modal.style.display = "block";
            document.getElementById("editId").value = id;
            document.getElementById("editName").value = name;
            document.getElementById("editDesignation").value = designation;
            document.getElementById("editDob").value = dob;
            document.getElementById("editJoiningDate").value = joiningDate;
            document.getElementById("editGender").value = gender;
        }

        // Function to validate form submission
        function validateForm() {
            var name = document.getElementById("name").value;
            var designation = document.getElementById("designation").value;
            var dob = new Date(document.getElementById("dob").value);
            var today = new Date();
            var age = today.getFullYear() - dob.getFullYear();
            var monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            if (age < 18) {
                alert("User must be at least 18 years old");
                return false;
            }
            var nameRegex = /^[a-zA-Z\s]+$/;
            if (!nameRegex.test(name) ) {
                alert("Name should contain only alphabets");
                return false;
            }
            return true;
        }
    </script>

     <a href="index.php">Back to Dashboard</a>   
</body>
</html>
