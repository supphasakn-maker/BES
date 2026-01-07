#!/bin/bash

comment=$1
if [ -z "$comment" ];then
  echo "Please input comment with ./commit.sh test";

else
 echo "git comment";
 git add  .
 git push
 git commit -m "$1"

fi
