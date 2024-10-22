<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Check</title>
</head>
<body>
<?php
echo "Health Check ~ : OK ";
echo date("D M d H:i:s T Y");
echo " [",$_SERVER['SERVER_ADDR'],"]";
?>
</body>
</html>