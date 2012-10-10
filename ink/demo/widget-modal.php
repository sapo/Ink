<?php include 'shared/header.php'; ?>

<style type="text/css">
    .ink-modal-source {
        display: none;
    }

    .ink-shade {
        background-color: rgba(0, 0, 0, 0.25);
    }
</style>

<button onclick="location='widget-modal.php?demo=0'">default</button>
<button onclick="location='widget-modal.php?demo=1'">custom dims</button>
<button onclick="location='widget-modal.php?demo=2'">skip dismiss</button>
<button onclick="location='widget-modal.php?demo=3'">custom markup</button>

<div class="ink-l70">   
    <div class="ink-space">

        <div class="ink-modal-source" id="src1">
            <h1>this is the content of the modal</h1>
            <p>oh yeah baby</p>
        </div>

    </div>
</div>


<script type="text/javascript">
    var i = parseInt( location.search.substring( location.search.indexOf('demo=') + 5), 10);
    console.log(i);
    var mod;
    if (i === 0) {
        mod = new SAPO.Ink.Modal('.ink-modal-source', {});
    }
    else if (i === 1) {
        mod = new SAPO.Ink.Modal('.ink-modal-source', {
            width:  300,
            height: 300
        });
    }
    else if (i === 2) {
        mod = new SAPO.Ink.Modal('.ink-modal-source', {
            skipDismiss: true
        });
    }
    else if (i === 3) {
        mod = new SAPO.Ink.Modal('', {
            markup: '<h1>ASD ASD</h1>'
        });
    }
</script>
