<!doctype html>
<html>
<head>
</head>
<body>
    <script type="text/javascript" src="/Ink/1/"></script>
    <script type="text/javascript" src="/Ink/Dom/Loaded/1/"></script>
    <?php
        usleep((float)$_GET["w"] * 1000000);
    ?>
    <p>
        Iframe waited <?php echo $_GET["w"]; ?>s and loaded,
    </p>
</body>
</html>
