<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Upload File for Printing</h2>

    <form action="upload_handler.php" method="POST" enctype="multipart/form-data">

        <label for="printFile">Select File:</label>
        <input type="file" name="printFile" id="printFile" required>
        <br><br>

        <label for="pageCount">Number of Pages:</label>
        <input type="number" name="pageCount" id="pageCount" min="1" required>
        <br><br>

        <button type="submit" name="uploadSubmit">Submit for Print</button>

    </form>
</body>
</html>