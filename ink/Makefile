INK_OUTPUT = ../site/tree/assets/css/

INK = "${INK_OUTPUT}ink.css"
INK_IE = "${INK_OUTPUT}ink-ie.css"

INK_MIN = "${INK_OUTPUT}ink-min.css"
INK_IE_MIN = "${INK_OUTPUT}ink-ie-min.css"

INK_LESS = ./less/ink.less
INK_IE_LESS = ./less/ink-ie.less

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
	@echo " Compiling InK                             ${CHECK} Done"
	@recess ${INK_LESS} --compile > ${INK}
	@echo " Compiling InK IE exceptions               ${CHECK} Done"
	@recess ${INK_IE_LESS} --compile > ${INK_IE}
	@echo "${HR}"


minified: test
	@echo "${HR}"
	@echo " Compiling minified InK                    ${CHECK} Done"
	@recess ${INK_LESS} --compile --compress > ${INK_MIN}
	@echo " Compiling minified InK IE exceptions      ${CHECK} Done"
	@recess ${INK_IE6_LESS} --compile --compress > ${INK_IE_MIN}
	@echo "${HR}"