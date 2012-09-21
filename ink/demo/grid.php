<?php include 'shared/header.php'; ?>
<!-- |||||||||||||||||||||||||||||||||  Navitation  |||||||||||||||||||||||||||||||||  -->
<nav id="topbar">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li><a class="logoPlaceholder" href="./" title="Site Title">InK</a></li>
			<li class="active"><a href="grid.php">Layout</a></li>
			<li><a href="navigation.php">Navigation</a></li>
			<li><a href="typo.php">Typography & Icons</a></li>
			<li><a href="forms.php">Forms</a></li>
			<li><a href="alerts.php">Alerts</a></li>
			<li><a href="tables.php">Tables</a></li>
			<li><a href="widgets.php">InkJS</a></li>
		</ul>
	</div>
</nav>  

<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Layout</h2>
		<p>Build a true fluid layout. Ink doesn't care how many colums your layout has, you just need to put your taste to good use.</p>
	</div>
</div>

<nav class="menu">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li class="active"><a class="home" href="#">Home</a></li>
			<li><a href="#">Containers</a></li>
			<li><a href="#">Spacer units</a></li>
			<li><a href="#">Spacing</a></li>
		</ul>
	</div>
</nav>  

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->		

<div class="ink-container">
	<div class="ink-space">
		<h2>Containers</h2>
		<p>The <mark>.ink-container</mark> class is where you define the width of your layout. You should define either a static width, a relative width or a maximum width for this element and use it to contain your layout.</p>
	</div>
	<div class="ink-section gridExemple">
		<div class="ink-space box">
			<div class="ink-l100 ink-m100"><p>100%</p></div>
			<div class="ink-l90 ink-m100 ink-clear"><p>90%</p></div>
			<div class="ink-l80 ink-m100 ink-clear"><p>80%</p></div>
			<div class="ink-l75 ink-m100 ink-clear"><p>75%</p></div>
			<div class="ink-l70 ink-m100 ink-clear"><p>70%</p></div>
			<div class="ink-l66 ink-m100 ink-clear"><p>66%</p></div>
			<div class="ink-l60 ink-m100 ink-clear"><p>60%</p></div>
			<div class="ink-l50 ink-m100 ink-clear"><p>50%</p></div>
			<div class="ink-l40 ink-m100 ink-clear"><p>40%</p></div>
			<div class="ink-l33 ink-m100 ink-clear"><p>33%</p></div>
			<div class="ink-l30 ink-m100 ink-clear"><p>30%</p></div>
			<div class="ink-l25 ink-m100 ink-clear"><p>25%</p></div>
			<div class="ink-l20 ink-m100 ink-clear"><p>20%</p></div>
			<div class="ink-l10 ink-m100 ink-clear"><p>10%</p></div>
		</div>

		<div class="ink-space">
			<h3>Markup</h3>
			<p>Let's say you need your page container to always be 80% of your view port.</p>
			<pre class="prettyprint"><ol><li><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-container"</span><span class="tag">&gt;</span></li></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l100"</span><span class="tag">&gt;</span></li><li><span class="pln">    </span><span class="tag">&lt;p&gt;</span><span class="pln">Content</span><span class="tag">&lt;/p&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;/div&gt;</span></li><li><span class="tag">&lt;/div&gt;</span></li></ol></pre>
			<p>In conf.css, define:</p>
			<pre class="prettyprint"><ol><li><span class="pun">.</span><span class="pln">ink-container </span><span class="pun">{</span></li><li><span class="pln">  </span><span class="kwd">width</span><span class="pun">:</span><span class="pln"> 80%</span><span class="pun">;</span></li><li><span class="pun">}</span></li></ol></pre>
		</div>
		</div>
		
		<div class="ink-section">
			
			<div class="ink-space">
				<h2>Division</h2>
				<p>Ink uses a percentage-based container logic which is flexible and promotes the use of fluid layouts.</p>
				<p>You can setup 10, 20, 25, 30, 33, 40, 50, 66, 70, 75, 80, 90 and 100% width units and combinations therein and think in a simple, percentage-oriented, manner, leaving the calculations for each browser box model up to Ink.</p>
			</div>
			
			<div class="ink-l50 gridExemple2">
				<div class="ink-hspace box">
					<div class="ink-l100 ink-m100 level1">
						<p>100%</p>
						<div class="ink-l50 ink-m100 level2"><p>50%</p></div>
						<div class="ink-l50 ink-m100 level2"><p>50%</p></div>
						<div class="ink-l25 ink-m100 level2"><p>25%</p></div>
						<div class="ink-l20 ink-m100 level2"><p>20%</p></div>
						<div class="ink-l10 ink-m100 level2"><p>10%</p></div>
						<div class="ink-l20 ink-m100 level2"><p>20%</p></div>
						<div class="ink-l25 ink-m100 level2"><p>25%</p></div>
					</div>
				</div>
				<pre class="prettyprint ink-space"><ol><li><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l100"</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l50"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l50"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l25"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l20"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l10"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l20"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l25"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="tag">&lt;/div&gt;</span></li></ol></pre>
			</div>
			<div class="ink-l50 gridExemple2">
				<div class="ink-hspace box">
					<div class="ink-l100 ink-m100 level1">
						<div class="ink-l50 ink-m100 level2">
							<div class="ink-l100 ink-m100 level2"><p>50%</p></div>
							<div class="ink-l50 ink-m50 level2"><p>50%</p></div>
							<div class="ink-l50 ink-m50 level2"><p>50%</p></div>
						</div>
						<div class="ink-l50 ink-m100 level2">
							<div class="ink-l100 ink-m100 level2"><p>50%</p></div>
							<div class="ink-l50 ink-m50 level2"><p>50%</p></div>
							<div class="ink-l50 ink-m50 level2"><p>50%</p></div>
						</div>
						<div class="ink-l25 ink-m50"><p>25%</p></div>
						<div class="ink-l25 ink-m50"><p>25%</p></div>
						<div class="ink-l25 ink-m50"><p>25%</p></div>
						<div class="ink-l25 ink-m50"><p>25%</p></div>
					</div>
				</div>
				<pre class="prettyprint ink-space"><ol><li><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l100"</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l50"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l50"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="tag">&lt;/div&gt;</span></li><li><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l50"</span><span class="tag">&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l50"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln">  </span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l50"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li class="tag">&lt;/div&gt;</li><li><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l25"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln"></span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l25"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln"></span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l25"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li><li><span class="pln"></span><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l25"</span><span class="tag">&gt;</span><span class="tag">&lt;/div&gt;</span></li></ol></pre>
			</div>		
			<div class="ink-space">
				<p>You should use the <strong>.ink-lxx, .ink-mxx and ink-sxx</strong> classes for layout only and add an additional semantic class for further costumization. In this case, you could then use your conf.css file to customize .maincontent, .sidebar, etc.</p>
			</div>
		</div>
	
	
		<div class="ink-section" id="spaceExemples">
		<div class="ink-space">
			<h2>Spacer units</h2>
			<p>Since Ink's approach to layout is not grid-based, but space division based, we needed to keep things simple spacing wise. 
			Despite meaning the need for extra markup elements, we feel the gained simplicity means you can build stuff faster and easier.</p>

			<p>Ink uses seven kinds of spacer unit: a vertical spacer, an horizontal spacer, and all-around spacer and one for each side of your box (top, right, bottom, left). 
			To use them, put a block-level element within your layout element with the corresponding spacer class.</p>
		</div>
		<div class="ink-l33">
			
			<div class="ink-hspace box">
				<div class="ink-vspace"><p>.ink-vspace</p></div>
			</div>
			<pre class="prettyprint ink-space"><ol><li class="com">//Defines width</li><li><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l33"</span><span class="tag">&gt;</span></li><li>  <span class="com">//Defines margins</span></li><li>  <span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-vspace"</span><span class="tag">&gt;</span></li><li>    <span class="com">...</span></li><li><span class="tag">  &lt;/div&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/div&gt;</span><span class="tag"></span></li></ol></pre>

		</div>
		<div class="ink-l33">
			
			<div class="ink-hspace box">
				<div class="ink-hspace"><p>.ink-hspace</p></div>
			</div>
			<pre class="prettyprint ink-space"><ol><li><span class="com">//Defines width</span></li><li><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l33"</span><span class="tag">&gt;</span></li><li>  <span class="com">//Defines margins</span></li><li>  <span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-hspace"</span><span class="tag">&gt;</span></li><li>  <span class="com">  ...</span></li><li><span class="tag">  &lt;/div&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/div&gt;</span><span class="tag"></span></li></ol></pre>

		</div>
		<div class="ink-l33">
			
			<div class="ink-hspace box">
				<div class="ink-space"><p>.ink-space</p></div>
			</div>
			<pre class="prettyprint ink-space"><ol><li><span class="com">//Defines width</span></li><li><span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-l33"</span><span class="tag">&gt;</span></li><li>  <span class="com">//Defines margins</span></li><li>  <span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-space"</span><span class="tag">&gt;</span></li><li>    ...</li><li><span class="tag">  &lt;/div&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/div&gt;</span><span class="tag"></span></li></ol></pre>

		</div>
	</div>
	
	<div class="ink-space">
		<h2>Available Classes</h2>
		<p>
		.ink-container <br>
		.ink-section<br>
		.ink-space<br>
		.ink-vspace<br>
		.ink-hspace<br>
		</p>
	</div>
	
</div>

<?php include 'shared/footer.php'; ?>	