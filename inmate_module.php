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

$cell_blocks = array("1", "2", "3", "4", "5", "6" , "7" , "8" , "9" , "10");
// Handle form submissions and database operations for the inmate management module
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_inmate'])) {
        $criminal_name = $_POST['criminal_name'];
        $crime = $_POST['crime'];
        $adhar_number = $_POST['adhar_number'];
        $belongings = $_POST['belongings'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $cell_block = $_POST['cell_block'];
        $joining_date = $_POST['joining_date'];
        $sentence_duration = $_POST['sentence_duration'];
        $leaving_date = date('Y-m-d', strtotime("+$sentence_duration months", strtotime($joining_date)));
        $court_name = $_POST['court_name'];
        $crime_history = isset($_POST['crime_history']) ? 'Yes' : 'No';
        $previous_crime = $_POST['previous_crime'];
        if (!ctype_digit($adhar_number)) {
            echo "<script>alert('Aadhar number must be a numeric value.');</script>";
        } elseif (strlen($adhar_number) !== 12) {
            echo "<script>alert('Aadhar number must be a 12-digit integer.');</script>";
        }  else {
        // Check if an inmate with the same Aadhar number already exists
        $check_query = "SELECT * FROM inmates WHERE adhar_number = '$adhar_number'";
        $result = $conn->query($check_query);
        if ($result->num_rows > 0) {
            echo "<script>alert('An inmate with the same Aadhar number already exists.');</script>";
        } else {
            // Check if the inmate's age is above 18
            if ($age < 18 || $age > 80) {
                echo "<script>alert('Inmate age must be between 18 to 80.');</script>";
            } else {
                // Check if joining date and leaving date are the same
                if ($joining_date === $leaving_date) {
                    echo "<script>alert('Joining date and leaving date cannot be the same.');</script>";
                } else {
                    // Check if the cell block has already reached the maximum capacity
                    $count_query = "SELECT COUNT(*) AS count FROM inmates WHERE cell_block = '$cell_block'";
                    $count_result = $conn->query($count_query);
                    $count_row = $count_result->fetch_assoc();
                    $count = $count_row['count'];
                    if ($count >= 3) {
                        echo "<script>alert('Maximum capacity reached for this cell block.');</script>";
                    } else {
                        // Check if there are inmates of the opposite gender in the same cell block
                        $opposite_gender = ($gender === 'male') ? 'female' : 'male';
                        $gender_check_query = "SELECT * FROM inmates WHERE cell_block = '$cell_block' AND gender = '$opposite_gender'";
                        $gender_check_result = $conn->query($gender_check_query);
                        if ($gender_check_result->num_rows > 0) {
                            echo "<script>alert('Cannot have inmates of both genders in the same cell block.');</script>";
                        } else {
                            // Validate criminal name
                            if (!preg_match('/^[a-zA-Z\s]+$/', $criminal_name)) {
                                echo "<script>alert('Criminal name must contain only alphabets only.');</script>";
                            }
                             elseif (!in_array($cell_block, $cell_blocks)) {
                                 echo "<script>alert('Invalid cell block.');</script>";
                             } else {
                                // Insert new inmate
                                $query = "INSERT INTO inmates (criminal_name, crime, adhar_number, belongings, gender, age, cell_block, joining_date, sentence_duration, leaving_date, court_name, crime_history, previous_crime)
                                          VALUES ('$criminal_name', '$crime', '$adhar_number', '$belongings', '$gender', $age, '$cell_block', '$joining_date', '$sentence_duration', '$leaving_date', '$court_name', '$crime_history', '$previous_crime')";
                                if ($conn->query($query) === TRUE) {
                                    echo "<script>alert('New inmate created successfully');</script>";
                                } else {
                                    $error = "Error creating new inmate: " . $conn->error;
                                }
                        }
                    }
                    }
                }
            }
        }
    }
    } elseif (isset($_POST['update_inmate'])) {
        $id = $_POST['id'];
        $criminal_name = $_POST['criminal_name'];
        $crime = $_POST['crime'];
        // $adhar_number = $_POST['adhar_number'];
        $belongings = $_POST['belongings'];
        $gender = $_POST['gender'];
        // $age = $_POST['age'];
        $cell_block = $_POST['cell_block'];
        $joining_date = $_POST['joining_date'];
        $sentence_duration = $_POST['sentence_duration'];
        $leaving_date = date('Y-m-d', strtotime("+$sentence_duration months", strtotime($joining_date)));
        $court_name = $_POST['court_name'];
        $crime_history = isset($_POST['crime_history']) ? 'Yes' : 'No';
        $previous_crime = $_POST['previous_crime'];
            // Check if the inmate's age is above 18
            // if ($age < 18) {
            //     echo "<script>alert('Inmate must be 18 years or older.');</script>";
            // }
            // else {
                // Check if joining date and leaving date are the same
                if ($joining_date === $leaving_date) {
                    echo "<script>alert('Joining date and leaving date cannot be the same.');</script>";
                } else {
                    // Check if the cell block has already reached the maximum capacity
                    $count_query = "SELECT COUNT(*) AS count FROM inmates WHERE cell_block = '$cell_block'";
                    $count_result = $conn->query($count_query);
                    $count_row = $count_result->fetch_assoc();
                    $count = $count_row['count'];
                    if ($count >= 3) {
                        echo "<script>alert('Maximum capacity reached for this cell block.');</script>";
                    } else {
                        // Check if there are inmates of the opposite gender in the same cell block
                        $opposite_gender = ($gender === 'male') ? 'female' : 'male';
                        $gender_check_query = "SELECT * FROM inmates WHERE cell_block = '$cell_block' AND gender = '$opposite_gender'";
                        $gender_check_result = $conn->query($gender_check_query);
                        if ($gender_check_result->num_rows > 0) {
                            echo "<script>alert('Cannot have inmates of both genders in the same cell block.');</script>";
                        } else {
                            // Validate criminal name
                            if (!preg_match('/^[a-zA-Z\s]+$/', $criminal_name)) {
                                echo "<script>alert('Criminal name must contain only alphabets only.');</script>";
                            }
                            if (!in_array($cell_block, $cell_blocks)) {
                                echo "<script>alert('Invalid cell block.');</script>";
                            } else {
                                // Updateinmate
                                $query = "UPDATE inmates SET criminal_name = '$criminal_name', crime = '$crime', belongings = '$belongings', gender = '$gender', cell_block = '$cell_block', joining_date = '$joining_date', sentence_duration = '$sentence_duration', leaving_date = '$leaving_date', court_name = '$court_name', crime_history = '$crime_history', previous_crime = '$previous_crime' WHERE id = $id";
                                if ($conn->query($query) === TRUE) 
                                   {echo "<script>alert('Inmate updated successfully');</script>"; } 
                                else {   $error = "Error updatinginmate: " . $conn->error;
                                }
                        }
                    }
                    }
                }
            // }
    }
    elseif (isset($_POST['delete_inmate'])) {
        $id = $_POST['id'];
        // Delete visitor
        $query = "DELETE FROM inmates WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            echo "<script>alert('Inmate deleted successfully');</script>";
        } else {
            $error = "Error deleting inmate: " . $conn->error;
        }
    }
}
// Retrieve data for inmates
$query = "SELECT * FROM inmates";
$result = $conn->query($query);
$inmates = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prison Management System - Inmate Management</title>
    <link rel="stylesheet" type="text/css" href="inmate.css">
