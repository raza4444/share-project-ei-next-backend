#!/bin/bash
echo Hooking

d=$CERTBOT_DOMAIN
v=$CERTBOT_VALIDATION
t=$CERTBOT_TOKEN
file=/tmp/$t

echo "$d / $v / $t"

#creating directories
curl ftp://%ftpusername%:%ftppassword%@%ftphost%%ftpdirectoryhtml%/.well-known/acme-challenge/ --ftp-create-dirs

#create file with validation and upload it
echo $v>$file
curl -T $file ftp://%ftpusername%:%ftppassword%@%ftphost%%ftpdirectoryhtml%/.well-known/acme-challenge/$t  --ftp-create-dirs

