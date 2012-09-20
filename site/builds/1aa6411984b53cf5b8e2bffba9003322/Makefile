INK_LESS = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/1aa6411984b53cf5b8e2bffba9003322.less
INK_LARGE_LESS = /home/pedro/work/ink/site/builds/latest/less/large.less
INK_MEDIUM_LESS = /home/pedro/work/ink/site/builds/latest/less/medium.less
INK_SMALL_LESS = /home/pedro/work/ink/site/builds/latest/less/small.less
INK_IE6_LESS = /home/pedro/work/ink/site/builds/latest/less/ie6.less
INK_IE7_LESS = /home/pedro/work/ink/site/builds/latest/less/ie7.less

INK = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/ink.css
INK_LARGE = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/large.css
INK_MEDIUM = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/medium.css
INK_SMALL = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/small.css
INK_IE6 = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/ink-ie6.css
INK_IE7 = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/ink-ie7.css

INK_MIN = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/ink-min.css
INK_LARGE_MIN = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/large-min.css
INK_MEDIUM_MIN = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/medium-min.css
INK_SMALL_MIN = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/small-min.css
INK_IE6_MIN = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/ink-ie6-min.css
INK_IE7_MIN = /home/pedro/work/ink/site/builds/1aa6411984b53cf5b8e2bffba9003322/ink/assets/css/ink-ie7-min.css


DATE=$(shell date +%I:%M%p)
CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

all:
	@recess ${INK_LESS} --compile > ${INK}
	@recess ${INK_LESS} --compile --compress > ${INK_MIN}
	@recess ${INK_LARGE_LESS} --compile > ${INK_LARGE}
	@recess ${INK_LARGE_LESS} --compile --compress > ${INK_LARGE_MIN}
	@recess ${INK_MEDIUM_LESS} --compile > ${INK_MEDIUM}
	@recess ${INK_MEDIUM_LESS} --compile --compress > ${INK_MEDIUM_MIN}
	@recess ${INK_SMALL_LESS} --compile > ${INK_SMALL}
	@recess ${INK_SMALL_LESS} --compile --compress > ${INK_SMALL_MIN}
	@recess ${INK_IE6_LESS} --compile > ${INK_IE6}
	@recess ${INK_IE6_LESS} --compile --compress > ${INK_IE6_MIN}
	@recess ${INK_IE7_LESS} --compile > ${INK_IE7}
	@recess ${INK_IE7_LESS} --compile --compress > ${INK_IE7_MIN}
