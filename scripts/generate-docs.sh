#!/bin/bash

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
phpdoc -d $DIR/../php/lib -t $DIR/../docs