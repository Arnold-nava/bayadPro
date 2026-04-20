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
    $sql = "SELECT gpa FROM student_gpa 
            WHERE student_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $gpaRow = $stmt->get_result()->fetch_assoc();
    $gpa = $gpaRow ? $gpaRow['gpa'] : 0;

    // BASE TUITION
    $tuition = 0;

    if ($student['education_level'] == "college") {

        switch ($student['program']) {
            case "BSIT":  $tuition = 2000;  break;
            case "ACT":   $tuition = 13000; break;
            case "BSBA":  $tuition = 15000; break;
            case "HM":    $tuition = 25000; break;
            default:      $tuition = 1800;
        }

        switch ($student['year_level']) {
            case "2nd year": $tuition += 500;  break;
            case "3rd year": $tuition += 1000; break;
            case "4th year": $tuition += 1500; break;
        }

        if ($semester == "2nd Sem") {
            $tuition += ($tuition * 0.20);
        }

    } else {
        $tuition = 1000;
    }

    // GPA DISCOUNT
    $discountRate = 0;
    if ($gpa <= 1.25)      $discountRate = 0.50;
    elseif ($gpa <= 1.50)  $discountRate = 0.30;
    elseif ($gpa <= 2.00)  $discountRate = 0.10;

    $discountAmount = $tuition * $discountRate;
    $finalTuition   = $tuition - $discountAmount;

    // ✅ CHECK IF TUITION RECORD ALREADY EXISTS FOR THIS SEMESTER/YEAR
    $sql = "SELECT id FROM student_tuition 
            WHERE student_id = ? AND semester = ? AND school_year = ? 
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $student_id, $semester, $school_year);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();

    // ✅ INSERT ONLY IF NOT YET EXISTING
    if (!$existing) {
        $sql = "INSERT INTO student_tuition 
                (student_id, semester, school_year, base_tuition, discount_amount, final_tuition)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issddd", $student_id, $semester, $school_year, $tuition, $discountAmount, $finalTuition);
        $stmt->execute();
    }
    

    return [
        "base"     => $tuition,
        "discount" => $discountAmount,
        "final"    => $finalTuition
    ];
}
?>