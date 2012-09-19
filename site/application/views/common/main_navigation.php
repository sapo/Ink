<nav id="topbar">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li><a class="logoPlaceholder" href="./" title="Site Title">InK</a></li>
			<?php foreach($pages as $page): ?>
			<?php $menu_item_class = ($page['url'] == uri_string()) ? 'class="active"' : ''; ?>
			<li <?php echo $menu_item_class ?>><a href="<?php echo site_url() . '/' . $page['url'] ?>"><?php echo $page['text'] ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>