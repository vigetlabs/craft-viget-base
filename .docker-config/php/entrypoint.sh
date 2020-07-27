#!/bin/bash

set -e

composer install -q

exec "$@"
