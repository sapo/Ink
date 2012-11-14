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
					The <i>Table</i> component ease the process of improving a regular table element with some features like:
                    <ul>
                        <li>Pagination</li>
                        <li>Sorting</li>
                        <li>Loading through AJAX (via an endpoint)</li>
                    </ul>
				</p>
                <p>
                    More examples and a list of all options supported on our <a target="_blank" href="http://js.sapo.pt/SAPO/Ink/Table/doc.html">technical documentation</a>.
                </p>
			</div>
		</div>
		<div class="ink-l60">
			<div class="ink-gutter">
				<div class="box">
					<?php echo $js;?>
				</div>
				<a href="#" data-target="table_sourcecode" class="ink-button toggleTrigger">View Source Code</a>
				<pre id="table_sourcecode" style="display:none" class="prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>
			</div>
		</div>
	</div>
</div>