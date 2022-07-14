#!/bin/bash
for f in *.{jpg,jpeg,JPG,PNG,png,gif}; do if [ ! -d ./${f:0:1}/${f:1:1} ]; then mkdir -p ./${f:0:1}/${f:1:1}; fi; mv $f ./${f:0:1}/${f:1:1}; done