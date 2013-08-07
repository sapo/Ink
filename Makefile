# CSS output directory
CSS_OUTPUT_DIR = ./css

# LESS files directory
LESS_SOURCE_DIR = ./less

# LESS FILES
INK_LESS = ${LESS_SOURCE_DIR}/ink.less
INK_IE7_LESS = ${LESS_SOURCE_DIR}/ink-ie7.less
SITE_LESS = ${LESS_SOURCE_DIR}/docs.less

# CSS output files
INK_CSS = "${CSS_OUTPUT_DIR}/ink.css"
INK_IE7_CSS = "${CSS_OUTPUT_DIR}/ink-ie7.css"
SITE_CSS = "${CSS_OUTPUT_DIR}/docs.css"

# Minified CSS output files
INK_MIN_CSS = "${CSS_OUTPUT_DIR}/ink-min.css"
INK_IE7_MIN_CSS = "${CSS_OUTPUT_DIR}/ink-ie7-min.css"
SITE_MIN_CSS = "${CSS_OUTPUT_DIR}/ink-ltie9-min.css"

# Check mark character
CHECK = \033[32m✔\033[39m

# horizontal ruler
HR = \#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

all: ink minified site

test: 
	@echo "${HR}"
	@if [ ! -d "css" ]; then \
	echo "CSS dir does not exist. I'll create it.     ${CHECK} Done"; \
	echo "${HR}"; \
	mkdir css; fi

ink: test
	@echo " Compiling InK                             ${CHECK} Done"
	@lessc ${INK_LESS} > ${INK_CSS}
	@echo " Compiling InK IE7 exceptions              ${CHECK} Done"
	@lessc ${INK_IE7_LESS} > ${INK_IE7_CSS}
	@echo "${HR}"

site: test
	@echo " Compiling documentation specific css      ${CHECK} Done"
	@lessc ${SITE_LESS} > ${SITE_CSS}
	@echo "${HR}"

minified: test
	@echo "${HR}"
	@echo " Compiling minified InK                    ${CHECK} Done"
	@lessc ${INK_LESS} --yui-compress > ${INK_MIN_CSS}
	@echo " Compiling minified InK IE7 exceptions     ${CHECK} Done"
	@lessc ${INK_IE7_LESS} --yui-compress > ${INK_IE7_MIN_CSS}
	@echo "${HR}"
