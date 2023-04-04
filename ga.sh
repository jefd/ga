#!/bin/bash

DB=ga2.db
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

  tail -n +2 ./events.csv > ./events_tmp.csv
  echo "importing events table"
  (echo .separator ,; echo .import ./events_tmp.csv events) | sqlite3 $DB

  # remove temp files
  rm ./new_users_tmp.csv
  rm ./users_country_tmp.csv
  rm ./followers_tmp.csv
  rm ./events_tmp.csv

fi

