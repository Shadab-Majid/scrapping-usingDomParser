<?php

//we choose to display the content as plain text
header("Content-Type: text/plain");

include "simple_html_dom.php";

// Create a DOM object
$html = file_get_html('https://www.nextgeeks.in/');

// Extract data
$title = $html->find('title', 0)->plaintext;

$images = [];
foreach ($html->find('img') as $img) {
    $src = $img->src;
    $alt = $img->alt;
    $images[] = ['src' => $src, 'alt' => $alt];
}

$description = $html->find('meta[name="description"]', 0)->content;

// Print or store the scraped data
echo "Title: $title\n";
foreach ($images as $image) {
    echo "Image Source: {$image['src']}\n";
    echo "Image Name (Alt): {$image['alt']}\n";
}

// storing into database 

// ... (Your existing code to scrape data)

include 'config.php';

// Insert scraped data into the database
if ($con) {
    // Prepare an SQL statement
    $insertSql = "INSERT INTO scraped_data (title, img_src, img_alt, description) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($insertSql);

    // Bind parameters
    $stmt->bind_param("ssss", $title, $imageSrc, $imageAlt, $description);

    // Loop through images and insert data
    foreach ($images as $image) {
        $imageSrc = $image['src'];
        $imageAlt = $image['alt'];

        // Execute the statement
        if ($stmt->execute()) {
            echo "Scraped data inserted successfully into the database.\n";
        } else {
            echo "Error inserting scraped data: " . $stmt->error;
        }
    }

    // Close the database connection
    $con->close();
} else {
    echo "Database connection failed.";
}
