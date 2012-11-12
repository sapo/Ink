<?php
$js = <<<JS
<table></table>
<script type="text/javascript">
    var t = new SAPO.Ink.Table('table', {
        fields: ['name', 'age'],
        sortableFields: '*',
        model: [
            {name:'Jesus Christ',    age:33},
            {name:'Kurt Cobain',     age:27},
            {name:'Joni Mitchel',    age:27},
            {name:'Michael Jackson', age:51}
        ],
        pageSize: 2
    });
</script>
JS;
?>
    <div class="ink-section">
        <div class="ink-row ink-vspace">
            <div class="ink-l40">
                <div class="ink-gutter"> 
                    <h3 id="table">Table</h3>
                    <p>
                        The <i>Table</i> component provides an easy way to list data in a tabular format.
                        It supports sorting, pagination and getting data through <a href="#" class="modal">AJAX</a>.
                    </p>
                </div>
            </div>
            <div class="ink-l60">
                <div class="ink-gutter">
                    <div class="ink-row box">
                        <?php echo $js;?>
                        <br/>
                        <a href="#" data-target="table_sourcecode" class="toggleTrigger">View Source Code</a>
                        <pre id="table_sourcecode" style="display:none" class="prettyprint linenums ink-vspace"><?php echo(htmlentities( $js )); ?></pre>
                    </div>
                </div>
            </div>
		</div>
    </div>