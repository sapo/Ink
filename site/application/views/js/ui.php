<div class="whatIs">
   <div class="ink-container" id="ui_home">
		<h2>Ink JS UI</h2>
		<p>Beautiful js components to go with your project.</p>
	</div>
</div>

<div>
	<div class="ink-container">
		<nav class="ink-navigation ink-collapsible ink-dockable" data-fixed-height="44">
			<ul class="menu horizontal black ink-l100 ink-m100 ink-s100">
				<li class="active"><a class="scrollableLink home" href="#ui_home">
					<i class="icon-chevron-up ink-for-l"></i>
					<span class="ink-for-m ink-for-s">Back to Top</span>
				</a></li>
				<?php foreach( $components as $component => $configuration ){ ?>
					<li><a class="scrollableLink" href="#<?php echo $component;?>"><?php echo $configuration['label'];?></a></li>
				<?php } ?>
			</ul>
		</nav>
	</div>
</div>

<div class="ink-container">
	<div class="ink-section">
		<p>To use these components in your application you need to include the Ink.js bundle. Just add this line somewhere in your document:</p>
<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/Bundles/Ink-v1.js"&gt;&lt;/script&gt;</pre>
	</div>
	<?php
		foreach( $components as $component => $configuration ){
			echo $configuration['view'];
		}
	?>
</div>
<style>
	/*.ink-docked {
		position:           fixed !important;
		opacity:            0.75;
		z-index:            1000;
	}
	.ink-docked:hover {
		opacity:            1;
    }*/
</style>
<script>

	var toggleTriggers = SAPO.Dom.Selector.select('.toggleTrigger');
	for(i=0;i<toggleTriggers.length;i+=1){
		SAPO.Dom.Event.observe(toggleTriggers[i],'click',function(event){
			var targetElm = s$(this.getAttribute('data-target'));
			this.innerHTML = ( ( targetElm.style.display === 'none' ) ? 'Hide' : 'View' ) + ' Source Code';
			SAPO.Dom.Css.toggle(targetElm);
			SAPO.Dom.Event.stop(event);
		});
	}
</script>