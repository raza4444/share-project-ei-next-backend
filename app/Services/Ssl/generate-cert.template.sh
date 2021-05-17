#!/bin/bash

#dryrun
#--dry-run

#echter run
/usr/local/bin/certbot-auto certonly --manual --preferred-challenges=http --manual-public-ip-logging-ok --manual-auth-hook %hook%  -d %domain% -d www.%domain% --reinstall --expand &>> %mainoutputfile%
