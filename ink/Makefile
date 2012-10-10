INK = ./css/ink.css
# INK_LARGE = ./css/large.css
# INK_MEDIUM = ./css/medium.css
# INK_SMALL = ./css/small.css
INK_IE6 = ./css/ink-ie6.css
INK_IE7 = ./css/ink-ie7.css

INK_MIN = ./css/ink-min.css
# INK_LARGE_MIN = ./css/large-min.css
# INK_MEDIUM_MIN = ./css/medium-min.css
# INK_SMALL_MIN = ./css/small-min.css
INK_IE6_MIN = ./css/ink-ie6-min.css
INK_IE7_MIN = ./css/ink-ie7-min.css

INK_LESS = ./less/ink.less
# INK_LARGE_LESS = ./less/large.less
# INK_MEDIUM_LESS = ./less/medium.less
# INK_SMALL_LESS = ./less/small.less
INK_IE6_LESS = ./less/ink-ie6.less
INK_IE7_LESS = ./less/ink-ie7.less

CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

all: ink minified

ink:	
	@echo "${HR}" 
	@if [ ! -d "css" ]; then mkdir css; fi
	@recess ${INK_LESS} --compile > ${INK}
	@echo "Compiling InK                               ${CHECK} Done"
	@echo "Compiling InK ie6 exceptions                ${CHECK} Done"
	@echo "Compiling InK ie7 exceptions                ${CHECK} Done"
	@recess ${INK_SMALL_IE6} --compile > ${INK_IE6}
	@recess ${INK_IE7_LESS} --compile > ${INK_IE7}
	@echo "${HR}"


minified:
	@echo "${HR}"
	@recess ${INK_LESS} --compile --compress > ${INK_MIN}
	@echo "Compiling minified InK                      ${CHECK} Done"
	@echo "Compiling minified InK ie6 exceptions       ${CHECK} Done"
	@echo "Compiling minified InK ie7 exceptions       ${CHECK} Done"
	@recess ${INK_IE6_LESS} --compile --compress > ${INK_IE6_MIN}
	@recess ${INK_IE7_LESS} --compile --compress > ${INK_IE7_MIN}
	@echo "${HR}"