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
				<li class="active"><a class="home" href="#">Home</a></li>
				<li><a href="#">Containers</a></li>
				<li><a href="#">Spacer units</a></li>
				<li><a href="#">Spacing</a></li>
			</ul>
		</nav>
	</div>
</div>  

<!-- |||||||||||||||||||||||||||||||||  Content  |||||||||||||||||||||||||||||||||  -->     

<div class="ink-container">
   <div class="ink-vspace">
      <h2>Containers</h2>
      <p>To start off your project, you should define a container block to wrap everything else. Use a <code>div</code> with the <code>.ink-container</code> class, which can be configured to be a fixed width, a relative width or a maximum width.</p>
      <p>You can use this class as often as you want in your pages, which will allow you to mix fixed and elastic width blocks in a single page, or, if you prefer, simply use it to contain your layout.</p>
      <p>This element can be configured in the <a href="<?php echo site_url() . '/download' ?>">Ink Customizer</a>.</p>
   </div>
   <div class="ink-section">
      <div class="gridExample">
         <div class="ink-vspace box">
            <div class="ink-l100 ink-m100 ink-s100"><p>100%</p></div>
            <div class="ink-l90 ink-m90 ink-s90 ink-clear"><p>90%</p></div>
            <div class="ink-l80 ink-m80 ink-s80 ink-clear"><p>80%</p></div>
            <div class="ink-l75 ink-m75 ink-s75 ink-clear"><p>75%</p></div>
            <div class="ink-l70 ink-m70 ink-s70 ink-clear"><p>70%</p></div>
            <div class="ink-l66 ink-m66 ink-s66 ink-clear"><p>66%</p></div>
            <div class="ink-l60 ink-m60 ink-s60 ink-clear"><p>60%</p></div>
            <div class="ink-l50 ink-m50 ink-s50 ink-clear"><p>50%</p></div>
            <div class="ink-l40 ink-m40 ink-s40 ink-clear"><p>40%</p></div>
            <div class="ink-l33 ink-m33 ink-s33 ink-clear"><p>33%</p></div>
            <div class="ink-l30 ink-m30 ink-s30 ink-clear"><p>30%</p></div>
            <div class="ink-l25 ink-m25 ink-s25 ink-clear"><p>25%</p></div>
            <div class="ink-l20 ink-m20 ink-s20 ink-clear"><p>20%</p></div>
            <div class="ink-l10 ink-m10 ink-s10 ink-clear"><p>10%</p></div>
         </div>
         
         <div class="ink-vspace">
            <h3>Markup</h3>
            <p>Let's say you need your page container to always be 80% of your view port.</p>
            <pre class="prettyprint linenums">
