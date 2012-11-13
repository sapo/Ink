<?php
 $js = <<<JS
<ul id="slist">
    <li>primeiro</li>
    <li>segundo</li>
    <li>terceiro</li>
</ul>
<script type="text/javascript">
    var list = new SAPO.Ink.SortableList('#slist');
</script>
JS;
?>
    <div class="ink-section">
        <div class="ink-row ink-vspace">
        	<div class="ink-l40">
                <div class="ink-gutter"> 
                    <h3 id="sortable_list">Sortable List</h3>
                    <p>
                        The <i>Sortable List</i> component transforms the rows of a list in draggable/droppable items inside of the list.
                        By doing that, allows the user to sort the order of the list. Also allows other configurations, as you can see in these <a href="#" class="modal">examples</a>.
                    </p>
                </div>
            </div>
            <div class="ink-l60">
				<div class="ink-gutter">
                    <div class="box">
    					<?php echo $js;?>
                    </div>
					<a href="#" data-target="sortablelist_sourcecode" class="ink-button toggleTrigger">View Source Code</a>
    	            <pre id="sortablelist_sourcecode" style="display:none" class="prettyprint linenums"><?php echo(htmlentities( $js )); ?></pre>
				</div>
            </div>
        </div>
    </div>