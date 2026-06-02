<?php

include("../config/db.php");

/*
|--------------------------------------------------------------------------
| FETCH DOCTORS
|--------------------------------------------------------------------------
*/

$doctorQuery = "SELECT * FROM doctors";

$doctorResult = mysqli_query($conn, $doctorQuery);

/*
|--------------------------------------------------------------------------
| GET PATIENT ID FROM URL
|--------------------------------------------------------------------------
*/

$id = base64_decode($_GET['id']);

/*
|--------------------------------------------------------------------------
| FETCH PATIENT DATA USING PREPARED STATEMENT
|--------------------------------------------------------------------------
*/

$selectQuery = "SELECT * FROM patients WHERE id = ?";

$stmt = mysqli_prepare($conn, $selectQuery);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);  // Result la iruka ONE row ah associative array-ah convert pannum

/*
|--------------------------------------------------------------------------
| UPDATE PATIENT
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'])) {

    $patient_name = trim($_POST['patient_name']);
    $phone = trim($_POST['phone']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $doctor_id = trim($_POST['doctor_id']);
    $diagnosis = trim($_POST['diagnosis']);
    /*
    |--------------------------------------------------------------------------
    | UPDATE QUERY USING PREPARED STATEMENT
    |--------------------------------------------------------------------------
    */

    $updateQuery = "UPDATE patients SET

    patient_name = ?,
    phone = ?,
    age = ?,
    gender = ?,
    doctor_id = ?,
    diagnosis = ?

    WHERE id = ?";

    $updateStmt = mysqli_prepare($conn, $updateQuery);

    mysqli_stmt_bind_param(  // already stored values ah query placeholders ku attach pannum
        $updateStmt,  // prepared statement reference

    "ssisisi", //   // datatype definition
    $patient_name,
    $phone,
    $age,
    $gender,
    $doctor_id,
    $diagnosis,
    $id
    );

    mysqli_stmt_execute($updateStmt);

    header("Location: list.php");
    exit;
}

include("../includes/header.php");

?>

<div class="form-container">

<form method="POST">

<div class="mb-3">

<label>Patient Name</label>

<input type="text"
       name="patient_name"
       class="form-control"
       value="<?php echo htmlspecialchars($row['patient_name']); ?>"
       required>

</div>

<div class="mb-3">

<label>Phone</label>

<input type="text"
       name="phone"
       class="form-control"
       value="<?php echo htmlspecialchars($row['phone']); ?>"
       required>

</div>

<div class="mb-3">

<label>Age</label>

<input type="number"
       name="age"
       class="form-control"
       value="<?php echo htmlspecialchars($row['age']); ?>"
       required>

</div>

<div class="mb-3">

<label>Gender</label>

<select name="gender" class="form-control">

<option value="Male"
<?php if ($row['gender'] == 'Male') echo 'selected'; ?>>
Male
</option>

<option value="Female"
<?php if ($row['gender'] == 'Female') echo 'selected'; ?>>
Female
</option>

<option value="Other"
<?php if ($row['gender'] == 'Other') echo 'selected'; ?>>
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

        <option value="<?php echo $doctor['id']; ?>"

            <?php
            if ($doctor['id'] == $row['doctor_id']) {
                echo "selected";
            }
            ?>

        >

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
          required><?php echo htmlspecialchars($row['diagnosis']); ?></textarea>

</div>

<button type="submit"
        name="update"
        class="btn btn-primary">

Update Patient

</button>

</form>

</div>

<?php include("../includes/footer.php"); ?>