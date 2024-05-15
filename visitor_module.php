<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Function to check if the input contains only alphabets
function validateAlphabets($input) {
    return preg_match('/^[a-zA-Z\s]+$/', $input);
}

function validateAadhaarNumber($aadhaar_number) {
    return (preg_match('/^\d{12}$/', $aadhaar_number) === 1);
}

function validateVoterId($voter_id) {
    return (preg_match('/^[a-zA-Z0-9]{10}$/', $voter_id) === 1);
}

function validateDrivingLicense($driving_license) {
    return (preg_match('/^[a-zA-Z0-9@#$%^&*()\-_=+\\|\'"\/,.<>?:; ]{6,}$/', $driving_license) === 1);
}

// Function to validate ID proof number based on ID type
function validateIDProofNumber($id_proof_type, $id_proof_number) {
    switch ($id_proof_type) {
        case 'adhaar':
            // Aadhaar ID validation (12 digits)
            return validateAadhaarNumber($id_proof_number);
        case 'voter_id':
            // Voter ID validation (10 alphanumeric characters)
            return validateVoterId($id_proof_number);
        case 'driving_license':
            // Driving License validation (Alphanumeric with special characters, minimum 6 characters)
            return validateDrivingLicense($id_proof_number);
        // Add more cases for other ID types if needed
        default:
            // Default case if ID type is not recognized
            return false;
    }
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

// Handle form submissions and database operations for the visitor module
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_visitor'])) {
        $visitor_name = $_POST['visitor_name'];
        $id_proof_type = $_POST['id_proof_type'];
        $id_proof_number = $_POST['id_proof_number'];
        $criminal_name = $_POST['criminal_name'];
        $relation = $_POST['relation'];
        $visit_date = $_POST['visit_date'];
        $visit_time = $_POST['visit_time'];

        // Check if the criminal name exists in the database
        $criminal_check_query = "SELECT * FROM inmates WHERE criminal_name = '$criminal_name'";
        $criminal_check_result = $conn->query($criminal_check_query);
        if ($criminal_check_result->num_rows == 0) {
            echo "<script>alert('The criminal you are trying to visit does not exist in the database.');</script>";
        } else {
            // Check if the ID proof number contains only integers
            if (!validateIDProofNumber($id_proof_type, $id_proof_number)) {
                echo "<script>alert('Invalid ID proof number format for the selected ID type.');</script>";
            } else {
                // Check if the visitor has already visited twice this month
                $month = date('m', strtotime($visit_date));
                $year = date('Y', strtotime($visit_date));
                $query = "SELECT COUNT(*) AS visit_count FROM visitors WHERE MONTH(visit_date) = $month AND YEAR(visit_date) = $year AND visitor_name = '$visitor_name' AND criminal_name = '$criminal_name'";
                $result = $conn->query($query);
                $row = $result->fetch_assoc();
                $visit_count = $row['visit_count'];

                if ($visit_count >= 2) {
                    echo "<script>alert('This visitor has already visited twice this month.');</script>";
                } else {
                    // Insert new visitor
                    $query = "INSERT INTO visitors (visitor_name, id_proof_type, id_proof_number, criminal_name, relation, visit_date, visit_time) VALUES ('$visitor_name', '$id_proof_type', '$id_proof_number', '$criminal_name', '$relation', '$visit_date', '$visit_time')";
                    
                    // Execute the query
                    if ($conn->query($query) === TRUE) {
                        echo "<script>alert('New visitor created successfully');</script>";
                    } else {
                        $error = "Error creating new visitor: " . $conn->error;
                    }
                }
            }
        }
    } elseif (isset($_POST['delete_visitor'])) {
        $id = $_POST['id'];

        // Delete visitor
        $query = "DELETE FROM visitors WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            echo "<script>alert('Visitor deleted successfully');</script>";
        } else {
            $error = "Error deleting visitor: " . $conn->error;
        }
    }
}

