#!/bin/bash

# This script is used to upload changed files to corresponding directory in the server. 
# Changed files are determined by 'git-diff' with different options.
#
# Note: This script requires kanssani configuration at ~/.ssh/config

ERROR="error.log"
SERVER="kanssani"
CUSTOM_PATH="public_html/store/wp-content/plugins/theme-customisations-master"
THEME_PATH="public_html/store/wp-content/themes/stationery"

# remove error log
rm -f $ERROR

# make sure at 'kanssani' branch 
current_branch=`git rev-parse --abbrev-ref HEAD`
if [[ $current_branch != "kanssani" ]]; then
    echo "Please swith to branch 'kanssani' to upload."
    exit 1
fi

# get changed files according to 'git-diff' option
if [[ $# -eq 0 ]]; then
    files=`git diff --name-only`
else
    case $1 in
        "cached")
            files=`git diff --cached --name-only`
            ;;
        "commit")
            files=`git diff --name-only HEAD~1 HEAD`
            ;;
        *)
            echo "Usage: sh $0 [cached|commit]"
            exit 1
    esac
fi

# exit if no changed files
if [[ -z $files ]]; then
    echo "Nothing needs to upload."
    exit 0
fi

# upload
for file in $files; do
    cmd=
    if [[ $file =~ ^custom/ ]]; then
        dir_name=$CUSTOM_PATH/`dirname $file`
        cmd="ssh $SERVER mkdir -p $dir_name && scp $file $SERVER:$dir_name"
    elif [[ $file =~ ^theme-template/ ]]; then
        cmd="scp $file $SERVER:$THEME_PATH"
    fi

    if [[ -n $cmd ]]; then
        sh -c "$cmd" 2>> $ERROR
        if [[ $? -ne 0 ]]; then
            echo "$file... Err."
        else
            echo "$file... OK."
        fi
    else
        echo "$file... No."
    fi
done

if [[ -f $ERROR ]]; then
    echo ""
    echo "Read $ERROR to see more details."
fi


