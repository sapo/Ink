INK = ./css/ink.css
INK_LARGE = ./css/large.css
INK_MEDIUM = ./css/medium.css
INK_SMALL = ./css/small.css
INK_IE6 = ./css/ink-ie6.css
INK_IE7 = ./css/ink-ie7.css

INK_MIN = ./css/ink-min.css
INK_LARGE_MIN = ./css/large-min.css
INK_MEDIUM_MIN = ./css/medium-min.css
INK_SMALL_MIN = ./css/small-min.css
INK_IE6_MIN = ./css/ink-ie6-min.css
INK_IE7_MIN = ./css/ink-ie7-min.css

INK_LESS = ./less/ink.less
INK_LARGE_LESS = ./less/large.less
INK_MEDIUM_LESS = ./less/medium.less
INK_SMALL_LESS = ./less/small.less
INK_IE6_LESS = ./less/ink-ie6.less
INK_IE7_LESS = ./less/ink-ie7.less

CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

all: ink minified

ink:
	@recess ${INK_LESS} --compile > ${INK}
	@recess ${INK_LARGE_LESS} --compile > ${INK_LARGE}
	@recess ${INK_MEDIUM_LESS} --compile > ${INK_MEDIUM}
	@recess ${INK_SMALL_LESS} --compile > ${INK_SMALL}
	@recess ${INK_SMALL_IE6} --compile > ${INK_IE6}
	@recess ${INK_IE7_LESS} --compile > ${INK_IE7}
	@echo "${HR}"
	@echo "Compiling InK CSS code...                   ${CHECK} Done"
	@echo "${HR}"


minified:
	@recess ${INK_LESS} --compile --compress > ${INK_MIN}
	@recess ${INK_LARGE_LESS} --compile --compress > ${INK_LARGE_MIN}
	@recess ${INK_MEDIUM_LESS} --compile --compress > ${INK_MEDIUM_MIN}
	@recess ${INK_SMALL_LESS} --compile --compress > ${INK_SMALL_MIN}
	@recess ${INK_SMALL_IE6} --compile --compress > ${INK_IE6_MIN}
	@recess ${INK_IE7_LESS} --compile --compress > ${INK_IE7_MIN}
	@echo "${HR}"
	@echo "Compiling minified InK CSS code...          ${CHECK} Done"
	@echo "${HR}"