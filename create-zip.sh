#!/bin/bash

# Create a temporary directory for the plugin
mkdir -p wp-request

# Copy all plugin files to the temporary directory
cp -r wp-request.php templates assets wp-request/

# Create the zip file
zip -r wp-request.zip wp-request

# Remove the temporary directory
rm -rf wp-request

echo "wp-request.zip has been created successfully."
