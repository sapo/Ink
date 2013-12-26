<!doctype html>
<html>
<head>
</head>
<body>
    <script type="text/javascript" src="../../../src/js/Ink/1/lib.js"></script>
    <script type="text/javascript" src="../../../src/js/Ink/Dom/Loaded/1/lib.js"></script>
    <?php
        usleep((float)$_GET["w"] * 1000000);
    ?>
    <p>
        Iframe waited <?php echo $_GET["w"]; ?>s and loaded,
    </p>
</body>
</html>
