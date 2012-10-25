<?php include 'shared/header.php'; ?>

<div class="ink-container">
    <div class="ink-l60">

        <h1>Pagination Demo</h1>



        <h2>simplest example</h2>
        
        <nav class="p1 ink-navigation"></nav>



        <h2></h2>
        <h2></h2>
        <h2>subscribing onChange()</h2>

        <nav class="p2 ink-navigation"></nav>



        <h2></h2>
        <h2></h2>
        <h2>with hashchange</h2>
        
        <nav class="p3 ink-navigation"></nav>

    </div>
</div>

<script type="text/javascript">
    var pag1 = new SAPO.Ink.Pagination(
        '.p1',
        {
            size:     6
        }
    );

    var pag2 = new SAPO.Ink.Pagination(
        '.p2',
        {
            size:     8,
            onChange: function(pag) {
                console.log( 'Current: ' + pag.getCurrent() );
            }
        }
    );

    var pag3 = new SAPO.Ink.Pagination(
        '.p3',
        {
            size:    5,
            setHash: true
        }
    );
</script>
