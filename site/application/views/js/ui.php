<div class="whatIs">
   <div class="ink-container" id="ui_home">
		<h2>Ink-js</h2>
		<p>Beautiful js components to go with your project.</p>
	</div>
</div>

<div class="menu-second-levelx">
	<div class="ink-container">
		<nav class="ink-navigation" id="dockedMenu">
			<ul class="menu horizontal blue ink-l100 ink-m100 ink-s100">
				<li class="active"><a class="scrollableLink home" href="#ui_home">Home</a></li>
				<?php foreach( $components as $component => $configuration ){ ?>
					<li><a class="scrollableLink" href="#<?php echo $component;?>"><?php echo $configuration['label'];?></a></li>
				<?php } ?>
			</ul>
		</nav>
	</div>
</div>

<div class="ink-container">
	<?php
		foreach( $components as $component => $configuration ){
			echo $configuration['view'];
		}
	?>
</div>
<style>
/* DOCKED TENTATIVE PROPOSAL */
	#dockedMenu {
		/*position:           absolute;*/
	}

	.ink-docked {
		position:           fixed !important;
		opacity:            0.75;
		z-index:            1000;
	}

	.ink-docked:hover {
		opacity:            1;
            }
</style>
<script>
	new SAPO.Ink.Docked('#dockedMenu', {
		fixedHeight: 50
	});
	// horizontal menu
	new SAPO.Ink.HorizontalMenu('#topbar > nav');
	new SAPO.Ink.HorizontalMenu('#dockedMenu');
	
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