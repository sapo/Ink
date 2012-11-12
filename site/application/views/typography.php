<div class="whatIs">
   <div class="ink-container">
		<h2>Typography</h2>
		<p>Text is the most fundamental content type.</p>
	</div>
</div>

<div class="menu-second-level">
	<div class="ink-container">
		<nav class="ink-navigation">
			<ul class="menu horizontal">
				<li class="active"><a  class="scrollableLink home" href="#">Home</a></li>
				<li><a class="scrollableLink" href="#fonts">Fonts</a></li>
				<li><a class="scrollableLink" href="#headings">Headings</a></li>
				<li><a class="scrollableLink" href="#body">Body & Hypertext</a></li>
				<li><a class="scrollableLink" href="#lists">Lists</a></li>
				<li><a class="scrollableLink" href="#utilities">Utility Classes</a></li>
				<li><a class="scrollableLink" href="#additional">Additional Elements</a></li>
			</ul>
		</nav>
	</div>
</div>
<!--menu_end-->
<div class="ink-container">
	<!--fonts_start-->
	<a name="fonts"></a>
	<div class="ink-section">
		<h2>Fonts</h2>
		<p>Ink offers you a simple and elegant base style for text with a clear hierarchy and flexible, generic styling for all types of typographical composition you may need.</p>
		<p>Ink's typography is designed to create a strong hierarchy with basic styles. We distribute the open source Ubuntu font family for a modern fresh look, but you can opt for non-webfont solutions and choose from three basic font stacks.</p>
		<div class="ink-row ink-vspace">
			<div class="ink-l60">
				<div class="ink-gutter">
					<h3>Ubuntu</h3>
					<p>The default font stack starts with the Ubuntu webfont. Make sure you can serve up the font files from your server, they are free to use and redistribute.</p>
					<h3>Sans serif</h3>
					<p>If you prefer a system font, sans serif, option, change the font-family option bla bla</p>
					<h3>Serif font</h3>
					<p>For a serif option, we went with Georgia, which is readily availabe and quite versatile. Configure bla bla</p>
				</div>
			</div>
			<div class="ink-l40">
				<div class="ink-gutter">
					<h3>Examples</h3>
					<h5>Ubuntu font stack</h5>
					<ul>
						<li>Ubuntu</li>
						<li>Arial</li>
						<li>Helvetica</li>
						<li>Sans-serif</li>
					</ul>
					<h5>Sans serif font stack</h5>
					<ul>
						<li>Helvetica</li>
						<li>Arial</li>
						<li>Sans-serif</li>
					</ul>
					<h5>Serif font stack</h5>
					<ul>
						<li>Georgia</li>
						<li>Times</li>
						<li>Serif</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<!--fonts_end-->
	<!--headings_start-->
	<a name="headings"></a>
	<div class="ink-section">
		<div class="ink-row">
			<div class="ink-l60">
				<div class="ink-gutter">
					<h2>Headings</h2>
					<p>Headings are essential for defining your blocks of text. Use your h1 for a site-wide identifier, such as the logo, use the h2 as page title, and try to keep it to a single h2 per page, and then build your hierarchy of blocks using h3-h6.</p>
					<p>Ink defines default font-size, line-height and margins for each heading level to cleanly match the body text and maximize readability.</p>
				</div>
			</div>
			<div class="ink-l40">
				<div class="ink-gutter">
					<h1>&lt;h1&gt;Title 1&lt;/h1&gt;</h1>
					<h2>&lt;h2&gt;Title 2&lt;/h2&gt;</h2>
					<h3>&lt;h3&gt;Title 3&lt;/h3&gt;</h3>
					<h4>&lt;h4&gt;Title 4&lt;/h4&gt;</h4>
					<h5>&lt;h5&gt;Title 5&lt;/h5&gt;</h5>
					<h6>&lt;h6&gt;Title 6&lt;/h6&gt;</h6>
				</div>
			</div>
		</div>
	</div>
	<!--headings_end-->
	<!--body&hypertext_start-->
	<a name="body"></a>
	<div class="ink-section">
		<h2>Body & Hypertext</h2>
		<p>Body text is the most basic unit of text you can define. Always use <code>&lt;p&gt;</code> for your paragraphs and Ink takes care of font-size, line-height and proper margins.</p>
		<div class="ink-row ink-vspace">
			<div class="ink-l75 ink-m75 ink-s100">
				<div class="ink-gutter">
					<h4>Emphasis</h4>
					<p>For emphasis use either <code>&lt;em&gt;</code> or <code>&lt;i&gt;</code> to obtain <em>italicized</em> type and <code>&lt;strong&gt;</code> or <code>&lt;b&gt;</code> to obtain <strong>bold</strong> type. If you need further emphasis, the <code>&lt;mark&gt;</code> HTML5 tag renders a simple text highlight.</p>
					<h4>Hypertext</h4>
					<p>Hypertext is the fundament of the web. Ink renders all four states of link text as can be seen on the example on the right. Simply use the anchor, <code>&lt;a&gt;</code> tag. You can customize link appearance in the <a href="conf">Ink configurator</a>, using your own custom CSS or, if you're a more advanced user, editting the conf.less file.</p>
					<h4>Notes</h4>
					<p>If you need to add side notes to your text, apply the utility class <code>.note</code> to your paragraphs to get smaller, faded text.</p>
				</div>
			</div>
			<div class="ink-l25 ink-m25 ink-s100">
				<div class="ink-gutter">
				<h4>Emphatic elements</h4>
				<ul class="unstyled">
					<li><em>Italicized</em></li>
					<li><strong>Bold</strong></li>
					<li><mark>Highlighted</mark></li>
				</ul>
				<h4>Hyperlinks</h4>
					<ul class="unstyled">
						<li><a href="#">Normal</a></li>
						<li><a href="#" class="visited">Visited</a></li>
						<li><a href="#" class="active">Active</a></li>
						<li><a href="#" class="hover">Hover</a></li>
					</ul>
				<h4>Note text</h4>
				<p>This is normal body text.</p>
				<p class="note">This is a note.</p>
				</div>
			</div>
		</div>
	</div>
	<!--body&hypertext_end-->
	<!--lists_start-->
	<a name="lists"></a>
	<div class="ink-section">
		<h2>Lists</h2>
		<p>There are four basic list styles defined in Ink, which correspond to the most widely used HTML list formats and also include an unstyled list format.</p>

		<div class="ink-row ink-vspace">
			<div class="ink-l50 ink-m50 ink-s100">
				<div class="ink-gutter">
					<h4>Unordered List</h4>
					<pre class="prettyprint linenums">
