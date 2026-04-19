<?php

function calculateTuition($conn, $student_id, $semester, $school_year) {

    // GET STUDENT
    $sql = "SELECT education_level, program, year_level 
            FROM student_list WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    // GET GPA
    $sql = "SELECT gpa FROM student_gpa 
            WHERE student_id = ? 
            ORDER BY id DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $gpa = $stmt->get_result()->fetch_assoc()['gpa'];

    // BASE TUITION
    $tuition = 0;

    if ($student['education_level'] == "college") {

        if ($student['program'] == "BSIT") {
            $tuition = 2000;
        } elseif ($student['program'] == "ACT") {
            $tuition = 1500;
        } else {
            $tuition = 1800;
        }

        // YEAR adjustment
        if ($student['year_level'] == "2nd year") {
            $tuition += 500;
        } elseif ($student['year_level'] == "3rd year") {
            $tuition += 1000;
        } elseif ($student['year_level'] == "4th year") {
            $tuition += 1500;
        }

        // SEMESTER adjustment
        if ($semester == "2nd Sem") {
            $tuition += $tuition + ($tuition * 0.20);
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