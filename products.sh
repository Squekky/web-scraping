#!/bin/bash

if [ $# -lt 1 ]
then
    echo Invalid parameters, requires two text files
    exit 1
fi

while true
do
    echo Running
    for vendor in $1 $2
    do
        vendor=${vendor::-4}
        mkdir -p $vendor
        echo Made Directories
        count=1
        for item in $(cat $vendor.txt)
        do
            echo Downloading $item
            curl -s $item > $vendor/$count.html
            echo TagSouping
            java -jar tagsoup-1.2.1.jar --files $vendor/$count.html
            echo Deleting original HTML file
            rm $vendor/$count.html
            echo Getting info from file
            python3 parser.py $vendor/$count.xhtml $vendor
            count=`expr $count + 1`
        done
        rm -r $vendor
    done
    sleep 21600
    echo ""
done

echo Done
exit 0