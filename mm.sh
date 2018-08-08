#!/bin/bash

merge='git merge master'
checkout='git checkout'
branches=dev_{api,antaranews,detik,kompas,liputan6,tribunnews}
current_branch=$(git branch | grep "*" | cut -d "*" -f2 | cut -d " " -f2)

# Merge master branch to other branches.

$checkout master

bash -c "
for branch in $branches; do
	$checkout \$branch;
	$merge;
done;
"

$checkout $current_branch


