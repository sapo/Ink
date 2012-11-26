# CSS output directory
INK_OUTPUT = ./css/

# LESS files directory
LESS_DIR = ./less/

# LESS FILES
INK_LESS = ${LESS_DIR}/ink.less
INK_IE_LESS = ${LESS_DIR}/ink-ie.less
INK_LTIE9_LESS = ${LESS_DIR}/ink-ltie9.less

# CSS output files
INK = "${INK_OUTPUT}ink.css"
INK_IE = "${INK_OUTPUT}ink-ie.css"
INK_LTIE9 = "${INK_OUTPUT}ink-ltie9.css"

# Minified CSS output files
INK_MIN = "${INK_OUTPUT}ink-min.css"
INK_IE_MIN = "${INK_OUTPUT}ink-ie-min.css"
INK_LTIE9_MIN = "${INK_OUTPUT}ink-ltie9-min.css"

# Check mark character
CHECK = \033[32m✔\033[39m

# horizontal ruler
HR = \#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

# Background recess PID file for the watch target
PID_FILE = ./recess.pid

# Background recess process(es) PID(s)
PID = `cat $(PID_FILE)`


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
	@recess ${INK_LTIE9_LESS} --compile > ${INK_LTIE9}
	@echo "${HR}"


minified: test
	@echo "${HR}"
	@echo " Compiling minified InK                    ${CHECK} Done"
	@recess ${INK_LESS} --compile --compress > ${INK_MIN}
	@echo " Compiling minified InK IE exceptions      ${CHECK} Done"
	@recess ${INK_IE_LESS} --compile --compress > ${INK_IE_MIN}
	@recess ${INK_LTIE9_LESS} --compile --compress > ${INK_LTIE9_MIN}
	@echo "${HR}"

watch: test
	@echo "${HR}"
	@echo " Watching ${LESS_DIR} for changes               ${CHECK} Done"
	@echo " Use: \"make stop\" to stop watching ${LESS_DIR}"
	@recess ${INK_LESS}:${INK} --watch ${LESS_DIR} & echo "$$!" > ${PID_FILE}
	@echo "${HR}"

stop:
	@echo "${HR}"
	@kill -9 ${PID}
	@rm ${PID_FILE}
	@echo " Stopped Watching ${LESS_DIR} for changes       ${CHECK} Done"
	@echo "${HR}"