#!/bin/bash
if [ "$#" -ne  "2" ]
  then
    echo "Please supply exactly two arguments, not $#"
    echo ""
    echo "Usage:"
    echo ""
    echo "Mirrors this git repo from one location to another."
    echo "First make sure you have access to the source and destinations via ssh key."
    echo "Then call, periodically from continuous integration server:"
    echo ""
    echo "./scripts/mirror.sh git@example.com/source/repo git@example.com/destination/repo"
else
  git clone --mirror $1 _project_
  cd _project_
  git push --mirror $2
  cd ..
  rm -rf _project_
fi
