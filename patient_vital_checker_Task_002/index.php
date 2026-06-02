<?php

include "vitals.php";
include "validate.php";
include "rules.php";
include "scanner.php";

foreach ($vitals as $vital) {

    if ($vital["vital_type"] == "Temperature") {
        $result = validateVital($vital, "checkTemperature");
    } elseif ($vital["vital_type"] == "Pulse") {
        $result = validateVital($vital, "checkPulse");
    } elseif ($vital["vital_type"] == "BP") {
        $result = validateVital($vital, "checkBloodPressure");
    }

    echo "Patient:" . $result["patient_name"] . "<br>";
    echo "Vital: " . $result["vital_type"] . "<br>";
    echo "Value: " . $result["value"] . "<br>";
    echo "Status: " . $result["status"] . "<br>";
    echo "Message: " . $result["message"] . "<br>";
    echo "----------------------<br>";
}

echo "<br><b>Project Files:</b><br>";
scanFolder(__DIR__);

?>