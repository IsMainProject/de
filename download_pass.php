<?php
require('fpdf/fpdf.php');
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); // Hide warnings and notices

if ($id > 0) {
    // Fetch user data from the database
    $sql = "SELECT * FROM user_data WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Create the PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Set margins
        $pdf->SetLeftMargin(10);
        $pdf->SetRightMargin(10);

        // Add logo to the right side of the page
        $pdf->Image('images/bel_logo.png', 160, 10, 30); // Adjust size and position
        $pdf->Ln(10);

        // Form Title
        $pdf->SetFont('Arial', 'B', 12); // Smaller font for fitting
        $pdf->Cell(0, 8, 'TEMPORARY PERMISSION FOR CONTRACT LABOUR / STAFF', 0, 1, 'C');
        $pdf->Cell(0, 8, 'TO WORK INSIDE FACTORY PREMISES', 0, 1, 'C');
        $pdf->Ln(8);

        // First Section: Header Text
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 8, "1. The Under Mentioned Contract Labour / Personnel / Staff (Total __________) of M/S:_________________", 0, 'L');

        // Address (use local_address key)
        $pdf->Cell(30, 8, 'Address:', 0, 0);
        $pdf->Cell(100, 8, isset($user['local_address']) ? $user['local_address'] : 'N/A', 0, 1);

        $pdf->Cell(60, 8, 'Contact Telephone No:', 0, 0);
        $pdf->Cell(100, 8, isset($user['mobile']) ? $user['mobile'] : '9455400781', 0, 1);

        // Multiline with space adjustments
        $pdf->MultiCell(0, 8, "To work inside Factory Premises In __________ Area for Work. Sh. _____________ is detailed to supervise the work and ensure good and orderly conduct of the labours inside the factory.", 0, 'L');
        $pdf->Ln(4);

        // Period of Work
        $pdf->Cell(10, 8, '2.', 0, 0);
        $pdf->Cell(50, 8, 'Period of work:', 0, 0);
        $pdf->Cell(100, 8, '______________ to _____________', 0, 1);

        // Timings
        $pdf->Cell(10, 8, '3.', 0, 0);
        $pdf->Cell(50, 8, 'Timings:', 0, 0);
        $pdf->Cell(100, 8, '______________ to _____________', 0, 1);

        // Supervisor
        $pdf->Cell(10, 8, '4.', 0, 0);
        $pdf->Cell(150, 8, 'St. No _________Sh. _________ is detailed to supervise this work. You are requested to give necessary permission.', 0, 1);
        $pdf->Ln(8);

        // Section 2: Details of Contract Personnel
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, 'DETAILS OF CONTRACT PERSONNEL', 0, 1, 'C');

        // Table Headers
        $pdf->SetFont('Arial', 'B', 9); // Smaller font for table
        $pdf->Cell(10, 8, 'Sl.', 1, 0, 'C');
        $pdf->Cell(30, 8, 'ID No', 1, 0, 'C');
        $pdf->Cell(60, 8, 'Name & Father Name', 1, 0, 'C');
        $pdf->Cell(50, 8, 'Current Address', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Age', 1, 1, 'C');

        // Table Row (Populated Data)
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(10, 8, '1', 1, 0, 'C');
        $pdf->Cell(30, 8, isset($user['adhar_no']) ? $user['adhar_no'] : 'N/A', 1, 0, 'C');
        $pdf->Cell(60, 8, $user['name'] . ' (' . $user['father_name'] . ')', 1, 0, 'C');
        $pdf->Cell(50, 8, isset($user['local_address']) ? $user['local_address'] : 'N/A', 1, 0, 'C');
        $pdf->Cell(20, 8, isset($user['age']) ? $user['age'] : 'N/A', 1, 1, 'C');

        // Instructions Section
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'I', 9); // Adjusted font size
        $pdf->MultiCell(0, 8, "Instructions:\n1. To be filled in duplicate. One copy will be retained by security.\n2. Permission to be granted for a maximum period of 6 days (Mon to Sat).\n3. Holiday permission to be taken separately.\n4. Supervision to be strictly ensured by dept concerned.\n5. Provide local/temporary address details of contractor/supervisor/labours.\n6. Contractor to ensure good conduct of the laborers.\n\nNote: All the information provided must be truthful and accurate.", 0, 'L');

        // Signatures
        $pdf->Ln(43);
        $pdf->Cell(60, 8, 'SR. SECURITY OFFICER', 0, 0, 'C');
        $pdf->Cell(0, 8, 'DIVISIONAL HEAD', 0, 1, 'C');

        // Output the PDF
        $pdf->Output('D', 'GatePass_Temporary.pdf');
    } else {
        echo 'No record found.';
    }
} else {
    echo 'Invalid request.';
}

$conn->close();
?>