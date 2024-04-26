#!/bin/bash

while true
do
    /usr/bin/php /var/tools/webinsights_classifier/bin/classifier.php classifyMany -c https://api.webinsights.info/collection/job/pop
done
