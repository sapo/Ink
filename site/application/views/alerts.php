<div class="whatIs" id="nav-home">
   <div class="ink-container">
		<h2>Alerts</h2>
		<p>Although they're a way of displaying text, alerts have their own specificity and functionality.</p>
	</div>
</div>

<div>
	<div class="ink-container">
		<nav class="ink-navigation ink-collapsible ink-dockable" data-fixed-height="44">
			<ul class="menu horizontal black ink-l100 ink-m100 ink-s100">
				<li class="active"><a class="scrollableLink home" href="#nav-home">
					<i class="icon-chevron-up ink-for-l"></i>
					<span class="ink-for-m ink-for-s">Back to Top</span>
				</a></li>
				<li><a class="scrollableLink" href="#basic">Basic Alerts</a></li>
				<li><a class="scrollableLink" href="#block">Block alerts</a></li>
			</ul>
		</nav>
	</div>
</div>

<div class="ink-container" id="basic">
	<div class="ink-section">
		<div class="ink-row ink-vspace">
			<div class="ink-l40">
				<div class="ink-gutter">
					<h3>Basic alerts</h3>
					<p>Basic alerts are useful for simple interaction with the user, such as showing a state on a web application or giving a warning on a form.</p>
					<p>To style some text as an alert, create a block element, such as a <code>div</code>, with the <code>ink-alert</code> class. This creates the outter shell of the alert, so to speak. To add some meaning, using color, add one of the following classes:</p>
					<ul class="unstyled">
						<li><code>error</code> for error messages</li>
						<li><code>success</code> for success messages</li>
						<li><code>info</code> for informative messages</li>
					</ul>
					<p>Add a <code>&lt;button&gt;</code> element with the class <code>close</code> and an appropriate icon (Ink uses the <code>&amp;times&#59;</code> character), to add a simple dismiss action to your alerts.</p>
				</div>
			</div> 
			<div class="ink-l60">
				<div class="ink-gutter">
					<div class="ink-alert">
						<button class="ink-close">&times;</button>
						<p><b>Warning:</b> There's a warning for you</p>
					</div>
	
					<div class="ink-alert error">
						<button class="ink-close">&times;</button>
						<p><b>Error:</b> The system has failed</p>
					</div>
	
					<div class="ink-alert success">
						<button class="ink-close">&times;</button>
						<p><b>Done:</b> Process completed successfully</p>
					</div>
	
					<div class="ink-alert info">
						<button class="ink-close">&times;</button>
						<p><b>Note:</b> You have 5 minutes to leave, before self-destruct</p>
					</div>
					<pre class="prettyprint linenums">
<?php echo(htmlentities('<div class="ink-alert info">
<button class="ink-close">&times;</button>
<p><b>Note:</b> You have 5 minutes to leave, before self-destruct</p>
</div>')) ?></pre>
				</div>
			</div>
		</div>
	</div>
	<div class="ink-section" id="block">
		<div class="ink-row ink-vspace">
			<div class="ink-l40">
				<div class="ink-gutter">
					<h3>Block Alerts</h3>
					<p>
						Block alerts are ideal for messages that require further explanation, since they're comprised of a title, close button and description text. 
						Implementation is similar to basic alerts, simply use the <code>ink-alert-block</code> class, 
						instead of <code>ink-alert</code> and use an <code>&lt;h4&gt;</code> for the title and a <code>&lt;p&gt;</code> or a list for the description.
					</p>
					<pre class="prettyprint linenums">
<?php echo(htmlentities('<div class="ink-alert-block success">
<button class="ink-close">&times;</button>
<h4>Thank you for buying!</h4>
<p>Your payment has been received and your plutonium will be shipped shortly. Check your e-mail for tracking information.</p>
</div>')) ?></pre>
				</div>
			</div>
			<div class="ink-l60">
				<div class="ink-gutter">
					<div class="ink-alert-block">
						<button class="ink-close">&times;</button>
						<h4>System maintenance scheduled</h4>
						<p>
							Please note that, due to a platypus invasion on our datacenter early last morning, the servers will have to undergo a maintenance cleaning today at 23:00 GMT.
						</p>
					</div>
	
					<div class="ink-alert-block error">
						<button class="ink-close">&times;</button>
						<h4>System is down</h4>
						<ul>
							<li>Do not panic</li>
							<li>Do not call our service line</li>
							<li>Breathe deep and wait an hour</li>
						</ul>
					</div>
	
					<div class="ink-alert-block success">
						<button class="ink-close">&times;</button>
						<h4>Thank you for buying!</h4>
						<p>Your payment has been received and your plutonium will be shipped shortly. Check your e-mail for tracking information.</p>
					</div>

				</div>
			</div>
		</div>	
	</div>

</div>