// Retrieve data for visitors
$query = "SELECT * FROM visitors";
$result = $conn->query($query);
$visitors = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prison Management System - Visitor Module</title>
    <link rel="stylesheet" type="text/css" href="vistor.css">
    <script>
        function validateForm() {
            var visitorName = document.getElementById("visitor_name").value;
            var criminalName = document.getElementById("criminal_name").value;
            var idProofType = document.getElementById("id_proof_type").value;
            var idProofNumber = document.getElementById("id_proof_number").value;

            var letters = /^[a-zA-Z\s]+$/;

            if (!visitorName.match(letters)) {
                alert("Visitor name must contain only alphabets.");
                return false;
            }

            if (!criminalName.match(letters)) {
                alert("Criminal name must contain only alphabets.");
                return false;
            }

            // Validate ID proof number based on ID proof type
            if (idProofType === 'adhaar' && !idProofNumber.match(/^\d{12}$/)) {
                alert("Invalid Aadhaar number format. It must be 12 digits.");
                return false;
            } else if (idProofType === 'voter_id' && !idProofNumber.match(/^[a-zA-Z0-9]{10}$/)) {
                alert("Invalid Voter ID format. It must be 10 alphanumeric characters.");
                return false;
            } else if (idProofType === 'driving_license' && !idProofNumber.match(/^[a-zA-Z0-9@#$%^&*()\-_=+\\|\'"\/,.<>?:; ]{6,}$/)) {
                alert("Invalid Driving License format. It must be alphanumeric with special characters, minimum 6 characters.");
                return false;
            }

            return true;
        }

        // Function to display pop-up messages for adding and deleting visitors
        // function showPopUp(message) {
        //     alert(message);
        // }
    </script>
</head>
<body>
  <header>
      <h1>Visitor Module</h1>
      <?php if (isset($success)) echo '<script>showPopUp("' . $success . '");</script>'; ?>
      <?php if (isset($error)) echo '<script>showPopUp("' . $error . '");</script>'; ?>
  </header><br>
  <h2>Visitors</h2>
    <table>
        <tr>
            <th>Visitor Name</th>
            <th>ID Proof Type</th>
            <th>ID Proof Number</th>
            <th>Criminal Name</th>
            <th>Relation</th>
            <th>Visit Date</th>
            <th>Visit Time</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($visitors as $visitor): ?>
        <tr>
            <td><?php echo $visitor['visitor_name']; ?></td>
            <td><?php echo $visitor['id_proof_type']; ?></td>
            <td><?php echo $visitor['id_proof_number']; ?></td>
            <td><?php echo $visitor['criminal_name']; ?></td>
            <td><?php echo $visitor['relation']; ?></td>
            <td><?php echo $visitor['visit_date']; ?></td>
            <td><?php echo $visitor['visit_time']; ?></td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $visitor['id']; ?>">
                    <input type="submit" name="delete_visitor" value="Delete" onclick="showPopUp('Visitor deleted successfully.')">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<br><br><br><br>
    <h2>Create New Visitor</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validateForm()">
        <label for="visitor_name">Visitor Name:</label>
        <input type="text" name="visitor_name" id="visitor_name" required>
        <br>
        <label for="id_proof_type">ID Proof Type:</label>
        <select name="id_proof_type" id="id_proof_type" required>
            <option value="">Choose....</option>
            <option value="adhaar">Adhaar</option>
            <option value="voter_id">Voter ID</option>
            <option value="driving_license">Driving License</option>
            <!-- Add more options as needed -->
        </select>
        <br>
        <label for="id_proof_number">ID Proof Number:</label>
        <input type="text" name="id_proof_number" id="id_proof_number" required>
        <br>
        <label for="criminal_name">Criminal Name:</label>
        <input type="text" name="criminal_name" id="criminal_name" required>
        <br>
        <label for="relation">Relation:</label>
        <input type="text" name="relation" id="relation" required>
        <br>
        <label for="visit_date">Visit Date:</label>
        <input type="date" name="visit_date" id="visit_date" required>
        <br>
        <label for="visit_time">Visit Time:</label>
        <input type="time" name="visit_time" id="visit_time" required>
        <br>
        <input type="submit" name="create_visitor" value="Create Visitor">
    </form>

    <a href="index.php">Back to Dashboard</a>
</body>
</html>
