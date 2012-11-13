<div class="whatIs">
   <div class="ink-container">
        <h2>Ink JS Core</h2>
        <p>The engine that drives the machine.</p>
    </div>
</div>

<p><div class="ink-container">
Ink was built from the ground up to be easy to use and easy on the eyes, but some of the bricks were borrowed from one of our other premiere web frameworks, LibSAPO.js. Listed here are a few key classes and methods that you can take advantage of when building your applications.</p>

<p>When including the <a href="http://js.sapo.pt/Bundles/Ink.js">Ink.js</a> script you are including several classes from LibSAPO.js, namely:</p>

<ul>
<li><a href="http://js.sapo.pt/SAPO/0.1/">http://js.sapo.pt/SAPO/0.1/</a></li>
<li><a href="http://js.sapo.pt/SAPO/Dom/Css/0.1/">http://js.sapo.pt/SAPO/Dom/Css/0.1/</a></li>
<li><a href="http://js.sapo.pt/SAPO/Dom/Loaded/1.1">http://js.sapo.pt/SAPO/Dom/Loaded/1.1</a>/></li>
<li><a href="http://js.sapo.pt/SAPO/Dom/Element/0.">http://js.sapo.pt/SAPO/Dom/Element/0.</a>1/></li>
<li><a href="http://js.sapo.pt/SAPO/Dom/Event/0.1/">http://js.sapo.pt/SAPO/Dom/Event/0.1/</a></li>
<li><a href="http://js.sapo.pt/SAPO/Dom/Selector/1.1">http://js.sapo.pt/SAPO/Dom/Selector/1.1</a>/></li>
<li><a href="http://js.sapo.pt/SAPO/Utility/Array/0.">http://js.sapo.pt/SAPO/Utility/Array/0.</a>1/></li>
<li><a href="http://js.sapo.pt/SAPO/Utility/Swipe/0.">http://js.sapo.pt/SAPO/Utility/Swipe/0.</a>1/></li>
<li><a href="http://js.sapo.pt/SAPO/Utility/Url/1.1/">http://js.sapo.pt/SAPO/Utility/Url/1.1/</a></li>
<li><a href="http://js.sapo.pt/SAPO/Communication/Ajax/2.1/">http://js.sapo.pt/SAPO/Communication/Ajax/2.1/</a></li>
</ul>

<h2>SAPO</h2>

<p>This is the core object which all other classes from LibSAPO.js will be placed.</p>

<p><em>require</em> - Load only the JavaScript that you need when you want. You can mix and match any JavaScript available on the web, but be careful, and learn from Dr. Frankenstein's mistake, lest you create a monster!</p>

<pre class="prettyprint">SAPO.require(
    // Load some LibSAPO.js utility classes.
    [
        'http://js.sapo.pt/SAPO/Utility/String/0.1/',
        'http://js.sapo.pt/SAPO/Utility/Url/0.1/'
    ],

    // This function is executed after the code has been loaded
    function(){
        // Do cool stuffs here.
    }
);
</pre>

<p><em>browser</em> - Is the user on Internet Explorer? What version? What model? Browser detection should be used sparingly, but when you need it, we've got you covered.</p>

<pre class="prettyprint">// Is the user on IE?
if(SAPO.Browser.IE){
    // If so, do strange stuffs here.
}
</pre>

<p><em>namespace</em> - Sometimes it's convenient to extend the framework to fit your needs, maybe you want to add a new component, or maybe you want to create a new collection of classes. SAPO.namespace will return the object you asked for or create a new one if none was found.</p>

<pre class="prettyprint">// Create or fetch SAPO.Component.
SAPO.namespace('Component');

// Add some crazy method to our namespace.
SAPO.Component.SUIT = {
    up: function() {
        setTimeout(console.log.bindObj(console, 'dary!'), 500);
        console.log('Legen... wait for it...');
    }
}
</pre>

<p><em>bindObj and bindObjEvent</em> - Binding functions is an important concept of JavaScript but not all browsers support it out of the box, but don't worry, LibSAPO.js gives you the tools you need to get the job done.</p>

<pre class="prettyprint">SAPO.Dom.Event.observe(myElement, 'click', function(e){
    // Do some awesome event stuffs here.
}.bindObjEvent(SAPO.Component.SUIT));

setTimeout(function(){
    // Do some interesting async stuffs here.
}.bindObj(SAPO.Component.SUIT), 500);
</pre>

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

<p><em>Ajax</em> - The same AJAX you know and love, now with some extra CORS magic to spice up your cross request filled life.</p>

<pre class="prettyprint">new SAPO.Communication.Ajax(
    'http://www.cross-domain.com/post/data',

    // Configure the request.
    {
        // Activate the CORS mechanism.
        cors: true,

        // Pass some data to the query portion of the url
        parameters: {
            a: 'potato'
        },

        onSuccess: function(responseObject, responseData){
            // handle the success like a boss
        },

        onFailure: function(responseObject, responseData){
            // handle the failure gracefully
        }
    }
);
</pre>

<p><em>JsonP</em> - Break out of your local domain chains and enjoy the freedom of cross domain requests, JsonP give you the tools you need to start getting data from any source you want.</p>

<pre class="prettyprint">new SAPO.Communication.JsonP(
    'http://sub.local-domain.com/get/data',

    // Configure the request.
    {
        // Pass some data to the query portion of the url
        params: {
            a: 'potato'
        },

        onComplete: function(responseObject, responseData){
            // handle the completion like a boss
        },

        onFailure: function(responseObject, responseData){
            // handle the failure gracefully
        }
    }
);
</pre>

<p><em>Syndication</em> - There are plenty of ways to do cross domain requests, and we do them all right, Syndication is another prime example of that fact.</p>

<pre class="prettyprint">new SAPO.Communication.Syndication(
    'http://www.cross-domain.com/get/data',

    // Configure the request.
    {
        onComplete: function(responseObject, responseData){
            // handle the completion like a boss
        },

        onTimeout: function(responseObject, responseData){
            // handle the timeout like a sir
        }
    }
);
</pre>

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
 </div></p>
