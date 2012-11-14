<div class="whatIs" id="nav-home">
   <div class="ink-container">
        <h2>Ink JS Core</h2>
        <p>The engine that drives the machine.</p>
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
                <li><a class="scrollableLink" href="#nav-sapo">SAPO</a></li>
                <li><a class="scrollableLink" href="#nav-dom">Dom</a></li>
                <li><a class="scrollableLink" href="#nav-communication">Communication</a></li>
                <li><a class="scrollableLink" href="#nav-utility">Utility</a></li>
                <li><a class="scrollableLink" href="#nav-effects">Effects</a></li>
                <li><a class="scrollableLink" href="#nav-component">Component</a></li>
            </ul>
        </nav>
    </div>
</div>

<div class="ink-container">
 <div class="ink-section">
<p>Ink was built from the ground up to be easy to use and easy on the eyes, but some of the bricks were borrowed from one of our other premiere web frameworks, LibSAPO.js. Listed here are a few key classes and methods that you can take advantage of when building your applications.</p>

<p>When including the <a href="http://js.sapo.pt/Bundles/Ink-v1.js">Ink.js</a> script you are including several classes from LibSAPO.js, namely:</p>
<ul>
    <li><a href="http://js.sapo.pt/SAPO/doc.html">SAPO</a></li>
    <li><a href="http://js.sapo.pt/SAPO/Dom/Css/doc.html">SAPO.Dom.Css</a>,
    <a href="http://js.sapo.pt/SAPO/Dom/Element/doc.html">SAPO.Dom.Element</a>,
    <a href="http://js.sapo.pt/SAPO/Dom/Event/doc.html">SAPO.Dom.Event</a>,
    <a href="http://js.sapo.pt/SAPO/Dom/Loaded/doc.html">SAPO.Dom.Loaded</a>,
    <a href="http://js.sapo.pt/SAPO/Dom/Selector/doc.html">SAPO.Dom.Selector</a></li>
    <li><a href="http://js.sapo.pt/SAPO/Utility/Array/doc.html">SAPO.Utility.Array</a>,
    <a href="http://js.sapo.pt/SAPO/Utility/Validator/doc.html">SAPO.Utility.FormValidator</a>,
    <a href="http://js.sapo.pt/SAPO/Utility/Swipe/doc.html">SAPO.Utility.Swipe</a>,
    <a href="http://js.sapo.pt/SAPO/Utility/Url/doc.html">SAPO.Utility.Url</a></li>
    <li><a href="http://js.sapo.pt/SAPO/Communication/Ajax/doc.html">SAPO.Communication.Ajax</a></li>
</ul>


</div>
<div class="ink-section" id="nav-sapo">

<h2>SAPO</h2>

<p>This is the core object which all other classes from LibSAPO.js will be placed.</p>

<p><em>require</em> - Load only the JavaScript that you need when you want. You can mix and match any JavaScript available on the web, but be careful, and learn from Dr. Frankenstein's mistake, lest you create a monster!</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    SAPO.require(
        // Load some LibSAPO.js utility classes.
        [
            'http://js.sapo.pt/SAPO/Utility/String/0.1/',
            'http://js.sapo.pt/SAPO/Utility/Cache/0.1/'
        ],

        // This function is executed after the code has been loaded
        function(){
            // Do cool stuffs here.
        }
    );
&lt;/script&gt;
</pre>

<p><em>Browser</em> - Is the user on Internet Explorer? What version? What model? Browser detection should be used sparingly, but when you need it, we've got you covered.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    // Is the user on IE?
    if(SAPO.Browser.IE){
        // If so, do strange stuffs here.
    }
&lt;/script&gt;
</pre>

<p><em>namespace</em> - Sometimes it's convenient to extend the framework to fit your needs, maybe you want to add a new component, or maybe you want to create a new collection of classes. SAPO.namespace will return the object you asked for or create a new one if none was found.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    // Create namespace if it does not exist.
    SAPO.namespace('Component');

    // Let's add something new to our namespace.
    SAPO.Component.SUIT = function(friendName){
        this.friend = friendName;
    };

    SAPO.Component.SUIT.prototype.up = function(){
        setTimeout(console.log.bindObj(console, 'dary!'), 500);

        console.log(this.friend + ", it\'s going to be Legen... wait for it...");
    }

    var theSuit = new SAPO.Component.SUIT('Ted');
&lt;/script&gt;
</pre>

<p><em>bindObj and bindObjEvent</em> - Binding functions is an important concept of JavaScript but not all browsers support it out of the box, but don't worry, LibSAPO.js gives you the tools you need to get the job done.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    SAPO.Dom.Event.observe(document.body, 'load', function(e){
        // Do some awesome event stuffs here.
    }.bindObjEvent(theSuit));

    setTimeout(function(){
        // Do some interesting async stuffs here.
    }.bindObj(theSuit), 500);
&lt;/script&gt;
</pre>

<p><a href="http://js.sapo.pt/SAPO/doc.html">API Link</a></p>

</div>

<div class="ink-section" id="nav-dom">

<h2>Dom</h2>

<p>The DOM is a mess, you know it and we do too, that's why we've added specialized Dom methods to LibSAPO.js, it makes your life easier and that makes us happy.</p>

