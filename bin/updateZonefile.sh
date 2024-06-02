#!/bin/bash

set -ex

cd /var/tools/zone

# Make directory if not existing
if [ ! -d "/var/tools/zone/file" ]; then
  mkdir /var/tools/zone/file
fi

# Download the Zonefile via API. This is a gziped file
wget --no-verbose https://zonefiles.io/a/ixgkco2gjgn94vl384ou/fulldata-gz/1/ -O /var/tools/zone/file/zonefile_full.gz

# Go to the correct directory
cd /var/tools/zone/file/

# Uncompress the .gz file
gunzip zonefile_full.gz

# ~35 min | Normalize the file so we only have mandatory fields and the IP address is a long and first field
/usr/bin/php /var/tools/webinsights_classifier/tools/normalize_zone.php /var/tools/zone/file/zonefile_full /var/tools/zone/file/zonefile_normalized.csv

# Remove the origin zonefile to not run into a disk problem
rm zonefile_full

# ~5 min | sort the file by IP so that AS request is much faster and can be cached
sort -o zonefile_sorted.csv -n zonefile_normalized.csv

# Remove the normalized file
rm zonefile_normalized.csv

# ~11 hours | Import data
/usr/bin/php /var/tools/webinsights_classifier/tools/zonefile_import.php /var/tools/zone/file/zonefile_sorted.csv

# Remove the sorted file
rm zonefile_sorted.csv

# Touch file to see when last update was
touch /var/tools/zone/lastUpdate
