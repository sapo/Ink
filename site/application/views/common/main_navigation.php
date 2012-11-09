<div id="topbar">
	<nav class="ink-navigation ink-container">
		<ul class="menu horizontal flat black">
			<li><a class="logoPlaceholder" href="<?php echo site_url() ?>" title="Site Title">InK</a></li>
			
			<?php foreach($pages as $page): ?>
				<?php if(isset($page['submenu'])): ?>
				
				<?php 
				if( !(($menu_item_class = ($page['url'] == uri_string()) ? 'class="active"' : '')) ){
					foreach( $page['submenu'] as $submenu )
						if( (($menu_item_class = ($submenu['url'] == uri_string()) ? 'class="active"' : '')) )
							break;
				}
				?>
				
				<li <?php echo $menu_item_class ?>>
					<a href="#"><?php echo $page['text'] ?><i class="icon-caret-down"></i></a>
					<ul class="submenu">
						<?php foreach($page['submenu'] as $subpage => $subpage_data): ?>
							<li><a href="<?php echo site_url() . '/' . $subpage_data['url'] ?>"><?php echo $subpage_data['text'] ?></a></li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php else: ?>
			<?php $menu_item_class = ($page['url'] == uri_string()) ? 'class="active"' : ''; ?>
			<li <?php echo $menu_item_class ?>><a href="<?php echo site_url() . '/' . $page['url'] ?>"><?php echo $page['text'] ?></a></li>
			<?php endif;?>
			<?php endforeach; ?>
		</ul>
	</nav>
	<div class="border"></div>
</div>