<div class="whatIs">
   <div class="ink-container">
		<h2>Forms</h2>
		<p>Form nightmare building made easy.</p>
	</div>
</div>

<div class="menu-second-level">
	<div class="ink-container">
		<nav class="ink-navigation">
			<ul class="menu horizontal">
				<li class="active"><a class="scrollableLink home" href="#">Home</a></li>
				<li><a class="scrollableLink" href="#building">Form building</a></li>
				<li><a class="scrollableLink" href="#text-numbers">Text & Number entry</a></li>
				<li><a class="scrollableLink" href="#checkboxes-radios">Checkboxes & Radio buttons</a></li>
				<li><a class="scrollableLink" href="#buttons">Buttons</a></li>
			</ul>
		</nav>
	</div>
</div>

<div class="ink-container">
	<div class="ink-section">
		<div class="ink-vspace">
			<h3>Form essentials</h3>
			<p>There are three essential classes you need to know to work with forms in Ink: <code>ink-form-block</code>, <code>ink-form-inline</code> and <code>ink-form-wrapper</code>. The first two are mutually exclusive, use the first one in your <code>&lt;form&gt;</code> element if you want your labels stacked on your fields (block), or the second one, if you prefer labels on the left of your fields. We strongly advise you use block as it's easier to read.
			<p>Finally, the third class needs to be applied to a <code>&lt;div&gt;</code> element, containing each of your label/field pairs. This helps separate and align elements in your form. If you're creating a set of checkboxes or radio buttons, then apply this class to the <code>&lt;ul&gt;</code> element, while also adding the <code>unstyled</code> class, to remove bullets.</p>
			<p>You'll see examples to all this, below.</p>
