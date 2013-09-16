<script src="/Ink/1/"></script>
<script src="/Ink/Dom/Loaded/1/"></script>
<script type="text/javascript">
    Ink.Dom.Loaded.run(function () {
        var elem = document.createElement('p');
        if (document.getElementsByTagName('p')) {
            elem.innerHTML = 'Found paragraph on the bottom of the page!';
        } else {
            elem.innerHTML = 'Failed! Could not find paragraph on the bottom of the page!';
            elem.style.color = 'red';
        }
        (document.body || document.getElementsByTagName('body')).appendChild(elem);
    });
</script>


<?php sleep(3); ?>

<p>Paragraph on the bottom of the page!</p>