<p><em><a href="http://js.sapo.pt/SAPO/Dom/Css/doc.html">Css</a></em> - Give your document some style with our Css class.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Css/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    if(!SAPO.Dom.Css.hasClassName(myElement, 'selected')){
        SAPO.Dom.Css.addClassName(myElement, 'selected');
    }

    if(SAPO.Dom.Css.hasClassName(myElement, 'aria-hidden')){
        SAPO.Dom.Css.removeClassName(myElement, 'aria-hidden');
    }

    SAPO.Dom.Css.setStyle(myElement, {"margin": 'auto'});
&lt;/script&gt;
</pre>

<p><em><a href="http://js.sapo.pt/SAPO/Dom/Element/doc.html">Element</a></em> - Not all browsers were created equal, and the DOM is where it shows the most. Get the document just the way you want it with Element.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Element/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    var divalicious = SAPO.Dom.Element.create('div', {
        className: 'some-class',
        innerHTML: '&lt;a href="http://www.sapo.pt"&gt;SAPO&lt;/a&gt;'
    });

    SAPO.Dom.Element.insertAfter(divalicious, myElement);
&lt;/script&gt;
</pre>

<p><em><a href="http://js.sapo.pt/SAPO/Dom/Event/doc.html">Event</a></em> - Events are easy with LibSAPO.js, whether you're catching clicks or sniffing keys, get your events under control with LibSAPO.js.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Event/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    SAPO.Dom.Event.observe(myElement, 'click', function(e){
        var target = SAPO.Dom.Event.element(e);

        SAPO.Dom.Event.stop(e);

        console.log('The element we clicked was: ', target);
    }.bindObjEvent(thSuit));
&lt;/script&gt;
</pre>

<p><em><a href="http://js.sapo.pt/SAPO/Dom/Selector/doc.html">Selector</a></em> - If you've ever worked with CSS then you know how to use our Selector engine, we've enlisted the help of Sizzle to bring you a simple and fast way to get the elements you need.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Selector/1.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    var elements = SAPO.Dom.Selector.select('p.my_class_name');
    var filteredElements = SAPO.Dom.Selector.match('p &gt; ul', elements);
&lt;/script&gt;
</pre>

<p><em><a href="http://js.sapo.pt/SAPO/Dom/Loaded/doc.html">Loaded</a></em> - Whether you're executing code when the document is ready or loaded we've got the tools you need to get your code running when you want.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Loaded/1.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    SAPO.Dom.Loaded.run(function(){
        // Your document is now ready for awesome
    }.bindObj(theSuit));
&lt;/script&gt;
</pre>

<p><a href="http://js.sapo.pt/SAPO/Dom/doc.html">API Link</a></p>

</div>

<div class="ink-section" id="nav-communication">

<h2>Communication</h2>

<p>LibSAPO.js offers you plenty of ways to get your web site communicating with the vast collection of public services available on the web. CORS enabled AJAX, JsonP and Syndication will get you up and running in no time.</p>

<p><em><a href="http://js.sapo.pt/SAPO/Communication/Ajax/doc.html">Ajax</a></em> - The same AJAX you know and love, now with some extra CORS magic to spice up your cross request filled life.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/Communication/Ajax/2.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    new SAPO.Communication.Ajax(
        '/post/data',

        // Configure the request.
        {
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
&lt;/script&gt;
</pre>

<p><em><a href="http://js.sapo.pt/SAPO/Communication/JsonP/doc.html">JsonP</a></em> - Break out of your local domain chains and enjoy the freedom of cross domain requests, JsonP give you the tools you need to start getting data from any source you want.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/Communication/JsonP/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    new SAPO.Communication.JsonP(
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
&lt;/script&gt;
</pre>

<p><em><a href="http://js.sapo.pt/SAPO/Communication/Syndication/doc.html">Syndication</a></em> - There are plenty of ways to do cross domain requests, and we do them all right, Syndication is another prime example of that fact.</p>

<pre class="prettyprint linenums">&lt;script type="text/javascript" src="http://js.sapo.pt/SAPO/Communication/Syndication/0.1/"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
    new SAPO.Communication.Syndication(
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
&lt;/script&gt;
</pre>

<p><a href="http://js.sapo.pt/SAPO/Communication/doc.html">API Link</a></p>

</div>

<div class="ink-section" id="nav-utility">

<h2>Utility</h2>

<p>Even certain bat suit wearing ninjas need to use utilities every one in a while, and what are JavaScript developers if not ninjas without the bat suit? LibSAPO.js offers those who possess the script-fu ways to take their skills to the next level.</p>

<p><a href="http://js.sapo.pt/SAPO/Utility/doc.html">API Link</a></p>

</div>

<div class="ink-section" id="nav-effects">

<h2>Effects</h2>

<p>We can't all be magicians, but anyone can use Effects to bring a web page to life. Fades and tweens, we've got it all. Why not give it a whirl?</p>

<p><a href="http://js.sapo.pt/SAPO/Effects/doc.html">API Link</a></p>

</div>

<div class="ink-section" id="nav-component">

<h2>Component</h2>

<p>Need a styled select input? How about autocomplete text fields? Does uploading files with AJAX give you nightmares? Contrary to what a certain Jedi Master may tell you LibSAPO.js does have many of the UI components you are looking for.</p>

<p><a href="http://js.sapo.pt/SAPO/Component/doc.html">API Link</a></p>

</div>
 </div>
