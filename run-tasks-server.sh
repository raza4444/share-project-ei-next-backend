#!/bin/bash
nohup php /var/www/html/ei-next-backend-core/artisan application:serve-task-reservation > /var/log/serve-task.log &
