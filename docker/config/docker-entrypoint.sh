#!/bin/sh
set -e

exec supervisord -n -c /etc/supervisord.conf
