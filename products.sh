#!/bin/bash

if [ $# -lt 1 ];
then
    echo Invalid parameters, requires two text files
    exit 1
fi

# Download tagsoup if not in directory
if ! [ -f tagsoup-1.2.1.jar ];
then
    wget https://repo1.maven.org/maven2/org/ccil/cowan/tagsoup/tagsoup/1.2.1/tagsoup-1.2.1.jar
fi

while true
do
    echo Running
    for vendor in $1 $2
    do
        count=1
        vendor=${vendor::-4}
        mkdir -p $vendor
        echo Made directory
        for item in $(cat $vendor.txt)
        do
            echo Downloading $item
            curl -s $item > $vendor/$count.html

            echo TagSouping
            java -jar tagsoup-1.2.1.jar --files $vendor/$count.html

            echo Getting info from file
            python3 parser.py $vendor/$count.xhtml $vendor

            count=`expr $count + 1`
        done
        rm -r $vendor
        echo Removed directory
    done
    sleep 21600
    echo ""
done

echo Done
exit 0
