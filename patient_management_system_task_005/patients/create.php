<?php

include("../config/db.php");
/*
|--------------------------------------------------------------------------
| FETCH DOCTORS
|--------------------------------------------------------------------------
*/

$doctorQuery = "SELECT * FROM doctors";

$doctorResult = mysqli_query($conn, $doctorQuery);

$error = "";
$success = "";

if (isset($_POST['submit'])) {

    /*
    |--------------------------------------------------------------------------
    | GET FORM DATA
    |--------------------------------------------------------------------------
    */

    $patient_name = trim($_POST['patient_name']);  // $POST - super global array.
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $doctor_id = trim($_POST['doctor_id']);
    $diagnosis = trim($_POST['diagnosis']);

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        empty($patient_name) ||
        empty($email) ||
        empty($phone) ||
        empty($age) ||
        empty($gender) ||
        empty($doctor_id) ||
        empty($diagnosis)
    ) {

        $error = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Invalid email format.";

    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

        $error = "Phone number must contain 10 digits.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | CHECK UNIQUE EMAIL USING PREPARED STATEMENT
        |--------------------------------------------------------------------------
        */

        $checkEmailQuery = "SELECT id FROM patients WHERE email = ?";  // ? - placeholder marker - Value later varum

        $checkStmt = mysqli_prepare($conn, $checkEmailQuery);  // Database query structure prepare pannum

        mysqli_stmt_bind_param($checkStmt, "s", $email); // Value attach pannrom

        mysqli_stmt_execute($checkStmt);  // run panni email exists ahh eruka nu check pandrom

        $checkResult = mysqli_stmt_get_result($checkStmt);  // result store pandrom 

        /*
        |--------------------------------------------------------------------------
        | EMAIL EXISTS CHECK
        |--------------------------------------------------------------------------
        */

        if (mysqli_num_rows($checkResult) > 0) {  // How many rows came from database

            $error = "Email already exists.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | INSERT PATIENT USING PREPARED STATEMENT
            |--------------------------------------------------------------------------
            */

            $insertQuery = "INSERT INTO patients

            (patient_name, email, phone, age, gender, doctor_id, diagnosis)

            VALUES (?, ?, ?, ?, ?, ?, ?)";

            $insertStmt = mysqli_prepare($conn, $insertQuery);

            mysqli_stmt_bind_param(

    $insertStmt,
    "sssisis",
    $patient_name,
    $email,
    $phone,
    $age,
    $gender,
    $doctor_id,
    $diagnosis

);

            /*
            |--------------------------------------------------------------------------
            | EXECUTE INSERT QUERY
            |--------------------------------------------------------------------------
            */

            if (mysqli_stmt_execute($insertStmt)) {

                $success = "Patient added successfully.";

            } else {

                $error = "Something went wrong.";
            }
        }
    }
}

include("../includes/header.php");

?>

<div class="form-container">

    <a href="list.php" class="btn btn-secondary mb-3">
        Back
    </a>

    <?php if ($error) { ?>

        <div class="alert alert-danger">

            <?php echo $error; ?>

        </div>

    <?php } ?>

    <?php if ($success) { ?>

        <div class="alert alert-success">

            <?php echo $success; ?>

        </div>

    <?php } ?>

    <form method="POST">

        <div class="mb-3">

            <label>Patient Name</label>

            <input type="text"
                   name="patient_name"
                   class="form-control"
                   required>

        </div>

        <div class="mb-3">

            <label>Email</label>

            <input type="email"
                   name="email"
                   class="form-control"
                   required>

        </div>

        <div class="mb-3">

            <label>Phone</label>

            <input type="text"
                   name="phone"
                   class="form-control"
                   required>

        </div>

        <div class="mb-3">

            <label>Age</label>

            <input type="number"
                   name="age"
                   class="form-control"
                   required>

        </div>

        <div class="mb-3">

            <label>Gender</label>

            <select name="gender"
                    class="form-control"
                    required>

                <option value="">
                    Select
                </option>

                <option value="Male">
                    Male
                </option>

                <option value="Female">
                    Female
                </option>

                <option value="Other">
                    Other
                </option>

            </select>

        </div>

          <div class="mb-3">

            <label>Select Doctor</label>

            <select name="doctor_id"
                    class="form-control"
                    required>

                <option value="">
                    Select Doctor
                </option>

                <?php while ($doctor = mysqli_fetch_assoc($doctorResult)) { ?>

                    <option value="<?php echo $doctor['id']; ?>">

                        <?php echo $doctor['doctor_name']; ?>

                        -

                        <?php echo $doctor['specialization']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>

        <div class="mb-3">

            <label>Diagnosis</label>

            <textarea name="diagnosis"
                      class="form-control"
                      required></textarea>

        </div>

        <button type="submit"
                name="submit"
                class="btn btn-primary">

            Add Patient

        </button>

    </form>

</div>

<?php include("../includes/footer.php"); ?>