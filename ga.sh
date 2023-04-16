#!/bin/bash

DB=ga.db
SCHEMA=ga.schema

if [[ -f $DB ]]

then

  echo "The file $DB exists. Exiting..."

else

  echo "Creating database file $DB."
  sqlite3 $DB < $SCHEMA

  tail -n +2 ./new_users.csv > ./new_users_tmp.csv
  echo "importing new_users table"
  (echo .separator ,; echo .import ./new_users_tmp.csv new_users) | sqlite3 $DB

  tail -n +2 ./users_country.csv > ./users_country_tmp.csv
  echo "importing users_country table"
  (echo .separator ,; echo .import ./users_country_tmp.csv users_country) | sqlite3 $DB

  tail -n +2 ./followers.csv > ./followers_tmp.csv
  echo "importing followers table"
  (echo .separator ,; echo .import ./followers_tmp.csv followers) | sqlite3 $DB

  tail -n +2 ./event_type.csv > ./event_type_tmp.csv
  echo "importing event_type table"
  (echo .separator ,; echo .import ./event_type_tmp.csv event_type) | sqlite3 $DB

  tail -n +2 ./event.csv > ./event_tmp.csv
  echo "importing event table"
  (echo .separator ,; echo .import ./event_tmp.csv event) | sqlite3 $DB

  tail -n +2 ./twitter-impressions.csv > ./twitter-impressions_tmp.csv
  echo "importing twitter table"
  (echo .separator ,; echo .import ./twitter-impressions_tmp.csv twitter) | sqlite3 $DB

  tail -n +2 ./page_views.csv > ./page_views_tmp.csv
  echo "importing page_views table"
  (echo .separator ,; echo .import ./page_views_tmp.csv page_views) | sqlite3 $DB

  # remove temp files
  rm ./new_users_tmp.csv
  rm ./users_country_tmp.csv
  rm ./followers_tmp.csv
  rm ./event_type_tmp.csv
  rm ./event_tmp.csv
  rm ./twitter-impressions_tmp.csv
  rm ./page_views_tmp.csv

fi

