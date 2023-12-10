<?php
ob_start(); // Start output buffering

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle image download
    if (isset($_POST['images']) && !empty($_POST['images'])) {
        $imageDirectory = 'assert/';

        $zip = new ZipArchive();
        $zipFile = 'selected_images.zip';

        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            foreach ($_POST['images'] as $image) {
                $imagePath = $imageDirectory . $image;
                $zip->addFile($imagePath, $image);
            }
            $zip->close();

            // Offer download of the zip file
            if (file_exists($zipFile)) {
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
                header('Content-Length: ' . filesize($zipFile));
                readfile($zipFile);
                unlink($zipFile); // Delete the zip file after download
                exit;
            }
        }
    }
}
ob_end_flush(); // End output buffering and flush the output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select and Download Images</title>
    <link rel="stylesheet" href="styles.css"> <!-- Linking the external CSS file -->
    <script>
        function displayImage(src) {
            document.getElementById('image-preview').src = src;
        }
    </script>
</head>
<body>
<form method="post">
    <h2>Select Images to Download</h2>
    <div class="gallery">
        <?php
        $imageDirectory = 'assert/';
        $files = scandir($imageDirectory);

        $imageFiles = array_filter($files, function ($file) {
            return preg_match("/\.(jpg|jpeg|png|gif)$/i", $file);
        });

        foreach ($imageFiles as $image) {
            echo '<div class="image-container">';
            echo '<img src="' . $imageDirectory . $image . '" alt="' . $image . '" onclick="displayImage(\'' . $imageDirectory . $image . '\')"> ';
            echo '<label><input type="checkbox" name="images[]" value="' . $image . '"> ' . $image . '</label>';
            echo '</div>';
        }
        ?>
    </div>
    <input type="submit" name="download" value="Download Selected Images">
</form>
</body>
</html>
