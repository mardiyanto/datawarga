<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['signature'])) {
        // Get the base64 signature
        $signatureData = $_POST['signature'];

        // Remove the "data:image/png;base64," part from the string
        $imageData = str_replace('data:image/png;base64,', '', $signatureData);
        $imageData = base64_decode($imageData);

        // Set the file path
        $filePath = 'signatures/signature_' . time() . '.png';

        // Save the image to the server
        if (file_put_contents($filePath, $imageData)) {
            echo json_encode(["message" => "Signature saved successfully!"]);
        } else {
            echo json_encode(["message" => "Failed to save signature."]);
        }
    } else {
        echo json_encode(["message" => "No signature received."]);
    }
} else {
    echo json_encode(["message" => "Invalid request."]);
}
?>
