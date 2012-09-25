<div class="ink-container whatIs">
	<div class="ink-vspace">
		<h2>Forms</h2>
		<p>
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
			Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
		</p>
	</div>
</div>

<nav class="menu">
	<div class="ink-container">
		<ul class="ink-h-nav">
			<li class="active"><a class="home" href="#">Home</a></li>
			<li><a href="#">Form building</a></li>
			<li><a href="#">Text & Number entry</a></li>
			<li><a href="#">Dropdowns and list boxes</a></li>
			<li><a href="#">Buttons</a></li>
			<li><a href="#">Alerts</a></li>
		</ul>
	</div>
</nav>

<div class="ink-container">
	<div class="ink-section">
		<div class=" ink-space">
			<h3>Form building</h3>
			<p>
				Os campos dos formulários devem ser envolvidos por um elemento com a class <code>.e_wrap</code>. 
				Esta class existe para separar os elementos do formulários assim como assiste na estilização de erros
			</p>
			<pre class="prettyprint"><ol><li><span class="tag">&lt;form</span><span class="tag">&gt;</span></li><li>  <span class="tag">&lt;div</span><span class="pln"> </span><span class="atn">class</span><span class="pun">=</span><span class="atv">"e-wrap"</span><span class="tag">&gt;</span></li><li><span class="tag">    &lt;label</span><span class="pln"> </span><span class="atn">for</span><span class="pun">=</span><span class="atv">"inputId"</span><span class="tag">&gt;</span><span class="pln">Name</span><span class="tag">&lt;/label&gt;</span></li><li>    <span class="tag">&lt;input</span><span class="pln"> </span><span class="atn">type</span><span class="pun">=</span><span class="atv">"text" <span class="atn">id</span><span class="pun">=</span>"inputId"</span><span class="tag">&gt;</span></li><li><span class="tag">  &lt;/div&gt;</span><span class="tag"></span></li><li><span class="tag">&lt;/form&gt;</span><span class="tag"></span></li></ol></pre>
		</div>
	</div>
	<div class="ink-section">
		<div class="ink-l40">
			<div class=" ink-space">
				<h3>Text & Number entry</h3>
				<p>
					Arnold ipsum. I want my Larry. I wanna see you. I need a vacation. Bastards. Get out. My nipples are very sensitive. 
					This hero stuff has it's limits. I don't do requests. Crumb. Your luggage. Who the fuck are you? Get down or I'll put you down. 
					What killed the dinosaurs? The ice age. I'm the party pooper. Take your toy back to the carpet. You killed my fada. Big mistake.
				</p>
			</div>
		</div>
		<div class="ink-l60">
			<form action="" class="ink-labels-above ink-space">
				<fieldset>
					<legend><h4>Fieldset legend</h4></legend>
					<div class="ink-form-row">
						<label for="text-input">Text input</label>
						<input id="text-input" type="text" placeholder="Please input some text">
						<p class="ink-field-tip">You can add help text to fields</p>
					</div>
					<div class="ink-form-row ink-required-field">
						<label for="select">Select</label>
						<select name="" id="select">
							<option value="">onions</option>
							<option value="">carrots</option>
							<option value="">potatoes</option>
							<option value="">beets</option>
						</select>
						<p class="ink-form-validation ink-caution">An error occured. Let your user know about it</p>
					</div>
					<div class="ink-form-row ink-required-field">
						<label for="multiSelect">Multiple select</label>
						<select multiple="multiple" id="multiSelect">
							<option>onions</option>
							<option>carrots</option>
							<option>potatoes</option>
							<option>beets</option>
							<option>kale</option>
						</select>
					</div>
					<div class="ink-form-row">
						<label for="textarea">Textarea</label>
						<textarea name="" id="textarea" placeholder="Please enter some text"></textarea>
					</div>
					<div class="ink-form-row">
						<label for="file-input">File input</label>
						<input type="file" name="" id="file-input">
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	
	<div class="ink-section">
		<div class="ink-l40">
			<div class="ink-space">
				<h3>Checkboxes and radio buttons</h3>
				<p>
					You are not you you're me. This hero stuff has it's limits. But I'm all woman. Screw you! Go on! Do it now! Kill me! I'm here. 
					Just bodies. Do it! Do it! Come on! Kill me. I'm here. Do it now. Kill me. Stick around. I'm the party pooper. Only pain. 
					I need your clothes, your boots, and your motorcycle. Take your toy back to the carpet. I do not want to touch his ass. 
					I want to make him talk. Wrong. No I don't stop it. I want my baby. Blondes. 
					You're not going to have your mommies right behind you to wipe your little tushies. Grant me revenge. And if you do not listen, the hell with you.
				</p>
			</div>
		</div>
		<div class="ink-l60">
			<form action="" class="ink-hspace ink-labels-above ink-space">
				<fieldset>
					<legend><h4>Here's a group of checkboxes in a fieldset</h4></legend>
						<div class="ink-form-row">
							<p class="ink-field-tip">Please select one or more options</p>
							<input type="checkbox" id="cb1" name="cb1" value="">
							<label for="cb1">Option 1</label>
							<input type="checkbox" id="cb2" name="cb2" value="">
							<label for="cb2">Option 2</label>
							<input type="checkbox" id="cb3" name="cb3" value="">
							<label for="cb3">Option 3</label>
							<input type="checkbox" id="cb4" name="cb4" value="">
							<label for="cb4">Option 4</label>
						</div>
				</fieldset>
				<fieldset>
					<legend><h4>Here's a group of radio buttons in a fieldset</h4></legend>
						<div class="ink-form-row">
							<p class="ink-field-tip">Please select one of these option</p>
							<input type="radio" id="rb1" name="rb" value="">
							<label for="rb1">Option 1</label>
							<input type="radio" id="rb2" name="rb" value="">
							<label for="rb2">Option 2</label>
							<input type="radio" id="rb3" name="rb" value="">
							<label for="rb3">Option 3</label>
							<input type="radio" id="rb4" name="rb" value="">
							<label for="rb4">Option 4</label>
						</div>
				</fieldset>
			</form>
		</div>
	</div>
	
	<div class="ink-section">
		<div class="ink-space">
			<h3>Buttons</h3>
			<p>
				Button styling can be applied to almost any html element by using the
				<code>.ink-button</code>
				class.
			</p>
			<p>The <code>button</code>, <code>input[type="button"]</code> and <code>input[type="submit"]</code> elements are styled by default.</p>
		</div>
		<div class="ink-hspace">
		<table class="ink-table ink-bordered">
			<thead>
				<tr>
					<th>type</th>
					<th>active state</th>
					<th>disabled state</th>
					<th>description</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4">bla bla bla</td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td>Default</td>
					<td><button type="button">Default</button></td>
					<td><button disabled>Default</button></td>
					<td><code>&lt;button&gt;Default&lt;/button&gt;</code></td>
				</tr>
				<tr>
					<td>Success</td>
					<td><button class="ink-success">Success</button></td>
					<td><button class="ink-success" disabled>Success</button></td>
					<td>
						<code>&lt;button class=&quot;ink-success&quot;&gt;Success&lt;/button&gt;</code>
						<br>
						<code>&lt;button type=&quot;button&quot; class=&quot;ink-success disabled&quot;&gt;Success&lt;/button&gt;</code>
					</td>
				</tr>
				<tr>
					<td>Warning</td>
					<td><button class="ink-warning">Warning</button></td>
					<td><button class="ink-warning" disabled>Warning</button></td>
					<td><code>blah</code></td>
				</tr>
				<tr>
					<td>Caution</td>
					<td><button class="ink-caution">Caution</button></td>
					<td><button class="ink-caution" disabled>Caution</button></td>
					<td><code>blah</code></td>
				</tr>
				<tr>
					<td>Info</td>
					<td><button class="ink-info">Info</button></td>
					<td><button class="ink-info" disabled>Info</button></td>
					<td><code>blah</code></td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>
</div>