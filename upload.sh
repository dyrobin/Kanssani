#!/bin/bash

# This script is used to upload changed files to corresponding directory in the server. 
# Changed files are determined by 'git-diff' with different modes.
#
# If changed file has ever been uploaded and there is no modification since then,
# it will be ignored for next uploading. The timestamp of uploading is stored as
# .timestamp file and if it is missing, all changed files will be uploaded.
# 
# Note: This script requires kanssani configuration at ~/.ssh/config

SCRIPT_PATH="$(cd $(dirname $0) && pwd)"
TIMESTAMP="$SCRIPT_PATH/.timestamp"
ERROR_LOG="error.log"

SERVER="kanssani"
THEME="stationery"
CUSTOM_PATH="public_html/store/wp-content/plugins/theme-customisations-master"
THEME_PATH="public_html/store/wp-content/themes/$THEME"

SCP="scp -q"

# remove error log
rm -f "$ERROR_LOG"

# make sure at 'kanssani' branch 
current_branch=$(git rev-parse --abbrev-ref HEAD)
if [[ "$current_branch" != "kanssani" ]]; then
    echo "Please swith to branch 'kanssani' to upload."
    exit 1
fi

# get options
while getopts ":dm:" opt; do
    case "$opt" in
        d)
            dry_run=1
            ;;
        m)
            mode="$OPTARG"
            ;;
        *)
            echo "Usage: sh $0 [-d] [-m index|cached|commit]"
            exit 1
    esac
done

if [[ -z "$mode" ]]; then
    mode="index"
fi

# get changed files according to mode
case "$mode" in
    index)
        files=$(git diff --name-only)
        ;;
    cached)
        files=$(git diff --cached --name-only)
        ;;
    commit)
        files=$(git diff --name-only HEAD~1 HEAD)
        ;;
    *)
        echo "Usage: sh $0 [-d] [-m cached|commit]"
        exit 1
esac

# upload
for file in $files; do
    if [[ "$file" =~ ^theme-template/ ]]; then
        remote_dir="$THEME_PATH/$(dirname ${file#theme-template/})"
    elif [[ "$file" =~ ^custom/ ]] || [[ $file == "theme-customisations.php" ]]; then
        remote_dir="$CUSTOM_PATH/$(dirname $file)"
    else
        continue
    fi

    # only upload newer/un-uploaded file
    if [[ "$file" -nt "$TIMESTAMP" ]]; then
        printf "$file... "
        cmd="ssh $SERVER mkdir -p $remote_dir && $SCP $file $SERVER:$remote_dir"

        if [[ "$dry_run" -eq 1 ]]; then
            echo ""
            echo "$cmd"
        else
            sh -c "$cmd" 1>/dev/null 2>>"$ERROR_LOG"
            if [[ $? -ne 0 ]]; then
                echo "Err."
                error=1
            else
                echo "OK."
            fi
        fi
    fi
done

# exit if no changed files
if [[ -z "$cmd" ]]; then
    echo "Nothing needs to upload."
    exit 0
fi

# update timestamp if no errors
if [[ -z "$error" ]] && [[ -z "$dry_run" ]]; then
    touch "$TIMESTAMP"
elif [[ -f "$ERROR_LOG" ]]; then
    echo ""
    echo "Read $ERROR_LOG to see more details."
fi
