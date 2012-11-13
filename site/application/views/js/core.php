<div class="whatIs">
   <div class="ink-container">
        <h2>Ink JS Core</h2>
        <p>The engine that drives the machine.</p>
    </div>
</div>

<div class="ink-container">
Ink was built from the ground up to be easy to use and easy on the eyes, but some of the bricks were borrowed from one of our other premiere web frameworks, LibSAPO.js. Listed here are a few key classes and methods that you can take advantage of when building your applications.</p>

<h2>SAPO</h2>

<p>This is the core object which all other classes from LibSAPO.js will be placed.</p>

<p><em>require</em> - Load only the JavaScript that you need when you want. You can mix and match any JavaScript available on the web, but be careful, and learn from Dr. Frankenstein's mistake, lest you create a monster!</p>

<pre><code>SAPO.require(
    // Load some LibSAPO.js utility classes.
    ['SAPO.Utility.String', 'SAPO.Utility.Url'],

    // This function is executed after the code has been loaded
    function(){
        // Do cool stuffs here.
    }
);
</code></pre>

<p><em>browser</em> - Is the user on Internet Explorer? What version? What model? Browser detection should be used sparingly, but when you need it, we've got you covered.</p>

<pre><code>// Is the user on IE?
if(SAPO.Browser.IE){
    // If so, do strange stuffs here.
}
</code></pre>

<p><em>namespace</em> - Sometimes it's convenient to extend the framework to fit your needs, maybe you want to add a new component, or maybe you want to create a new collection of classes.</p>

<pre><code>// Create or fetch SAPO.Component.UIThingy.
var SUIT = SAPO.namespace('Component.UIThingy');

// Add some crazy method to our namespace.
SUIT.up = function(){
    setTimeout(console.log.bindObj(console, 'dary!'), 500);
    console.log('Legen... wait for it...');
}
</code></pre>

<p>SAPO.namespace will always return an object.</p>

<p><em>bindObj and bindObjEvent</em> - Binding functions is an important concept of JavaScript but not all browsers support it, but don't worry, LibSAPO.js gives you the tools you need to get the job done.</p>

<pre><code>SAPO.Dom.Event.observe(myElement, 'click', function(e){
    // Do some awesome event stuffs here.
}.bindObjEvent(SUIT));

setTimeout(function(){
    // Do some interesting async stuffs here.
}.bindObj(SUIT), 500);
</code></pre>

<p><a href="http://js.sapo.pt/SAPO/doc.html">API Link</a></p>

<h2>Dom</h2>

<p>The DOM is a mess, you know it and we do too, that's why we've added specialized Dom methods to LibSAPO.js, it makes your life easier and that makes us happy.</p>

<ul>
<li>CSS</li>
<li>Element</li>
<li>Event</li>
<li>Selector</li>
</ul>

<p><a href="http://js.sapo.pt/SAPO/Dom/doc.html">API Link</a></p>

<h2>Communication</h2>

<p>LibSAPO.js offers you plenty of ways to get your web site communicating with the vast collection of public services available on the web. CORS enabled AJAX, JsonP and Syndication will get you up and running in no time.</p>

<ul>
<li>Ajax</li>
<li>JsonP</li>
<li>Syndication</li>
</ul>

<p><a href="http://js.sapo.pt/SAPO/Communication/doc.html">API Link</a></p>

<h2>Utility</h2>

<p>Even certain bat suit wearing ninjas need to use utilities every one in a while, and what are JavaScript developers if not ninjas without the bat suit? LibSAPO.js offers those who possess the script-fu ways to take their skills to the next level.</p>

<p><a href="http://js.sapo.pt/SAPO/Utility/doc.html">API Link</a></p>

<h2>Effects</h2>

<p>We can't all be magicians, but anyone can use Effects to bring a web page to life. Fades and tweens, we've got it all. Why not give it a whirl?</p>

<p><a href="http://js.sapo.pt/SAPO/Effects/doc.html">API Link</a></p>

<h2>Component</h2>

<p>Need a styled select input? How about autocomplete text fields? Does uploading files with AJAX give you nightmares? Contrary to what a certain Jedi Master may tell you LibSAPO.js does have many of the UI components you are looking for.</p>

<p><a href="http://js.sapo.pt/SAPO/Component/doc.html">API Link</a>
 </div>
