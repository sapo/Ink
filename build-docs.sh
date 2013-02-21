#!/bin/bash

PAGES=(home/index layout/index navigation/index typography/index icons/index forms/index alerts/index tables/index js/ui js/core about/index changelog/index)
URL='http://ink.local/'
LOCAL_PATH='./documentation/'
DUMP_TRIGGER='_dump'
SUFFIX='.html'

for PAGE in ${PAGES[@]}
do

    if [[ ${PAGE} = "home/index" ]]; 
    then
        FILENAME='index'
    elif [[ ${PAGE} == js* ]]
    then
        FILENAME=${PAGE/\//-}
    else
        FILENAME=${PAGE%/*}
        # FILENAME=${PAGE}
    fi
    printf "curl $URL${PAGE}$DUMP_TRIGGER > $LOCAL_PATH$FILENAME$SUFFIX\n"
    curl $URL${PAGE}$DUMP_TRIGGER > $LOCAL_PATH$FILENAME$SUFFIX
done