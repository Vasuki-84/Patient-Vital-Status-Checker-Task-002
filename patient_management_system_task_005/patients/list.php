<?php

include("../config/db.php");
include("../includes/header.php");

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$limit = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$offset = ($page - 1) * $limit;

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

/*
|--------------------------------------------------------------------------
| SORTING
|--------------------------------------------------------------------------
*/

$allowedSorts = [

    "patient_name ASC",
    "patient_name DESC",
    "age ASC",
    "age DESC"

];

$sort = isset($_GET['sort']) ? $_GET['sort'] : "patient_name ASC";

/*
|--------------------------------------------------------------------------
| VALIDATE SORT VALUE
|--------------------------------------------------------------------------
*/

if (!in_array($sort, $allowedSorts)) {

    $sort = "patient_name ASC";
}

/*
|--------------------------------------------------------------------------
| SEARCH PATTERN
|--------------------------------------------------------------------------
*/

$searchTerm = "%$search%";

/*
|--------------------------------------------------------------------------
| PREPARED STATEMENT QUERY
|--------------------------------------------------------------------------
*/

$sql = "SELECT patients.*,

        doctors.doctor_name,
        doctors.specialization

FROM patients

LEFT JOIN doctors

ON patients.doctor_id = doctors.id

WHERE patient_name LIKE ?
OR diagnosis LIKE ?

ORDER BY $sort

LIMIT ?, ?";

/*
|--------------------------------------------------------------------------
| PREPARE QUERY
|--------------------------------------------------------------------------
*/

$stmt = mysqli_prepare($conn, $sql);

/*
|--------------------------------------------------------------------------
| BIND PARAMETERS
|--------------------------------------------------------------------------
*/

mysqli_stmt_bind_param(

    $stmt,
    "ssii",
    $searchTerm,
    $searchTerm,
    $offset,
    $limit

);

/*
|--------------------------------------------------------------------------
| EXECUTE QUERY
|--------------------------------------------------------------------------
*/

mysqli_stmt_execute($stmt);

/*
|--------------------------------------------------------------------------
| GET RESULT
|--------------------------------------------------------------------------
*/

$result = mysqli_stmt_get_result($stmt);

/*
|--------------------------------------------------------------------------
| TOTAL RECORDS
|--------------------------------------------------------------------------
*/

$totalQuery = "SELECT COUNT(*) as total FROM patients";

$totalResult = mysqli_query($conn, $totalQuery);

$totalRow = mysqli_fetch_assoc($totalResult);

$totalPages = ceil($totalRow['total'] / $limit);

?>

<div class="d-flex justify-content-between mb-3">

    <a href="create.php" class="btn btn-success">
        Add Patient
    </a>

    <form method="GET" class="d-flex">

        <input type="text"
               name="search"
               class="form-control me-2"
               placeholder="Search"
               value="<?php echo htmlspecialchars($search); ?>">  

        <div class="col-md-4 col-sm-6 me-3">

            <select name="sort"
                    class="form-select shadow-sm border-2 rounded-3 form-control me-2">

                <option selected disabled>
                    Sort Patients
                </option>

                <option value="patient_name ASC">
                    🔤 Name A-Z
                </option>

                <option value="patient_name DESC">
                    🔠 Name Z-A
                </option>

                <option value="age ASC">
                    👶 Age Low-High
                </option>

                <option value="age DESC">
                    👴 Age High-Low
                </option>

            </select>

        </div>

        <button class="btn btn-primary">
            Search
        </button>

    </form>

</div>

<table class="table table-bordered table-hover">

<tr class="table-secondary">

<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Age</th>
<th>Gender</th>
<th>Doctor</th>
<th>Diagnosis</th>
<th>Actions</th>

</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>

<tr>

    <td><?php echo $row['id']; ?></td>

    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>

    <td><?php echo htmlspecialchars($row['email']); ?></td>

    <td><?php echo htmlspecialchars($row['phone']); ?></td>

    <td><?php echo htmlspecialchars($row['age']); ?></td>

    <td><?php echo htmlspecialchars($row['gender']); ?></td>

    <td>

    <?php echo htmlspecialchars($row['doctor_name']); ?>

    -

    <?php echo htmlspecialchars($row['specialization']); ?>

</td>

    <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>

    <td>

        <div class="d-flex flex-column flex-sm-row gap-2">

            <a href="edit.php?id=<?php echo base64_encode($row['id']); ?>"
               class="btn btn-warning btn-sm">

                Edit

            </a>

            <a href="delete.php?id=<?php echo base64_encode($row['id']); ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this patient?')">

                Delete

            </a>

        </div>

    </td>

</tr>

<?php } ?>

</table>

<!-- Pagination -->

<nav>

<ul class="pagination">

<?php if ($page > 1) { ?>

<li class="page-item">

<a class="page-link"
href="?page=<?php echo $page - 1; ?>">

Previous

</a>

</li>

<?php } ?>

<?php if ($page < $totalPages) { ?>

<li class="page-item">

<a class="page-link"
href="?page=<?php echo $page + 1; ?>">

Next

</a>

</li>

<?php } ?>

</ul>

</nav>

<?php include("../includes/footer.php"); ?>