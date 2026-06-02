# Patient Vital Status Checker

## Project Overview
The **Patient Vital Status Checker** is a simple PHP healthcare validation project.

This project validates patient:
- Temperature
- Pulse
- Blood Pressure

The project demonstrates:
- Higher-Order Functions
- Callback Functions
- Recursive Functions
- Clinical Data Validation

---

# Project Structure

patient_vital_checker/
│── index.php
│── vitals.php
│── validate.php
│── rules.php
│── scanner.php
└── README.md

---

# Project Flow

vitals.php → index.php → validate.php → rules.php → output → scanner.php

---

# File Explanation

---

## 1. vitals.php

This file stores patient data in an array.

```php
$vitals = [
    [
        "patient_name" => "Arun",
        "vital_type" => "Temperature",
        "value" => 102
    ]
];