<pre class="prettyprint linenums">
<?php echo(htmlentities('<form class="ink-form-block">
  <div class="ink-form-wrapper">
    <label for="inputId">Name</label>
    <input type="text" id="inputId">
  </div>
</form>')) ?></pre>
		</div>
	</div>
	
	<div class="ink-section">
		<a name="text-numbers"></a>
		<h3>Text & Number entry</h3>
		<div class="ink-row ink-vspace">
			<div class="ink-l50">
				<div class="ink-gutter">
					<form action="" class="ink-form-block">
						<fieldset>
							<legend><h4>Fieldset legend</h4></legend>
							<div class="ink-form-wrapper">
								<label for="text-input">Text input</label>
								<input id="text-input" type="text" placeholder="Please input some text">
								<p class="ink-field-tip">You can add help text to fields</p>
							</div>
							<div class="ink-form-wrapper ink-warning-field">
								<label for="text-input">Text input</label>
								<input id="text-input" type="text" placeholder="Please input some text">
								<p class="ink-form-validation ink-warning">Warn about somthing</p>
							</div>
							<div class="ink-form-wrapper ink-required-field">
								<label for="text-input">Text input</label>
								<input id="text-input" type="text" placeholder="Please input some text">
								<p class="ink-form-validation ink-caution">Something is missing. Let your user know about it</p>
							</div>
							<div class="ink-form-wrapper">
								<label for="select">Select</label>
								<select name="" id="select">
									<option value="">onions</option>
									<option value="">carrots</option>
									<option value="">potatoes</option>
									<option value="">beets</option>
								</select>
								
							</div>
							<div class="ink-form-wrapper">
								<label for="multiSelect">Multiple select</label>
								<select multiple="multiple" id="multiSelect">
									<option>onions</option>
									<option>carrots</option>
									<option>potatoes</option>
									<option>beets</option>
									<option>kale</option>
								</select>
							</div>
							<div class="ink-form-wrapper">
								<label for="textarea">Textarea</label>
								<textarea name="" id="textarea" placeholder="Please enter some text"></textarea>
							</div>
							<div class="ink-form-wrapper">
								<label for="file-input">File input</label>
								<input type="file" name="" id="file-input">
							</div>
						</fieldset>
					</form>
				</div>
			</div>
			<div class="ink-l50">
				<div class="ink-gutter">
					<h4>Block form</h4>
					<p>
						In this example, the <code>&lt;label&gt;</code> for each field is located above the field, in block.
						To achieve this we used <code>ink-form-block</code> in the <code>&lt;form&gt;</code> element.
					</p>
					<h4>Required fields and warnings</h4>
					<p>If you have a required field, use the field wrapping element (<code>ink-form-wrapper</code>) to add a <code>ink-required-field</code> class.</p>
					<p>If you need to print a warning or error message near your field, then add the <code>ink-warning-field</code> to the wrapper and follow the field with a paragraph containing the message.
					</p>
					<h4>Automated form validation</h4>
					<p><code>ink-form-validation</code> instructions should be added here...</p>
					<h5>Example</h5>
					<p>Here's a simple form with a required field and a warning message:</p>
				<pre class="prettyprint linenums">
<?php echo(htmlentities('<form class="ink-form-block">
   <div class="ink-form-wrapper ink-warning-field">
      <label for="inputId">Text input</label>
      <input type="text" id="inputId">
      <p class="ink-form-validation ink-warning">Warn about something</p>
   </div>
</form>')) ?></pre>
<form class="ink-form-block">
   <div class="ink-form-wrapper ink-warning-field">
      <label for="inputId">Text input</label>
      <input type="text" id="inputId">
      <p class="ink-form-validation ink-warning">Warn about something</p>
   </div>
</form>
				</div>
			</div>
		</div>
	</div>
	
	<div class="ink-section">
		<a name="checkboxes-radios"></a>
		<h3>Checkboxes and radio buttons</h3>
		<p>To create a list of checkboxes or radio buttons, use an unordered list element, <code>&lt;ul&gt;</code> as your wrapper (<code>ink-form-wrapper</code>) with the <code>unstyled</code> class, to eliminate bullets.</p>
		<div class="ink-row ink-vspace">
			<div class="ink-l50">
				<form action="" class="ink-form-block ink-gutter">
					<fieldset>
						<legend><h4>Group of checkboxes</h4></legend>
						<ul class="ink-form-wrapper unstyled">
							<p class="ink-field-tip">Please select one or more options</p>
							<li>
								<input type="checkbox" id="cb1" name="cb1" value="">
								<label for="cb1">Option 1</label>
							</li>
							<li>
								<input type="checkbox" id="cb2" name="cb2" value="">
								<label for="cb2">Option 2</label>
							</li>
							<li>
								<input type="checkbox" id="cb3" name="cb3" value="">
								<label for="cb3">Option 3</label>
							</li>
							<li>
								<input type="checkbox" id="cb4" name="cb4" value="">
								<label for="cb4">Option 4</label>
							</li>
						</ul>
					</fieldset>
					<pre class="prettyprint"><ol><li><span class="tag">&lt;form</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-block"</span>&gt;</span></li><li>  <span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-wrapper unstyled"</span><span class="tag">&gt;</span></li><li>    <span class="tag">&lt;li</span><span class="pln"></span><span class="tag">&gt;</span></li><li><span class="tag">      &lt;label</span><span class="pln"> </span><span class="atn">for</span><span class="pun">=</span><span class="atv">"checkboxId"</span><span class="tag">&gt;</span><span class="pln">Option 1</span><span class="tag">&lt;/label&gt;</span></li><li>    <span class="tag">  &lt;input</span><span class="pln"> </span><span class="atn">type</span><span class="pun">=</span><span class="atv">"text" <span class="atn">id</span><span class="pun">=</span>"checkboxId"</span><span class="tag">&gt;</span></li><li><span class="tag">    &lt;li</span><span class="pln"></span><span class="tag">&gt;</span></li><li class="com">    ...</li><li><span class="tag">  &lt;/ul&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/form&gt;</span><span class="tag"></span></li></ol></pre>
				</form>	
			</div>
			<div class="ink-l50">
				<form action="" class="ink-form-block ink-gutter">
					<fieldset>
						<legend><h4>Group of radio buttons</h4></legend>
						<ul class="ink-form-wrapper unstyled">
							<p class="ink-field-tip">Please select one of these options</p>
							<li>
								<input type="radio" id="rb1" name="rb" value="">
								<label for="rb1">Option 1</label>
							</li>
							<li>
								<input type="radio" id="rb2" name="rb" value="">
								<label for="rb2">Option 2</label>
							</li>
							<li>
								<input type="radio" id="rb3" name="rb" value="">
								<label for="rb3">Option 3</label>
							</li>
							<li>
								<input type="radio" id="rb4" name="rb" value="">
								<label for="rb4">Option 4</label>
							</li>
						</ul>
					</fieldset>
					<pre class="prettyprint"><ol><li><span class="tag">&lt;form</span><span class="tag"> <span class="pln"></span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-block"</span>&gt;</span></li><li>  <span class="tag">&lt;ul</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"ink-form-wrapper unstyled"</span><span class="tag">&gt;</span></li><li>    <span class="tag">&lt;li</span><span class="pln"></span><span class="tag">&gt;</span></li><li><span class="tag">      &lt;label</span><span class="pln"> </span><span class="atn">for</span><span class="pun">=</span><span class="atv">"radioId"</span><span class="tag">&gt;</span><span class="pln">Option 1</span><span class="tag">&lt;/label&gt;</span></li><li>    <span class="tag">  &lt;input</span><span class="pln"> </span><span class="atn">type</span><span class="pun">=</span><span class="atv">"radio" <span class="atn">id</span><span class="pun">=</span>"radioId"</span><span class="tag">&gt;</span></li><li><span class="tag">    &lt;li</span><span class="pln"></span><span class="tag">&gt;</span></li><li class="com">    ...</li><li><span class="tag">  &lt;/ul&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/form&gt;</span><span class="tag"></span></li></ol></pre>
				</form>	
			</div>
		</div>
		
	</div>
	
	<div class="ink-section">
		<a name="buttons"></a>
		<h3>Buttons</h3>
		<p>Your forms will need at least one button. You can crate a button with one of several markup elements, although <code>&lt;button&gt;</code> is most appropriate. Just add the <code>ink-button</code> class for the button layout and then use a utility class to give it a specific meaning. Available styling is shown below.</p>
		
		<table class="ink-table ink-bordered">
			<thead>
				<tr>
					<th>type</th>
					<th>active state</th>
					<th>disabled state</th>
					<th>code</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td>Info</td>
					<td><button class="ink-button info">Info</button></td>
					<td><button class="ink-button info" disabled>Info</button></td>
					<td><pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button info">Info</button>')) ?></pre>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button info" disabled>Info</button>')) ?></pre>
						</td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td>Default</td>
					<td><button class="ink-button">Default</button></td>
					<td><button class="ink-button" disabled>Default</button></td>
					<td><pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button">Default</button>')) ?></pre>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button" disabled>Default</button>')) ?></pre>
						</td>
				</tr>
				<tr>
					<td>Success</td>
					<td><button class="ink-button success">Success</button></td>
					<td><button class="ink-button success" disabled>Success</button>
					</td>
					<td>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button success">Success</button>')) ?></pre>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button success" disabled>Success</button>')) ?></pre></p>
					</td>
				</tr>
				<tr>
					<td>Warning</td>
					<td><button class="ink-button warning">Warning</button></td>
					<td><button class="ink-button warning" disabled>Warning</button></td>
					<td><pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button warning">Warning</button>')) ?></pre>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button warning" disabled>Warning</button>')) ?></pre></td>
				</tr>
				<tr>
					<td>Caution</td>
					<td><button class="ink-button caution">Caution</button></td>
					<td><button class="ink-button caution" disabled>Caution</button></td>
					<td><pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button caution">Caution</button>')) ?></pre>
						<pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button caution" disabled>Caution</button>')) ?></pre></td>
				</tr>
			</tbody>
			
		</table>
	</div>
</div>
