#!/bin/bash

comment=$1
if [ -z "$comment" ];then
  echo "Please input comment with ./commit.sh test";

else
 echo "git comment $comment & upload";
 git add  .
 git commit -m "$1"
 git push

fi
