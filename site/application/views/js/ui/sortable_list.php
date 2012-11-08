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
					<div id="slist"></div>
				</div>
            </div>
			<script type="text/javascript">
				var list = new SAPO.Ink.SortableList('#slist', {
					model: ['primeiro', 'segundo', 'terceiro']
				});
			</script>
        </div>
    </div>