</head>
<body>
    <header>
        <h1>Inmate Management</h1>
        <?php if (isset($success)) echo '<p>' . $success . '</p>'; ?>
        <?php if (isset($error)) echo '<p>' . $error . '</p>'; ?>
    </header><br>
    <h2>Inmates</h2>
    <table>
        <tr>
            <th>Criminal Name</th>
            <th>Aadhar Number</th>
            <th>Crime</th>
            <th>Release Status</th>
            <th>Cell Block</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($inmates as $inmate): ?>
        <tr>
            <td><?php echo $inmate['criminal_name']; ?></td>
            <td><?php echo $inmate['adhar_number']; ?></td>
            <td><?php echo $inmate['crime']; ?></td>
            <td><?php echo $inmate['release_status']; ?></td>
            <td><?php echo $inmate['cell_block']; ?></td>
            <td>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $inmate['id']; ?>">
                    <input type="submit" name="delete_inmate" value="Delete" onclick="showPopUp('Inmate deleted successfully.')">
                </form>
                <button id="btn" onclick="editInmate(<?php echo $inmate['id']; ?>, '<?php echo $inmate['criminal_name']; ?>', '<?php echo $inmate['crime']; ?>', '<?php echo $inmate['adhar_number']; ?>', '<?php echo $inmate['belongings']; ?>', '<?php echo $inmate['gender']; ?>', <?php echo $inmate['age']; ?>, '<?php echo $inmate['cell_block']; ?>', '<?php echo $inmate['joining_date']; ?>', '<?php echo $inmate['sentence_duration']; ?>', '<?php echo $inmate['leaving_date']; ?>', '<?php echo $inmate['court_name']; ?>', '<?php echo $inmate['crime_history']; ?>', '<?php echo $inmate['previous_crime']; ?>')">Edit</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table><br>
    <h2>Create New Inmate</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="criminal_name">Criminal Name:</label>
        <input type="text" name="criminal_name" id="criminal_name" placeholder="Enter the name of Inmate" required>
        <br>
        <label for="crime">Crime:</label>
        <select name="crime" id="crime" required>
            <option value="">Choose....</option>
            <option value="Theft">Theft</option>
            <option value="Murder">Murder</option>
            <option value="Assault">Assault</option>
            <option value="Drug Trafficking">Drug Trafficking</option>
        </select>
        <br>
        <label for="adhar_number">Adhar Number:</label>
        <input type="text" name="adhar_number" id="adhar_number" placeholder="Enter the Aadhar Number of Inmate" required>
        <br>
        <label for="belongings">Belongings:</label>
        <input type="text" name="belongings" id="belongings" placeholder="Please enter the belongings of Inmate" required>
        <br>
        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="">Choose....</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
        <br>
        <label for="age">Age:</label>
        <input type="number" name="age" id="age" placeholder="Please enter the age of inmate ( age should be>18 )" required min="18">
        <br>
        <label for="cell_block">Cell Block:</label>
        <input type="text" name="cell_block" id="cell_block" placeholder="Please enter the cell block allocated to Inmate" required>
        <br>
        <label for="joining_date">Joining Date:</label>
        <input type="date" name="joining_date" id="joining_date" placeholder="Please enter the joining date of Inmate" required>
        <br>
        <label for="sentence_duration">Sentence Duration (in months):</label>
        <input type="number" name="sentence_duration" id="sentence_duration" placeholder="Please enter the Sentence Duration of Inmate" required min="1">
        <br>
        <label for="court_name">Court Name:</label>
        <select name="court_name" id="court_name" required>
            <option value="">Choose....</option>
            <option value="Supreme Court">Supreme Court</option>
            <option value="High Court">High Court</option>
            <option value="Session Court">Session Court</option>
        </select>
        <br>
        <label for="crime_history">Previous Crime History:</label>
        <input type="checkbox" name="crime_history" id="crime_history">
        <br>
        <label for="previous_crime">Previous Crime:</label>
        <input type="text" name="previous_crime" id="previous_crime" placeholder="Please enter the past crimes of Inmate (if any)" >
        <br>
        <label>
            <input type="checkbox" name="agreement" required> I agree to the provided information
        </label>
        <br>
        <input type="submit" name="create_inmate" value="Create Inmate">
    </form>
    <div id="editInmateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Inmate</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="id" id="editId">
                <label for="editCriminalName">Criminal Name:</label>
                <input type="text" name="criminal_name" id="editCriminalName" required>
                <br>
                <label for="editCrime">Crime:</label>
                <select name="crime" id="editCrime" required>
                  <option value="">Choose....</option>
                  <option value="Theft">Theft</option>
                  <option value="Murder">Murder</option>
                  <option value="Assault">Assault</option>
                <option value="Drug Trafficking">Drug Trafficking</option>
                </select>
                <br>
                <!-- <label for="editAdharNumber">Adhar Number:</label>
                <input type="text" name="adhar_number" id="editAdharNumber" required> -->
                <label for="editBelongings">Belongings:</label>
                <input type="text" name="belongings" id="editBelongings" required>
                <br>
                <label for="editGender">Gender:</label>
                <select name="gender" id="editGender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <br>
                <!-- <label for="editAge">Age:</label>
                <input type="number" name="age" id="editAge" required>
                <br> -->
                <label for="editCellBlock">Cell Block:</label>
                <input type="text" name="cell_block" id="editCellBlock" required>
                <br>
                <label for="editJoiningDate">Joining Date:</label>
                <input type="date" name="joining_date" id="editJoiningDate" required>
                <br>
                <label for="editSentenceDuration">Sentence Duration (in months):</label>
                <input type="number" name="sentence_duration" id="editSentenceDuration" required>
                <br>
                <label for="editCourtName">Court Name:</label>
                <select name="court_name" id="editCourtName" required>
                  <option value="">Choose....</option>
                  <option value="Supreme Court">Supreme Court</option>
                  <option value="High Court">High Court</option>
                  <option value="Session Court">Session Court</option>
                </select>
                <br>
                <label for="editCrimeHistory">Previous Crime History:</label>
                <input type="checkbox" name="crime_history" id="editCrimeHistory">
                <br>
                <label for="editPreviousCrime">Previous Crime:</label>
                <input type="text" name="previous_crime" id="editPreviousCrime">
                <br>
                <input type="submit" name="update_inmate" value="Update Inmate">
            </form>
        </div>
    </div>
    <script>
    // Get the modal
    var modal = document.getElementById("editInmateModal");
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
    // Function to open the edit inmate modal
    function editInmate(id, criminalName, crime, adharNumber, belongings, gender, age, cellBlock, joiningDate, sentenceDuration, leavingDate, courtName, crimeHistory, previousCrime) {
    modal.style.display = "block";
    document.getElementById("editId").value = id;
    document.getElementById("editCriminalName").value = criminalName;
    document.getElementById("editCrime").value = crime;
    document.getElementById("editBelongings").value = belongings;
    document.getElementById("editGender").value = gender;
    // document.getElementById("editAge").value = age;
    document.getElementById("editCellBlock").value = cellBlock;
    document.getElementById("editJoiningDate").value = joiningDate;
    document.getElementById("editSentenceDuration").value = sentenceDuration;
    document.getElementById("editCourtName").value = courtName;
    document.getElementById("editCrimeHistory").checked = (crimeHistory === 'Yes');
    document.getElementById("editPreviousCrime").value = previousCrime;
    }
    </script>
    <a href="index.php">Back to Dashboard</a>
</body>
</html>