<?php echo(htmlentities('<ul>
   <li>Two fresh avocados</li>
   <li>Two ripe limes</li>
   <li>An onion</li>
   <li>Parsley</li>
</ul>')) ?></pre>
					<ul>
						<li>Two fresh avocados</li>
						<li>Two ripe limes</li>
						<li>An onion</li>
						<li>Parsley</li>
					</ul>
				</div>
			</div>

			<div class="ink-l50 ink-m50 ink-s100">
				<div class="ink-gutter">
					<h4>Ordered list</h4>
					<pre class="prettyprint linenums">
<?php echo(htmlentities('<ol>
   <li>Grill the chicken</li>
   <li>Pour into tortilla</li>
   <li>Add guacamole, sour cream and cheese</li>
   <li>Roll tortilla and serve</li>
</ol>')) ?></pre>
					<ol>
						<li>Grill the chicken</li>
						<li>Pour into tortilla</li>
						<li>Add guacamole, sour cream and cheese</li>
						<li>Roll tortilla and serve</li>
					</ol>
				</div>
			</div>
		</div>
		<div class="ink-row ink-vspace">
			<div class="ink-l50 ink-m50 ink-s100">
				<div class="ink-gutter">
					<h4>Definition list</h4>
					<pre class="prettyprint linenums">
<?php echo(htmlentities('<dl>
   <dt>Avocado</dt>
   <dd>Pear-shaped fruit native to Central Mexico.</dd>
   <dt>Guacamole</dt>
   <dd>An avocado-based sauce that originated with the Aztecs in Mexico.</dd>
</dl>')) ?></pre>
					<dl>
						<dt>Avocado</dt>
						<dd>Pear-shaped fruit native to Central Mexico.</dd>
						<dt>Guacamole</dt>
						<dd>An avocado-based sauce that originated with the Aztecs in Mexico.</dd>
					</dl>
				</div>
			</div>

			<div class="ink-l50 ink-m50 ink-s100">
				<div class="ink-gutter">
					<h4>Unstyled list</h4>
					<pre class="prettyprint linenums">
<?php echo(htmlentities('<ul class="unstyled">
   <li>Carnitas</li>
   <li>Jalapeno peppers</li>
   <li>Sour cream</li>
   <li>Pico de gallo</li>
</ul>')) ?></pre>
					<ul class="unstyled">
						<li>Carnitas</li>
						<li>Jalapeño peppers</li>
						<li>Sour cream</li>
						<li>Pico de gallo</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<!--lists_end-->
	<!--utilities_start-->
	<a name="utilities"></a>
	<div class="ink-section">
		<h2>Utility Classes</h2>
		<p>You can use the InK utility classes <code>.info</code>, <code>.warning</code>, <code>.caution</code> and <code>.success</code> on html elements like headings, paragraphs, lists, mark, code, to quickly style those elements using a basic color code that can greatly aid in adding meaning to your content.</p>
		<p>Use the classes to obtain a color background with white text or use the extra <code>.invert</code> class to write colored text on transparent background. You can tweak the colors used by each class in the <a href="conf">Ink Configurator</a>, your own CSS or the conf.less file.</p>
		<div class="ink-vspace">
		<div class="ink-row">
			<div class="ink-l25 ink-m50">
				<div class="ink-hspace">
					<h4 class="info">.info</h4>
				</div>
			</div>
			<div class="ink-l25 ink-m50">
				<div class="ink-hspace">
					<h4 class="warning">.warning</h4>
				</div>
			</div>
			<div class="ink-l25 ink-m50">
				<div class="ink-hspace">
					<h4 class="caution">.caution</h4>
				</div>
			</div>
			<div class="ink-l25 ink-m50">
				<div class="ink-hspace">
					<h4 class="success">.success</h4>
				</div>
			</div>
		</div>
		<div class="ink-row">
			<div class="ink-l25 ink-m50">
				<div class="ink-hspace">
					<h4 class="info invert">.info.invert</h4>
				</div>
			</div>
			<div class="ink-l25 ink-m50">
				<div class="ink-hspace">
					<h4 class="warning invert">.warning.invert</h4>
				</div>
			</div>
			<div class="ink-l25 ink-m50">
				<div class="ink-hspace">
					<h4 class="caution invert">.caution.invert</h4>
				</div>
			</div>
			<div class="ink-l25 ink-m50">
				<div class="ink-hspace">
					<h4 class="success invert">.success.invert</h4>
				</div>
			</div>
		</div>
		</div>
		<div class="ink-row">
			<div class="ink-l75 ink-m75 ink-s100">
				<div class="ink-gutter">
					<h4>Labels</h4>
					<p>You can easily use these utility classes to label sections of highlighted text. Simply create an inline level element with the class <code>.ink-label</code> and then add one of the utility classes to color code your inline label. This way, you can easily add a success message post a warning emphasize a particularly relevant information or display an error message.</p>
					<p>We suggest you do not use punctuation before or after labels, but instead, precede and succeed with spaces.</p>
				</div>
			</div>
			<div class="ink-l25 ink-m25 ink-s100">
				<div class="ink-gutter">
					<h4>Example</h4>
					<ul class="unstyled">
						<li><span class="ink-label info">info</span></li>
						<li><span class="ink-label warning">warning</span></li>
						<li><span class="ink-label caution">caution</span></li>
						<li><span class="ink-label success">success</span></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="ink-row ink-vspace">
			<div class="ink-l50 ink-m50 ink-s100">
				<div class="ink-gutter">
					<h4>Code example</h4>
					<pre class="prettyprint linenums">
<?php echo(htmlentities('<h6 class="warning">You are approaching your space quota</h6>
<p>Your account offers 20 GB of storage space and you are now using <span class="ink-label info">19.8 GB</span>. Please remove some files or upgrade your account.</p>
<p class="caution invert">Please note, files over quota will not be stored.</p>')) ?></pre>
				</div>
			</div>
			<div class="ink-l50 ink-m50 ink-s100">
				<div class="ink-gutter">
					<h4>Result</h4>
					<h6 class="warning">You are approaching your space quota</h6>
					<p>Your account offers 20 GB of storage space and you are now using <span class="ink-label info">19.8 GB</span>. Please remove some files or upgrade your account.</p>
					<p class="caution invert">Please note, files over quota will not be stored.</p>
				</div>
			</div>
		</div>
	</div>
	<!--utilities_end-->
	<!--additionalelements_start-->
	
	<div class="ink-section">
		<a name="additional"></a>
		<h2>Additional elements</h2>
		<p>These elements allow you to compose specific types of text, such as addresses or quotes. Use the example code for quick recipes that work.</p>
			<div class="ink-row ink-vspace">
				<div class="ink-l33">
					<div class="ink-gutter">
						<h4>Abbreviations</h4>
						<p>For abbreviations, simply use the HTML <code>abbr</code> tag, like so:</p>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<p>Ink was entirely developed with text editors, no <abbr title="What You See Is What You Get">WYSIWYG</abbr> software was harmed.</p>')) ?></pre>
						<p>Ink was entirely developed with text editors, no <abbr title="What You See Is What You Get">WYSIWYG</abbr> software was harmed.</p>
					</div>
				</div>
				<div class="ink-l33">
					<div class="ink-gutter">
						<h4>Block quotes</h4>
						<p>For block quotes, use the <code>blockquote</code> element, with an optional <code>cite</code> element to contain the source name, like so:</p>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<blockquote>
   <p>You can\'t always get what you want.</p>
   <p><cite>Jagger & Richards</cite>, 1969</p>
</blockquote>')) ?></pre>
						<blockquote>
							<p>You can't always get what you want.</p>
							<p><cite>Jagger & Richards</cite>, 1969</p>
						</blockquote>
					</div>
				</div>
				<div class="ink-l33">
					<div class="ink-gutter">
						<h4>Addresses</h4>
						<p>You can use the <code>&lt;address&gt;</code> element to contain address elements, such as a name, street, postal code. Use headings, paragraphs and line-breaks to compose your address.</p>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<address>
   <h6>SAPO</h6>
   <p>Av. Fontes Pereira de Melo, 40<br>1050 Lisboa</p>
</address>')) ?></pre>
						<address>
							<h6>SAPO</h6>
							<p>Av. Fontes Pereira de Melo, 40<br>1050 Lisboa</p>
						</address>
					</div>
				</div>
			</div>
		<!--additionalelements_end-->
	</div>
</div>