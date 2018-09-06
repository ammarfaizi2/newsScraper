#!/bin/bash

merge='git merge master'
checkout='git checkout'
branches={wordcloud,api,datetime_parser,dev_{ajnn,antaranews,banteninfo,detik,goaceh,gonews,indonesiatimur,jpnn,kabardaerah,kabarmedan,kabarsumut,kompas,liputan6,medansatu,modusaceh,portalsatu,prohaba,sindonews,suara,sumutpos,tribunnews,viva}}

# Merge master branch to other branches.

$checkout master

bash -c "
for branch in $branches; do
	$checkout \$branch;
	$merge;
done;
"

$checkout master
