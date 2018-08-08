#!/bin/sh

git checkout master

merge='git merge master'

git checkout api
$merge

git checkout dev_detik
$merge

git checkout dev_kompas
$merge

git checkout dev_liputan6
$merge

git checkout dev_tribunnews
$merge

git checkout master