<?php echo(htmlentities('<div class="ink-container">
   <div class="ink-l100">
     <p>Content</p>
   </div>
</div>')) ?>
            </pre>
         </div>
      </div>
   </div>
      
      <div class="ink-section">
         
         <div class="ink-vspace">
            <h2>Columns</h2>
            <p>Ink uses a percentage-based container logic which is flexible and promotes the use of fluid layouts.</p>
            <p>You can setup 10, 20, 25, 30, 33, 40, 50, 66, 70, 75, 80, 90 and 100% width units and combinations therein and think in a simple, percentage-oriented, manner, leaving the calculations for each browser box model up to Ink.</p>
         </div>
         
         <div class="ink-row">
            <div class="ink-l50">
               <div class="ink-gutter">
                  <div class="gridExample2">
                     <div class="box">
                        <div class="ink-l100 ink-m100 ink-s100 level1">
                           <div class="ink-l100 ink-m100 level2"><p>100%</p></div>
                           <div class="ink-l50 ink-m100 level2"><p>50%</p></div>
                           <div class="ink-l50 ink-m100 level2"><p>50%</p></div>
                           <div class="ink-l25 ink-m100 level2"><p>25%</p></div>
                           <div class="ink-l20 ink-m100 level2"><p>20%</p></div>
                           <div class="ink-l10 ink-m100 level2"><p>10%</p></div>
                           <div class="ink-l20 ink-m100 level2"><p>20%</p></div>
                           <div class="ink-l25 ink-m100 level2"><p>25%</p></div>
                        </div>
                     </div>
                  </div>
                  <pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<div class="ink-l100 ink-m100">
   <div class="inkl50 ink-m100"></div>
   <div class="inkl50 ink-m100"></div>
   <div class="ink-l25 ink-m100"></div>
   <div class="ink-l20 ink-m100"></div>
   <div class="ink-l10 ink-m100"></div>
   <div class="ink-l20 ink-m100"></div>
   <div class="ink-l25 ink-m100"></div>
</div>')) ?></pre>
                           </div>
               </div>
            <div class="ink-l50">
               <div class="ink-gutter">
                  <div class="gridExample2">
                     <div class="box">
                        <div class="ink-l100 ink-m100 ink-s100 level1">
                           <div class="ink-l50 ink-m100 ink-s100 level2">
                              <div class="ink-l100 ink-m100 ink-s100 level2"><p>50%</p></div>
                              <div class="ink-l50 ink-m50 ink-s100 level2"><p>50%</p></div>
                              <div class="ink-l50 ink-m50 ink-s100 level2"><p>50%</p></div>
                           </div>
                           <div class="ink-l50 ink-m100 ink-s100 level2">
                              <div class="ink-l100 ink-m100 ink-s100 level2"><p>50%</p></div>
                              <div class="ink-l50 ink-m50 ink-s100 level2"><p>50%</p></div>
                              <div class="ink-l50 ink-m50 ink-s100 level2"><p>50%</p></div>
                           </div>
                           <div class="ink-l25 ink-m50 ink-s100 level2"><p>25%</p></div>
                           <div class="ink-l25 ink-m50 ink-s100 level2"><p>25%</p></div>
                           <div class="ink-l25 ink-m50 ink-s100 level2"><p>25%</p></div>
                           <div class="ink-l25 ink-m50 ink-s100 level2"><p>25%</p></div>
                        </div>
                     </div>
                  </div>
                              <pre class="prettyprint linenums ink-vspace">
<?php echo(htmlentities('<div class="ink-l100 ink-m100 ink-s100">
   <div class="ink-l50 ink-m100 ink-s100">
      <div class="ink-l100 ink-m100 ink-s100"></div>
      <div class="ink-l50 ink-m50 ink-s100"></div>
      <div class="ink-l50 ink-m50 ink-s100"></div>
   </div>
   <div class="ink-l50 ink-m100 ink-s100">
      <div class="ink-l100 ink-m100 ink-s100"></div>
      <div class="ink-l50 ink-m50 ink-s100"></div>
      <div class="ink-l50 ink-m50 ink-s100"></div>
   </div>
   <div class="ink-l25 ink-m50 ink-s100"></div>
   <div class="ink-l25 ink-m50 ink-s100"></div>
   <div class="ink-l25 ink-m50 ink-s100"></div>
   <div class="ink-l25 ink-m50 ink-s100"></div>
</div>')) ?></pre>
               </div>
            </div>
         </div>
         <div class="ink-vpace">
            <p>
				You should use the <strong>.ink-lxx</strong>, <strong>.ink-mxx</strong> and <strong>.ink-sxx</strong> classes to specify the container width in the various screen sizes (large, medium or small), as shown in the examples above. 
				The purpose of these classes should be layout only. 
			</p>
			<p>	
				For further costumization add an additional semantic class. 
				In this case, you could use your conf.css file to customize .maincontent, .sidebar, etc.
			</p>
         </div>
      </div>
   
      <div class="ink-section">
         <div class="ink-vspace">
			 <h2>Gutters</h2>
    	     <p>To create gutters on you Ink based pages you need to wrap the elements that define column width in a <code>div</code> element with a <code>.ink-row</code> class, and add anoter <code>div</code> element inside the columns wrapping all of its content with a <code>.ink-gutter</code> class.</p>
        	 <p>If you need to add vertical space between the layout rows, simply add the <code>.ink-vspace</code> class.</p>
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
	  
	<div class="ink-section" id="spaceExamples">
		<div class="ink-vspace">
			<h2>Spacer units</h2>
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
   
   <div class="ink-vspace">
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
