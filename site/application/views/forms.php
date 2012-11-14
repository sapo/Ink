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
			<!-- <p>There are four essential classes you need to know to work with forms in Ink: <code>ink-form</code>, <code>ink-form-inline</code> and <code>ink-form-wrapper</code>. The first two are mutually exclusive, use the first one in your <code>&lt;form&gt;</code> element if you want your labels stacked on your fields (block), or the second one, if you prefer labels on the left of your fields. We strongly advise you use block as it's easier to read.
			<p>Finally, the third class needs to be applied to a <code>&lt;div&gt;</code> element, containing each of your label/field pairs. This helps separate and align elements in your form. If you're creating a set of checkboxes or radio buttons, then apply this class to the <code>&lt;ul&gt;</code> element, while also adding the <code>unstyled</code> class, to remove bullets.</p> -->
			<p>There are some essential classes you'll use to build forms with Ink: <code>ink-form</code>, <code>block</code>, <code>inline</code>, <code>control</code> and <code>control-group</code>.</p>
			<p>To get started, add the <code>ink-form</code> and <code>block</code> or <code>inline</code> class to you form element.</p>
			<p>To add labels and form fields use a <code>div</code> element with the <code>control</code> class to wrap each label/field pair.</p>
			<p>Don't forget to put your fields in a fieldset element!</p> 
			<p>Check out the examples below.</p>
