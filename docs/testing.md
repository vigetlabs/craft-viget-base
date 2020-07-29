---
layout: default
title: Testing
nav_order: 4
---

# Testing

This repo utilizes Docker in order to run the test suite.

1. Install [Docker Desktop for Mac](https://docs.docker.com/docker-for-mac/install/).

2. Build the containers

    Run `docker-compose build`. This will take a little while.

3. Run the app

    Run `docker-compose up`.

4. Run the tests

    Run `bin/codecept run`
