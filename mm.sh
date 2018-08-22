#!/bin/bash

merge='git merge master'
checkout='git checkout'
branches={wordcloud,api,dev_{antaranews,detik,kompas,liputan6,tribunnews,suara,viva,kabardaerah,jpnn}}

# Merge master branch to other branches.

$checkout master

bash -c "
for branch in $branches; do
	$checkout \$branch;
	$merge;
done;
"

$checkout master
