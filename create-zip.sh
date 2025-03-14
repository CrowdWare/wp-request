#!/bin/bash

# Create a temporary directory for the plugin
mkdir -p wp-request

# Copy all plugin files to the temporary directory
cp -r wp-request.php templates assets wp-request/

# Extract version number from the main plugin file
version=$(grep "Version:" wp-request.php | awk -F ': ' '{print $2}')

# Remove the duplicate extraction line

# Debugging output
echo "Extracted version: $version"

# Debugging output for the zip file name
zip_file_name="wp-request-v$version.zip"
echo "Creating zip file: $zip_file_name"

# Create the zip file with version number
zip -r "$zip_file_name" wp-request

# Remove the temporary directory
rm -rf wp-request

echo "wp-request.zip has been created successfully."
