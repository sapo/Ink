<?php
 $js = <<<JS
<ul id="tview">
    <li>root
        <ul>
            <li>child 1
                <ul>
                    <li>c</li>
                    <li>grandchild 1b</li>
                    <li>grandchild 1c</li>
                </ul>
            </li>
            <li>child 2
                <ul>
                    <li>grandchild 2a</li>
                    <li>grandchild 2b
                        <ul>
                            <li>grandgrandchild 1bA</b>
                            <li>grandgrandchild 1bB</b>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>child 3</li>
        </ul>
    </li>
</ul>
<script type="text/javascript">
    var tv1 = new SAPO.Ink.TreeView('#tview');
</script>
JS;
?>
<div class="ink-section">
	<div class="ink-row ink-vspace">
		<div class="ink-l40">
			<div class="ink-gutter"> 
				<h3 id="tree_view">Tree View</h3>
				<p>
					The <i>Tree View</i> component allows you to create a hierarchical list in a tree format or transform an existing list.
                    It supports:
                    <ul>
                        <li>Multi-node trees</li>
                        <li>Custom state of the branches (open, closed)</li>
                    </ul>
                </p>
                <br/>
                <p>
					Specifications and other examples, available in the <a target="_blank" href="http://js.staging.sapo.pt/SAPO/Ink/TreeView/doc.html">technical documentation</a>.
				</p>
			</div>
		</div>
		<div class="ink-l60">
			<div class="ink-gutter">
				<div class="box">
					<?php echo $js;?>
				</div>
				<a href="#" data-target="treeview_sourcecode" class="toggleTrigger ink-button">View Source Code</a>
				<pre id="treeview_sourcecode" style="display:none" class="prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>
			</div>
		</div>
	</div>
</div>