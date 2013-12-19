VERSION = "`cat VERSION`"



help:
	@echo Relevant tasks are:
	@echo "    all"
	@echo "    clean"
	@echo "    report"
	@echo "    docs"
	@echo "    showDocs"
	@echo "    min"
	@echo "    bundle"
	@echo "    bundleMin"
	@echo "    importPreCommitHook"
	@echo "    extractTree"


all: extractTree docs min bundle bundleMin updateJsFolder


report: extractTree deleteMinFiles
	@echo "\ngenerating report..."
	@mkdir -p report
	@plato -r -l .jshintrc -d report Ink

readReport: report
	@google-chrome report/index.html

clean: deleteMinFiles removeDirs
	@echo "clean."


.PHONY: docs report deleteMinFiles removeDirs


extractTree:
	@echo "\nextracting tree..."
	@node serverUtils/extractTree.js

builddir:
	@mkdir -p builds

extractTestsTree:
	@echo "\nextracting tests tree..."
	@node serverUtils/extractTestsTree.js


deleteTestResults:
	@echo "\ndeleting previous test results..."
	@node serverUtils/deleteTestResults.js


runTests: extractTestsTree deleteTestResults
	@echo "\nrunning tests..."
	@node serverUtils/runTests.js


docs:
	@echo "\ngenerating documentation..."
	@yuidoc -c .yuidoc.json -T default -q ./Ink
	@cp .yuidoclogo.png docs/yuidoclogo.png


liveDocs:
	@echo "\nstarting yuidoc server on http://localhost:4567/ ..."
	@yuidoc --server 4567 -c .yuidoc.json -T default -q ./Ink


showDocs: docs
	@google-chrome docs/index.html


min: extractTree
	@echo "\nminifying code..."
	@node serverUtils/minFiles.js


bundle: builddir extractTree
	@echo "\nbundling..."
	@node serverUtils/bundle.js ./builds/ink-all-$(VERSION).js
	@node serverUtils/bundle.js ./builds/ink-ui-$(VERSION).js "/UI/"
	@node serverUtils/bundle.js ./builds/ink-$(VERSION).js    "/UI/" negates


bundleMin: builddir min
	@echo "\nbundling minified..."
	@node serverUtils/bundle.js ./builds/ink-all-$(VERSION).min.js minify
	@node serverUtils/bundle.js ./builds/ink-ui-$(VERSION).min.js  minify "/UI/"
	@node serverUtils/bundle.js ./builds/ink-$(VERSION).min.js     minify "/UI/" negates


updateUi:
	@echo "Updating UI modules"
	@node serverUtils/updateUi.js ./Ink/UI ../../dist/js/


updateJsFolder: bundle bundleMin updateUi
	@echo "Moving built modules to js/ folder"
	@mv ./builds/ink-all-$(VERSION).js ../../dist/js/ink-all.js
	@mv ./builds/ink-ui-$(VERSION).js ../../dist/js/ink-ui.js
	@mv ./builds/ink-$(VERSION).js ../../dist/js/ink.js
	@mv ./builds/ink-all-$(VERSION).min.js ../../dist/js/ink-all.min.js
	@mv ./builds/ink-ui-$(VERSION).min.js ../../dist/js/ink-ui.min.js
	@mv ./builds/ink-$(VERSION).min.js ../../dist/js/ink.min.js


deleteMinFiles:
	@echo "\nremoving minified files..."
	@node serverUtils/deleteMinFiles.js


removeDirs:
	@echo "\nremoving docs and reports directories..."
	@rm -rf docs
	@rm -rf report


importPreCommitHook:
	@echo "\ncreating symbolic link of pre-commit.sh to your git internals"
	@ln -s ../../pre-commit.sh .git/hooks/pre-commit
