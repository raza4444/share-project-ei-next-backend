#!/bin/bash
nohup php /var/www/html/ei-next-backend-core/artisan application:serve-event-reservation > /tmp/serve-events.log &
