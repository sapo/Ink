<nav id="topbar">
	<div class="ink-container">
		<h1><a class="logoPlaceholder" href="<?php echo base_url() ?>" title="Site Title">InK<small>Interface kit</small></a></h1>
		<ul>
			<?php foreach($pages as $page): ?>
			<?php $menu_item_class = ($page['url'] == uri_string()) ? 'class="active"' : ''; ?>
			<li <?php echo $menu_item_class ?>><a href="<?php echo site_url() . '/' . $page['url'] ?>"><?php echo $page['text'] ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>