#!/bin/bash

echo "Lines of Code:"
sloc -k total,source,comment,files ./
