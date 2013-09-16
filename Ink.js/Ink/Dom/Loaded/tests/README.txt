What's on this folder:

- QUnit tests: index.html
- visual test: iframes.html -- You should see iframes loading one at a time, and the word "loaded" appearing as the iframes load. In ALL browsers.
- php sleep() test: rawtest.php -- You should see a paragraph saying "Paragraph at the bottom of the page", and a paragraph below it indicating success or failure. This page has a serverside sleep() before all HTML content is sent over to the client, and a callback which tries to find an unavailable paragraph is called in Ink.Dom.Loaded.run..

Some of the QUnit tests do require PHP, and the visual test only works with PHP.
