#!/bin/bash

merge='git merge '
checkout='git checkout'
branches={wordcloud,api,datetime_parser,dev_{ajnn,antaranews,banteninfo,detik,goaceh,gonews,indonesiatimur,jpnn,kabardaerah,kabarmedan,kabarsumut,kompas,liputan6,medansatu,modusaceh,portalsatu,prohaba,sindonews,suara,sumutpos,tribunnews,viva}}

# Merge other branches to master branch

bash -c "
for branch in $branches; do
	$checkout master;
	$merge \$branch --progress --verbose;
done;
"

$checkout master