<pre class="prettyprint linenums">
<?php echo(htmlentities('<form class="ink-form block">
  <fieldset>
    <div class="control">
      <label for="inputId">Name</label>
      <input type="text" id="inputId">
    </div>
  <fieldset>
</form>')) ?>
</pre>
					 </div>
				</div>
				<div class="ink-section">
					 <a name="text-numbers"></a>
					 <h3>
						  Text &amp; Number entry
					 </h3>
					 <div class="ink-row ink-vspace">
						  <div class="ink-l50">
								<div class="ink-gutter">
									 <form action="" class="ink-form block">
										  <fieldset>
												<legend>Fieldset legend</legend>
												<div class="control">
													 <label for="text-input" class="short">Text input</label> 
													 <input id="text-input" type="text" placeholder="Please input some text" class="medium">
													 <p class="tip space short">
														  You can add help text to fields
													 </p>
												</div>
												<div class="control validation warning">
													 <label for="text-input" class="short">Text input</label> 
													 <input id="text-input2" type="text" placeholder="Please input some text" class="medium">
													 <p class="tip space short">
														  Warn your user about some problem with the form.
													 </p>
												</div>
												<div class="control required validation error">
													 <label for="text-input" class="short">Text input</label> <input id="text-input3" type="text" placeholder="Please input some text">
													 <p class="tip space short">
														  This field is required.
													 </p>
												</div>
												<div class="control">
													 <label for="select" class="short">Select</label> <select name="" id="select" class="medium">
														  <option value="">
																onions
														  </option>
														  <option value="">
																carrots
														  </option>
														  <option value="">
																potatoes
														  </option>
														  <option value="">
																beets
														  </option>
													 </select>
												</div>
												<div class="control">
													 <label for="multiSelect" class="short">Multiple select</label> <select multiple="multiple" id="multiSelect" class="medium">
														  <option>
																onions
														  </option>
														  <option>
																carrots
														  </option>
														  <option>
																potatoes
														  </option>
														  <option>
																beets
														  </option>
														  <option>
																kale
														  </option>
													 </select>
												</div>
												<div class="control">
													 <label for="textarea" class="short">Textarea</label> 
													 <textarea name="" id="textarea" placeholder="Please enter some text" class="medium">
</textarea>
												</div>
												<div class="control">
													 <label for="file-input" class="short">File input</label> <input type="file" name="" id="file-input" class="medium">
												</div>
										  </fieldset>
									 </form>
								</div>
						  </div>
						  <div class="ink-l50">
								<div class="ink-gutter">
									 <h4>
										  Block form
									 </h4>
									 <p>
										  In this example, the <code>&lt;label&gt;</code> for each field is located above the field, in block. To achieve this we used <code>block</code> in the <code>&lt;form&gt;</code> element.
									 </p>
									 <h4>
										  Required fields and warnings
									 </h4>
									 <p>
										  If you have a required field, use the field wrapping element (<code>control</code> or <code>control-group</code> ) to add a <code>required</code> class.
									 </p>
									 <p>
										  If you need to print a warning or error message near your field, then add the <code>validation</code> class to the wrapper (<code>control</code> or <code>control-group</code>) and an <code>warning</code> or <code>error</code> class.
									 </p>
									 <p>Also add a message using a paragraph element with the <code>tip</code> class.</p>
									 <!-- <h4>
										  Automated form validation
									 </h4>
									 <p>
										  <code>validation</code> instructions should be added here...
									 </p> -->
									 <h5>
										  Example
									 </h5>
									 <p>
										  Here's a simple form with a required field and a warning message:
									 </p>
									 <pre class="prettyprint linenums">
<?php echo(htmlentities('<form class="ink-form block">
  <div class="control validation warning">
    <label for="inputId">Text input</label>
    <input type="text" id="inputId2">
    <p class="tip">Warn about something</p>
  </div>
</form>')) ?>
</pre>
									 <form class="ink-form block">
										  <div class="control validation warning">
												<label for="inputId">Text input</label> <input type="text" id="inputId3">
												<p class="tip">
													 Warn about something
												</p>
										  </div>
									 </form>
								</div>
						  </div>
					 </div>
					 <div class="ink-row">					 	
					 	<div class="ink-l50">
					 		<div class="ink-gutter">
					 			<form action="#" class="ink-form inline">
					 				<fieldset>
					 					<legend>Fieldset</legend>
					 					<div class="control required validation error">
						 					<label for="name" class="short">Name</label>
						 					<input type="text" id="name" class="wide">
						 					<p class="tip space short">Here's a small text tip</p>
						 				</div>
						 				<div class="control">
						 					<label for="phone" class="short">Phone</label>
						 					<input type="text" id="phone" class="medium">
						 					<p class="tip space short">Please include the country code</p>
						 				</div>
						 				<div class="control">
						 					<label for="email" class="short">Email</label>
						 					<input type="text" id="email" class="wide">
						 				</div>
						 				<div class="control">
						 					<label for="options" class="short">Email</label>
						 					<select id="option" class="medium">
						 						<option value="1">option 1</option>
						 						<option value="2">option 2</option>
						 						<option value="3">option 3</option>
						 						<option value="4">option 4</option>
						 					</select>
						 				</div>
						 				<div class="control">
						 					<label for="area" class="short">Description</label>
						 					<textarea id="area" class="wide"></textarea>
						 					<p class="tip space short">256 character limit.</p>
						 				</div>
						 				<div class="control">
						 					<label for="options" class="short">Email</label>
						 					<select id="option" multiple="multiple" class="short">
						 						<option value="1">option 1</option>
						 						<option value="2">option 2</option>
						 						<option value="3">option 3</option>
						 						<option value="4">option 4</option>
						 					</select>
						 				</div>
					 				</fieldset>
					 			</form>
					 		</div>
					 	</div>
					 	<div class="ink-l50">
					 		<div class="ink-gutter">
					 			<h4>Inline Forms</h4>
					 			<p>Ink provides a second layout for forms. To get it add the <code>inline</code> class to the form element or replace an existing <code>block</code> class.</p>
					 			<p>Since this layout requires that labels and fields have a set width, we added some classes to help you getting things aligned in a breeze: <code>shorter</code>, <code>short</code>, <code>medium</code>, <code>wide</code>	and <code>wider</code>.</p>
					 			<p>Combining these lets you deal with diferent widths of labels and fields. Also you can align tip text by adding a <code>space</code> class and one of the above. Matching the label width class aligns the tip text with the field.</p>

<pre class="prettyprint linenums">
<?php echo(htmlentities('<form action="#" class="ink-form inline">
  <fieldset>
    <legend>Personal data</legend>
    <div class="control required validation error">
      <label for="name" class="short">Name</label>
      <input type="text" id="name" class="wide">
      <p class="tip space short">Here\'s a small text tip</p>
    </div>
    ...
  </fieldset>
</form>')) ?></pre>
					 		</div>
					 	</div>
					 </div>
				</div>
				<div class="ink-section">
					 <div class="ink-vspace">
					 	<a name="checkboxes-radios"></a>
					 <h3>
						  Checkboxes and radio buttons
					 </h3>
					 <p>
						  To create a list of checkboxes or radio buttons, use an unordered list element, <code>&lt;ul&gt;</code> as your wrapper with the <code>control-group</code> class. If you need to add a label to the field group, add another <code>li</code> element and a <code>p.label</code>
						  inside it. This pseudo-label will also display the icon if the control group contains required fields.
					 </p>
					 </div>
					 <div class="ink-row">
						  <div class="ink-l50">
								<div class="ink-gutter">
									<form action="" class="ink-form block">
										 <fieldset>
													<legend>Group of checkboxes</legend>
											  <ul class="control-group required validation error">
													<li>
														 <p class="label">
															  Please select one or more options
														 </p>
													</li>
													<li>
														 <input type="checkbox" id="cb1" name="cb1" value=""> <label for="cb1">Option 1</label>
													</li>
													<li>
														 <input type="checkbox" id="cb2" name="cb2" value=""> <label for="cb2">Option 2</label>
													</li>
													<li>
														 <input type="checkbox" id="cb3" name="cb3" value=""> <label for="cb3">Option 3</label>
													</li>
													<li>
														 <input type="checkbox" id="cb4" name="cb4" value=""> <label for="cb4">Option 4</label>
													</li>
											  </ul>
										 </fieldset>					 
									</form>

								</div>
						  </div>
						  <div class="ink-l50">
								<div class="ink-gutter">
									<form action="" class="ink-form block">
										 <fieldset>
													<legend>Group of radio buttons</legend>
											  <ul class="control-group">
													<li>
														 <p class="label">
															  Please select one of these options
														 </p>
													</li>
													<li>
														 <input type="radio" id="rb1" name="rb" value=""> <label for="rb1">Option 1</label>
													</li>
													<li>
														 <input type="radio" id="rb2" name="rb" value=""> <label for="rb2">Option 2</label>
													</li>
													<li>
														 <input type="radio" id="rb3" name="rb" value=""> <label for="rb3">Option 3</label>
													</li>
													<li>
														 <input type="radio" id="rb4" name="rb" value=""> <label for="rb4">Option 4</label>
													</li>
											  </ul>
										 </fieldset>
									</form>
								</div>
						  </div>
					 </div>
					 <div class="ink-row">
					 	<div class="ink-l50">
					 		<div class="ink-gutter">
					 			<pre class="prettyprint linenums">
<?php echo(htmlentities('<form action="" class="ink-form block">
  <fieldset>
    <legend>Group of checkboxes</legend>
    <ul class="control-group required">
      <li>
        <p class="label">Select one or more options</p>
      </li>
      ...
      <li>
          <input type="checkbox" id="cb4" value="">
          <label for="cb4">Option 4</label>
      </li>
    </ul>
  </fieldset>
</form>')) ?></pre>
					 		</div>
					 	</div>
					 	<div class="ink-l50">
					 		<div class="ink-gutter">
<pre class="prettyprint linenums">
<?php echo(htmlentities('<form action="" class="ink-form block ink-gutter">
  <fieldset>
    <legend>Group of radio buttons</legend>
      <ul class="control-group">
        <li>
          <p class="label">Please select one option.</p>
        </li>
          ...
        <li>
           <input type="radio" id="rb4" value="rbOption">
           <label for="rb4">Option 4</label>
        </li>
      </ul>
  </fieldset>
</form>')) ?></pre>
					 		</div>
					 	</div>
					 </div>
				</div>
				<div class="ink-section">
					 <a name="buttons"></a>
					 <h3>
						  Buttons
					 </h3>
					 <p>
						  Your forms will need at least one button. You can crate a button with one of several markup elements, although <code>&lt;button&gt;</code> is most appropriate. Just add the <code>ink-button</code> class for the button layout and then use a utility class to give it a specific meaning. Available styling is shown below.
					 </p>
					 <table class="ink-table ink-bordered">
						  <thead>
								<tr>
									 <th>
										  type
									 </th>
									 <th>
										  active state
									 </th>
									 <th>
										  disabled state
									 </th>
									 <th>
										  code
									 </th>
								</tr>
						  </thead>
						  <tfoot>
								<tr>
									 <td>
										  Info
									 </td>
									 <td>
										  <button class="ink-button info">Info</button>
									 </td>
									 <td>
										  <button class="ink-button info" disabled>Info</button>
									 </td>
									 <td>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button info">Info</button>')) ?>
</pre>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button info" disabled>Info</button>')) ?>
</pre>
									 </td>
								</tr>
						  </tfoot>
						  <tbody>
								<tr>
									 <td>
										  Default
									 </td>
									 <td>
										  <button class="ink-button">Default</button>
									 </td>
									 <td>
										  <button class="ink-button" disabled>Default</button>
									 </td>
									 <td>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button">Default</button>')) ?>
</pre>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button" disabled>Default</button>')) ?>
</pre>
									 </td>
								</tr>
								<tr>
									 <td>
										  Success
									 </td>
									 <td>
										  <button class="ink-button success">Success</button>
									 </td>
									 <td>
										  <button class="ink-button success" disabled>Success</button>
									 </td>
									 <td>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button success">Success</button>')) ?>
</pre>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button success" disabled>Success</button>')) ?>
</pre>
									 </td>
								</tr>
								<tr>
									 <td>
										  Warning
									 </td>
									 <td>
										  <button class="ink-button warning">Warning</button>
									 </td>
									 <td>
										  <button class="ink-button warning" disabled>Warning</button>
									 </td>
									 <td>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button warning">Warning</button>')) ?>
</pre>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button warning" disabled>Warning</button>')) ?>
</pre>
									 </td>
								</tr>
								<tr>
									 <td>
										  Caution
									 </td>
									 <td>
										  <button class="ink-button caution">Caution</button>
									 </td>
									 <td>
										  <button class="ink-button caution" disabled>Caution</button>
									 </td>
									 <td>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button caution">Caution</button>')) ?>
</pre>
										  <pre class="prettyprint linenums">
<?php echo(htmlentities('<button class="ink-button caution" disabled>Caution</button>')) ?>
</pre>									 </td>
								</tr>
						  </tbody>
					 </table>
				</div>
		  </div>		  
	 </body>
</html>
