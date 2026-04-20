<?php

function calculateTuition($conn, $student_id, $semester, $school_year) {

    // GET STUDENT INFO
    $sql = "SELECT education_level, program, year_level 
            FROM student_list WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    if (!$student) {
        return ["error" => "Student not found"];
    }

    // GET GPA
    $sql = "SELECT gpa 
            FROM student_gpa 
            WHERE student_id = ? 
            ORDER BY id DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $gpaRow = $stmt->get_result()->fetch_assoc();

    $gpa = $gpaRow ? $gpaRow['gpa'] : 0;

    // BASE TUITION
    $tuition = 0;

    if ($student['education_level'] == "college") {

        // PROGRAM BASE
        switch ($student['program']) {
            case "BSIT":
                $tuition = 2000;
                break;
            case "ACT":
                $tuition = 13000;
                break;
            default:
                $tuition = 1800;
        }

        // YEAR LEVEL ADDITION
        switch ($student['year_level']) {
            case "2nd year":
                $tuition += 500;
                break;
            case "3rd year":
                $tuition += 1000;
                break;
            case "4th year":
                $tuition += 1500;
                break;
        }

        // SEMESTER ADJUSTMENT
        if ($semester == "2nd Sem") {
            $tuition += ($tuition * 0.20);
        }

    } else {
        $tuition = 1000; // senior high
    }

    // GPA DISCOUNT
    $discountRate = 0;

    if ($gpa <= 1.25) {
        $discountRate = 0.50;
    } elseif ($gpa <= 1.50) {
        $discountRate = 0.30;
    } elseif ($gpa <= 2.00) {
        $discountRate = 0.10;
    }

    $discountAmount = $tuition * $discountRate;
    $finalTuition = $tuition - $discountAmount;

    return [
        "base" => $tuition,
        "discount" => $discountAmount,
        "final" => $finalTuition
    ];
}
?>