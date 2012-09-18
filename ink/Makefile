INK = ./css/ink.css
INK_MIN = ./css/ink-min.css
INK_LESS = ./less/ink.less
INK_LARGE = ./css/large.css
INK_LARGE_MIN = ./css/large-min.css
INK_LARGE_LESS = ./less/large.less
INK_MEDIUM = ./css/medium.css
INK_MEDIUM_MIN = ./css/medium-min.css
INK_MEDIUM_LESS = ./less/medium.less
INK_SMALL = ./css/small.css
INK_SMALL_MIN = ./css/small-min.css
INK_SMALL_LESS = ./less/small.less

DATE=$(shell date +%I:%M%p)
CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

ink:
	@recess ${INK_LESS} --compile > ${INK}
	@recess ${INK_LARGE_LESS} --compile > ${INK_LARGE}
	@recess ${INK_MEDIUM_LESS} --compile > ${INK_MEDIUM}
	@recess ${INK_SMALL_LESS} --compile > ${INK_SMALL}	
	@echo "${HR}"
	@echo "Compiling InK...            ${CHECK} Done"
	@echo "${HR}"


minified:
	@recess ${INK_LESS} --compile --compress > ${INK_MIN}
	@recess ${INK_LARGE_LESS} --compile --compress > ${INK_LARGE_MIN}
	@recess ${INK_MEDIUM_LESS} --compile --compress > ${INK_MEDIUM_MIN}
	@recess ${INK_SMALL_LESS} --compile --compress > ${INK_SMALL_MIN}	
	@echo "${HR}"
	@echo "Compiling minified version of InK...            ${CHECK} Done"
	@echo "${HR}"