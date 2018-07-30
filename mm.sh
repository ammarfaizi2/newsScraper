#!/bin/sh

git checkout master

git checkout api
git merge master

git checkout dev_liputan6
git merge master

git checkout dev_tribunnews
git merge master

git checkout master
