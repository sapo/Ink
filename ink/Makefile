INK_OUTPUT = ../site/tree/assets/css/

INK = "${INK_OUTPUT}ink.css"
INK_IE6 = "${INK_OUTPUT}ink-ie6.css"
INK_IE7 = "${INK_OUTPUT}ink-ie7.css"

INK_MIN = "${INK_OUTPUT}ink-min.css"
INK_IE6_MIN = "${INK_OUTPUT}ink-ie6-min.css"
INK_IE7_MIN = "${INK_OUTPUT}ink-ie7-min.css"


INK_LESS = ./less/ink.less
INK_IE6_LESS = ./less/ink-ie6.less
INK_IE7_LESS = ./less/ink-ie7.less

CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

all: ink minified

test: 
	@echo "${HR}" 
	@if [ ! -d "css" ]; then \
	echo "CSS dir does not exist. I'll create it.     ${CHECK} Done"; \
	echo "${HR}"; \
	mkdir css; fi

ink: test
	@recess ${INK_LESS} --compile > ${INK}
	@echo "Compiling InK                               ${CHECK} Done"
	@echo "Compiling InK ie6 exceptions                ${CHECK} Done"
	@echo "Compiling InK ie7 exceptions                ${CHECK} Done"
	@recess ${INK_SMALL_IE6} --compile > ${INK_IE6}
	@recess ${INK_IE7_LESS} --compile > ${INK_IE7}
	@echo "${HR}"


minified: test
	@echo "${HR}"
	@recess ${INK_LESS} --compile --compress > ${INK_MIN}
	@echo "Compiling minified InK                      ${CHECK} Done"
	@echo "Compiling minified InK ie6 exceptions       ${CHECK} Done"
	@echo "Compiling minified InK ie7 exceptions       ${CHECK} Done"
	@recess ${INK_IE6_LESS} --compile --compress > ${INK_IE6_MIN}
	@recess ${INK_IE7_LESS} --compile --compress > ${INK_IE7_MIN}
	@echo "${HR}"