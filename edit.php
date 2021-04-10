<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel News</title>
</head>
<body>
    <nav>
        <a href="home.html">Laravel News</a>
    </nav>
    <?php
    $txt = fgets(fopen($toukou, "r"));
    echo $txt."<br>";
    ?><br>
    <hr>
    
</body>
</html>