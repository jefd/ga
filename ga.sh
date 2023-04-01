#!/bin/bash

DB=$1

if [[ -f $DB ]]

then

  echo "The file $DB exists."

else

  echo "The file $DB cannot be found."
  sqlite3 $DB < ga.schema
  (echo .separator ,; echo .import ./followers.csv followers) | sqlite3 $DB

fi

# (echo .separator ,; echo .import ./followers.csv followers) | sqlite3 t.db

# tail -n +2 file.txt > file2.txt
