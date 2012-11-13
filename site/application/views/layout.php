<div class="whatIs">
   <div class="ink-container">
      <h2>Layout</h2>
      <p>Ink's layout classes are truly fluid and extremely easy to implement.</p>
   </div>
</div>

<div class="menu-second-level">
	<div class="ink-container">
		<nav class="ink-navigation">
			<ul class="menu horizontal">
				<li class="active"><a class="scrollableLink home" href="#">Home</a></li>
                <li><a class="scrollableLink" href="#multiple-layouts">Multiple layouts</a></li>
				<li><a class="scrollableLink" href="#containers">Containers</a></li>
				<li><a class="scrollableLink" href="#markup">Markup</a></li>
				<li><a class="scrollableLink" href="#columns">Columns</a></li>
            <li><a class="scrollableLink" href="#gutters">Gutters</a></li>l
            <li><a class="scrollableLink" href="#hidenseek">Showing &amp; Hiding</a></li>l
            <li><a class="scrollableLink" href="#spacing">Spacing</a></li>
			</ul>
		</nav>
	</div>
</div>  

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->     

<div class="ink-container">
    <div class="ink-section" id="multiple-layouts">
        <div class="ink-vspace">
        <h2>Multiple layouts</h2>
        <p>We believe that no website/webapp is created equal&mdash;specially in this day and age. That's why we want you to be able to easily control how your layout behaves on different screen sizes.</p>

        <p>You can use special class names specify how wide your columns should be (see <a href="#columns">section columns</a> for details). But what happens when you need a layout switch and you need a <strong>breakpoint</strong>? We don't want you to be forced to fallback to a one-size-fits-all kind of solution.</p>
        <p>With Ink, you are given three layouts you can use to your hearts content.</p>
        <ul>
            <li><strong>S</strong> which stands for <strong>small</strong></li>
            <li><strong>M</strong> which stands for <strong>medium</strong></li>
            <li><strong>L</strong> which stands for <strong>large</strong></li>
        </ul>

        <p>By default these correspond to the following screen size intervals (we'll show you how you can customize these in just a second):</p>

        <ul>
            <li>Small: below 650 pixels wide</li>
            <li>Medium: between 651 and 960 pixels wide</li>
            <li>Large: above 961 pixels wide</li>
        </ul>

        <p>These thresholds are specified as regular media-queries inside ink.css</code> and can be customized.</p>

        <pre class="prettyprint linenums">
<?php echo(htmlentities('/* INK: LARGE SCREENS */
@media screen and (min-width: 961px) {
(...)

/* INK: MEDIUM SCREENS */
@media screen and (min-width: 651px) and (max-width: 960px) {
(...)

/* INK: SMALL SCREENS */
@media screen and (max-width: 650px) {
(...)')) ?>
        </pre>
        <p class="ink-alert"><strong>Warning</strong> customization comes at a price: when we update Ink in the future you'll have to repeat any customization made on the file we provide. We usualy advise you to keep your changes in a separate file, 
          but in this case, you can't do that.</p>

        <h3>Customizing breakpoints</h3>
        <p>If you do decide to customize the breakpoints provided, you can do this by editing the <code>ink.css</code> by hand and changing the values on those media-queries. (A regular search in the file for " SCREENS" will get you there).</p>
        <p><strong>Coming soon</strong>, we'll add breakpoint customization to your fantastic <a href="<?php echo site_url() . '/download' ?>">Ink Customizer</a>.</p>

        <h4>Example: Using only two layouts (Large and Small)</h4>

        <pre class="prettyprint linenums">
<?php echo(htmlentities('/* INK: LARGE SCREENS */
@media screen and (min-width: 767px) {
(...)

/* INK: MEDIUM SCREENS - DISABLED */
@media screen and (min-width: 0px) and (max-width: 0px) { 
(...)

/* INK: SMALL SCREENS */
@media screen and (max-width: 766px) {
(...)')) ?>
        </pre>
        <p class="ink-alert info"><strong>Note</strong> you can just customize the values. In this example we show how robust and extensible this approach allows you to be. Just don't remove the entire media-query because you may change your mind later in the project. ;)</p>


        <h3>Even simpler...</h3>
        <p>However useful it is to have control, if you want to simplify your markup and use the exact same layout in all sizes you can easily achieve this by simply not specifying a media-query.</p>
        <pre class="prettyprint linenums">
<?php echo(htmlentities('<head>
    <link rel="stylesheet" href="css/grids/large.css">
')) ?>
        </pre>
        <p>Boom. Done. Now you only have to use the large classe names (see <a href="#columns">columns section</a>).</p>
        

    </div>
</div>

<div class="ink-container">
	<div class="ink-section">
		<div class="ink-vspace">
			<h2>Containers</h2>
			<h3><code>ink-container</code></h3>
        	<p>This is the main container for your layout. Just wrap everything with a block-level element, such as a <code>div</code>, with the <code>ink-container</code> class and you're set. This container unit will carry the width of your layout, which can be a fixed value, such as 960px, a relative value, such as 95% or a relative value with a limit, ie, a maximum width.</p>
			<p>Although, typically, you'll wrap entire layouts in an <code>ink-container</code>, you can close and re-open the container to mix different width elements in your pages. Imagine you have a 960 px webpage, on which you want a 100% width footer. Just close the <code>ink-container</code> before your <code>&lt;footer&gt;</code> element, and then use a 100% width class for it.</p>
	        <h3><code>ink-row</code> (optional)</h3>
    	    <p>If you need a set of side-by-side blocks, aka, columns <strong>with gutters, you'll need to wrap them in an <code>ink-row</code</strong>> block-level element. These will keep your columns together and work a little negative margin magic to make the gutters work as intended.</p>
        	<p>If, for some reason, you don't need space between your columns, then the <code>ink-row</code>is unnecessary and should not be used. A good example is if you need a single 100% column.</p>
        	<p>So, basically, if you're starting a layout, create a <code>&lt;div class=&quot;ink-container&quot;&gt;</code>, followed by a <code>&lt;div class=&quot;ink-row&quot;&gt;</code> and then proceed to lay out your columns (see below for layout and spacer elements), close up your blocks and you're done. Need more sections? Go ahead and create another block-level element with the <code>ink-row</code> class to hold it in place. You can even use <code>&lt;section&gt;</code> and the <code>ink-section</code> utility class gives you a separation line.</p>
		</div>
	</div>
	<div class="ink-section" id="markup">
   		<div class="ink-vspace">
            <h3>Markup</h3>
            <p>Let's say you need your page container to always be 80% of your viewport (on large screens).</p>
            <pre class="prettyprint linenums">
<?php echo(htmlentities('<div class="ink-container">
   <div class="ink-l80">
      <p>Content</p>
   </div>
</div>')) ?>
			</pre>
		</div>
    </div>
      
	<div class="ink-section" id="columns">
		<div class="ink-vspace">
            <h2>Columns</h2>
			<p>Layout elements in Ink is where we mix things up a bit from what you may be used to in other frameworks you may have tried before. Instead of defining a grid with a certain number of columns and having you then declare how many columns you need your layout blocks to span, <strong>you simply use percentages</strong>.</p>
			<p>If you need 3 columns, you use three 33% elements; if you need four, then use four 25% elements; it couldn't be simpler. But it's also extremely flexible and the built-in media-queries make building one layout for multiple screens a snap.</p>
			<p>You can setup 10, 20, 25, 30, 33, 40, 50, 66, 70, 75, 80, 90 and 100% width units and combinations therein and think in a simple, percentage-oriented, manner, leaving the calculations for each browser box model up to Ink.</p>
		</div>

        <div class="ink-row">
			<div class="ink-l50">
				<div class="ink-gutter">
					<div class="gridExample2">
						<div class="box">
							<div class="ink-l100 ink-m100 ink-s100 level1">
							   <div class="ink-l100 ink-m100 ink-s100 level2"><p>100%</p></div>
							   <div class="ink-l50 ink-m50 ink-s50 level2"><p>50%</p></div>
							   <div class="ink-l50 ink-m50 ink-s50 level2"><p>50%</p></div>
							   <div class="ink-l66 ink-m66 ink-s66 level2"><p>66.66%</p></div>
							   <div class="ink-l33 ink-m33 ink-s33 level2"><p>33.33%</p></div>
							   <div class="ink-l25 ink-m25 ink-s25 level2"><p>25%</p></div>
							   <div class="ink-l20 ink-m20 ink-s20 level2"><p>20%</p></div>
							   <div class="ink-l10 ink-m10 ink-s10 level2"><p>10%</p></div>
							   <div class="ink-l20 ink-m20 ink-s20 level2"><p>20%</p></div>
							   <div class="ink-l25 ink-m25 ink-s25 level2"><p>25%</p></div>
							</div>
						</div>
					</div>
					<pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<div class="ink-l100 ink-m100 ink-s100">
   <div class="ink-l50 ink-m50 ink-s50"></div>
   <div class="ink-l50 ink-m50 ink-s50"></div>
   <div class="ink-l66 ink-m66 ink-s66"></div>
   <div class="ink-l33 ink-m33 ink-s33"></div>
   <div class="ink-l25 ink-m25 ink-s25"></div>
   <div class="ink-l20 ink-m20 ink-s20"></div>
   <div class="ink-l10 ink-m10 ink-s10"></div>
   <div class="ink-l20 ink-m20 ink-s20"></div>
   <div class="ink-l25 ink-m25 ink-s25"></div>
</div>')) ?></pre>
                    <p class="note">Note: To simplify the example, we're maintaining proportions across the <a href="#multiple-layouts">multiple layouts</a>.</p>
				</div>
			</div>
			<div class="ink-l50 ink-for-l">
				<div class="ink-gutter">
					<div class="gridExample2">
						<div class="box">
							<div class="ink-l100 level1">
								<div class="ink-l50 level2"><p>50%</p></div>
								<div class="ink-l50 level2"><p>50%</p></div>
								<div class="ink-l50 level2">
                                    <div class="ink-l100"><p>100%</p></div>
									<div class="ink-l50"><p>50%</p></div>
									<div class="ink-l50"><p>50%</p></div>
								</div>
								<div class="ink-l50 level2">
                                    <div class="ink-l100"><p>100%</p></div>
									<div class="ink-l50"><p>50%</p></div>
									<div class="ink-l50"><p>50%</p></div>
								</div>
                                <div class="ink-l100 level2">
									<div class="ink-l25 level2"><p>25%</p></div>
									<div class="ink-l25 level2"><p>25%</p></div>
									<div class="ink-l25 level2"><p>25%</p></div>
									<div class="ink-l25 level2"><p>25%</p></div>
								</div>
							</div>
						</div>
						<pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<div class="ink-l100">
    <div class="ink-l50"></div>
    <div class="ink-l50"></div>
    <div class="ink-l50">
        <div class="ink-l100"></div>
        <div class="ink-l50"></div>
        <div class="ink-l50"></div>
    </div>
    <div class="ink-l50">
        <div class="ink-l100"></div>
        <div class="ink-l50"></div>
        <div class="ink-l50"></div>
    </div>
    <div class="ink-l25"></div>
    <div class="ink-l25"></div>
    <div class="ink-l25"></div>
    <div class="ink-l25"></div>
</div>')) ?></pre>
					</div>
				</div>
			</div>		
			<div class="ink-l50 ink-for-m">
				<div class="ink-gutter">
					<div class="gridExample2">
						<div class="box">
							<div class="ink-m100 level1">
								<div class="ink-m100 level2">
									<div class="ink-m100 level2"><p>100%</p></div>
									<div class="ink-m50 level2"><p>50%</p></div>
									<div class="ink-m50 level2"><p>50%</p></div>
								</div>
								<div class="ink-m100 level2">
									<div class="ink-m100 level2"><p>100%</p></div>
									<div class="ink-m50 level2"><p>50%</p></div>
									<div class="ink-m50 level2"><p>50%</p></div>
								</div>
								<div class="ink-m50 level2"><p>50%</p></div>
								<div class="ink-m50 level2"><p>50%</p></div>
								<div class="ink-m50 level2"><p>50%</p></div>
								<div class="ink-m50 level2"><p>50%</p></div>
							</div>
						</div>
					</div>
<pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<div class="ink-m100">
   <div class="ink-m100">
      <div class="ink-m100"></div>
      <div class="ink-m50"></div>
      <div class="ink-m50"></div>
   </div>
   <div class="ink-m100">
      <div class="ink-m100"></div>
      <div class="ink-m50"></div>
      <div class="ink-m50"></div>
   </div>
   <div class="ink-m50"></div>
   <div class="ink-m50"></div>
   <div class="ink-m50"></div>
   <div class="ink-m50"></div>
</div>')) ?></pre>
				</div>
			</div>
			<div class="ink-l50 ink-for-s">
				<div class="ink-gutter">
					<div class="gridExample2">
						<div class="box">
							<div class="ink-s100 level1">
								<div class="ink-s100 level2">
									<div class="ink-s100 level2"><p>100%</p></div>
									<div class="ink-s100 level2"><p>100%</p></div>
									<div class="ink-s100 level2"><p>100%</p></div>
								</div>
								<div class="ink-s100 level2">
									<div class="ink-s100 level2"><p>100%</p></div>
									<div class="ink-s100 level2"><p>100%</p></div>
									<div class="ink-s100 level2"><p>100%</p></div>
								</div>
								<div class="ink-s100 level2"><p>100%</p></div>
								<div class="ink-s100 level2"><p>100%</p></div>
								<div class="ink-s100 level2"><p>100%</p></div>
								<div class="ink-s100 level2"><p>100%</p></div>
							</div>
						</div>
					</div>
					<pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<div class="ink-s100">
   <div class="ink-s100">
      <div class="ink-s100"></div>
      <div class="ink-s100"></div>
      <div class="ink-s100"></div>
   </div>
   <div class="ink-s100">
      <div class="ink-s100"></div>
      <div class="ink-s100"></div>
      <div class="ink-s100"></div>
   </div>
   <div class="ink-s100"></div>
   <div class="ink-s100"></div>
   <div class="ink-s100"></div>
   <div class="ink-s100"></div>
</div>')) ?></pre>
				</div>
			</div>
            </div>
		<div class="ink-vspace">
      <p>
				You should use the <code>ink-lxx</code>, <code>ink-mxx</code> and <code>ink-sxx</code> class names only to specify the container width in the various screen sizes (large, medium or small), as shown in the examples above. 
				The purpose of these classes should be layout only. 
			</p>
			<p>	
				For further customization add an additional semantic class. 
			</p>
		</div>
	</div>
   
      <div class="ink-section" id="gutters">
         <div class="ink-vspace">
			   <h2>Gutters</h2>
    	     <p>To create gutters on you Ink based pages you need to wrap the elements that define column width in a <code>div</code> element with an <code>ink-row</code> class, and add anoter <code>div</code> element inside the columns wrapping all of its content with an <code>ink-gutter</code> class.</p>
        	 <p>If you need to add vertical space between the layout rows, simply add the <code>.ink-vspace</code> class to the <code>ink-row</code> wrapper.</p>
        	 <p>Gutter size changes proportionaly to the screen size, so there's no waste of space!</p>
         </div>
		    <div class="gridExample2">
            <div class="box">
               <div class="level1">
                  <div class="ink-l100 level2">
                     <p>100%</p>
                  </div>
                     <div class="ink-row ink-vspace">
                        <div class="ink-l50 ink-m50 ink-s50 level2">
                              <div class="ink-gutter">
                                 <p>50%</p>
                              </div>
                        </div>
                        <div class="ink-l50 ink-m50 ink-s50 level2">
                              <div class="ink-gutter">
                                 <p>50%</p>
                              </div>
                        </div>
                     </div>
                  <div class="ink-row ink-vspace">
                     <div class="ink-l33 ink-m33 ink-s33 level2">
                        <div class="ink-gutter">
                           <p>33%</p>
                        </div>
                     </div>
                     <div class="ink-l33 ink-m33 ink-s33 level2">
                        <div class="ink-gutter">
                           <p>33%</p>
                        </div>
                     </div>
                     <div class="ink-l33 ink-m33 ink-s33 level2">
                        <div class="ink-gutter">
                           <p>33%</p>
                        </div>
                     </div>
                  </div>
                  <div class="ink-row ink-vspace">
                     <div class="ink-l25 ink-m25 ink-s25 level2">
                        <div class="ink-gutter">
                           <p>25%</p>
                        </div>
                     </div>
                     <div class="ink-l25 ink-m25 ink-s25 level2">
                        <div class="ink-gutter">
                           <p>25%</p>
                        </div>
                     </div>
                     <div class="ink-l25 ink-m25 ink-s25 level2">
                        <div class="ink-gutter">
                           <p>25%</p>
                        </div>
                     </div>
                     <div class="ink-l25 ink-m25 ink-s25 level2">
                        <div class="ink-gutter">
                           <p>25%</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="ink-vspace">
			 <p class="note"><strong>Note:</strong> The example below shows the markup necessary to produce the structure in the example.</p>
<pre class="prettyprint linenums ">
<?php echo(htmlentities('<div class="ink-container">
   <div class="ink-l100">
      <p>100%</p>
   </div>
   <div class="ink-row ink-vspace"> <!-- div.ink-row is used to group several "columns" together. -->
      <div class="ink-l50 ink-m50">
         <div class="ink-gutter"> <!-- div.ink-gutter is used to add the gutter to the column -->
            <p>50%</p>
         </div>
      </div>
      <div class="ink-l50 ink-m50">
         <div class="ink-gutter">
            <p>50%</p>
         </div>
      </div>
   </div>
   <div class="ink-row ink-vspace">
      <div class="ink-l33">
         <div class="ink-gutter">
            <p>33%</p>
         </div>
      </div>
      <div class="ink-l33">
         <div class="ink-gutter">
            <p>33%</p>
         </div>
      </div>
      <div class="ink-l33">
         <div class="ink-gutter">
            <p>33%</p>
         </div>
      </div>
   </div>
   <div class="ink-row ink-vspace">
      <div class="ink-l25 ink-m50">
         <div class="ink-gutter">
            <p>25%</p>
         </div>
      </div>
      <div class="ink-l25 ink-m50">
         <div class="ink-gutter">
            <p>25%</p>
         </div>
      </div>
      <div class="ink-l25 ink-m50">
         <div class="ink-gutter">
            <p>25%</p>
         </div>
      </div>
      <div class="ink-l25 ink-m50">
         <div class="ink-gutter">
            <p>25%</p>
         </div>
      </div>
   </div>
</div>')) ?></pre>
      </div></div>


	<div class="ink-section" id="hidenseek">
		<div class="ink-vspace">
			<h2>Showing &amp; Hiding</h2>
			<p>Sometimes, re-arranging layouts is not enough. You'll need to make some elements appear or disappear. Feel free to use these class names to help you do just that.</p>

            <ul>
                <li><code>.ink-for-s</code> only appears when Layout <strong>Small</strong> is active</li>
                <li><code>.ink-for-m</code> only appears when Layout <strong>Medium</strong> is active</li>
                <li><code>.ink-for-l</code> only appears when Layout <strong>Large</strong> is active</li>
            </ul>
			<pre class="prettyprint linenums ink-vspace"><?php echo(htmlentities('
<p class="ink-for-s ink-for-m">I love mexican food.</p>
<p class="ink-for-l">I love mexican food, such as pico de gallo.</p>')) ?></pre>
			<p>By using any of these classes, hides the element on all other layouts.</p>
            <p class="note">Note: content hidden via these classes won't be read out loud by screenreaders.</p>
        </div>
    </div>
	  
	<div class="ink-section" id="spacing">
		<div class="ink-vspace">
			<h2>Spacing</h2>
			<p>
				Since Ink's approach to layout is not grid-based, but space division based, we needed to keep things simple spacing wise. 
				Despite meaning the need for extra markup elements, we feel the gained simplicity means you can build stuff faster and easier.
			</p>
			<p>
				Ink uses seven kinds of spacer unit: a vertical spacer, an horizontal spacer, and all-around spacer and one for each side of your box (top, right, bottom, left). 
				To use them, put a block-level element within your layout element with the corresponding spacer class.
			</p>
		</div>
      <div class="ink-row">
         <div class="ink-l33">
            <div class="ink-gutter">
               <div class="box">
                  <div class="ink-vspace"><p>.ink-vspace</p></div>
               </div>
<pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<!--Sets width to 33%-->
<div class="ink-l33">
   <!--Adds vertical margins-->
   <div class="ink-vspace">
      ...
   </div>
</div>')) ?></pre>
            </div>
         </div>
         <div class="ink-l33">
            <div class="ink-gutter">
               <div class="box">
                  <div class="ink-hspace"><p>.ink-hspace</p></div>
               </div>
<pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<!--Sets width to 33%-->
<div class="ink-l33">
   <!--Adds horizontal margins-->
   <div class="ink-hspace">
      ...
   </div>
</div>')) ?></pre>
         </div>
      </div>
         <div class="ink-l33">            
            <div class="ink-gutter">
               <div class="box">
                  <div class="ink-space"><p>.ink-space</p></div>
               </div>
<pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<!--Sets width to 33%-->
<div class="ink-l33">
   <!--Adds margins-->
   <div class="ink-space">
      ...
   </div>
</div>')) ?></pre>
            </div>
         
         </div>
      </div>
   </div>
   
</div>
