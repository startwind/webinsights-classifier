#!/bin/bash

while true
do
    /usr/bin/php /var/tools/classifier/bin/classifier.php  aggregate-pop
    sleep 1  # Optional: Füge eine kurze Verzögerung ein, um die CPU-Auslastung zu reduzieren
done
