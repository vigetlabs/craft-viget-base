---
layout: default
title: Testing
nav_order: 7
---

# Testing

This repo utilizes DDEV in order to run the test suite locally. 

Tests are run in GitHub Actions when PRs are opened.  

### Install DDEV
 [DDEV](https://ddev.com/).

### Launch DDEV
```
ddev start
```

### Run the tests

```
# Run all tests
ddev codecept run unit

# Run specific test
ddev codecept run unit PartsKitTest

# Run without database setup (must run without env flag first)
ddev codecept run unit PartsKitTest --env fast

```

Run specific tests 
```
```
