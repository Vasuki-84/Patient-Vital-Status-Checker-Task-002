<?php

function checkTemperature($data)
{
    if ($data["value"] > 100) {
        $data["status"] = "HIGH";
        $data["message"] = "Fever detected";
    } elseif ($data["value"] < 97) {
        $data["status"] = "LOW";
        $data["message"] = "Temperature low";
    } else {
        $data["status"] = "NORMAL";
        $data["message"] = "Temperature normal";
    }

    return $data;
}

function checkPulse($data)
{
    if ($data["value"] > 100) {
        $data["status"] = "HIGH";
        $data["message"] = "Pulse rate high";
    } elseif ($data["value"] < 60) {
        $data["status"] = "LOW";
        $data["message"] = "Pulse rate low";
    } else {
        $data["status"] = "NORMAL";
        $data["message"] = "Pulse rate normal";
    }

    return $data;
}

function checkBloodPressure($data)
{
    list($sys, $dia) = explode("/", $data["value"]);

    if ($sys > 130 || $dia > 90) {
        $data["status"] = "HIGH";
        $data["message"] = "Blood pressure high";
    } elseif ($sys < 90 || $dia < 60) {
        $data["status"] = "LOW";
        $data["message"] = "Blood pressure low";
    } else {
        $data["status"] = "NORMAL";
        $data["message"] = "Blood pressure normal";
    }

    return $data;
}
?>