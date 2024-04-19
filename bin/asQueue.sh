#!/bin/bash

while true
do
    /usr/bin/php /var/tools/webinsights_classifier/bin/classifier.php hosting:as:iprange
    sleep 1  # Optional: Füge eine kurze Verzögerung ein, um die CPU-Auslastung zu reduzieren
done
