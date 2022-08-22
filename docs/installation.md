---
layout: default
title: Installation
nav_order: 1
---

# Installation

```
composer require viget/craft-viget-base
```

Lock it at a specific version if desired.

## Auto-Bootstrapping Yii2 Extension

This package is a Yii2 extension (and a module) that [bootstraps itself](https://www.yiiframework.com/doc/guide/2.0/en/structure-extensions#bootstrapping-classes).

This means that it’s automatically loaded with Craft, without you having to install it or configure it in any way.

### Migration
If you're upgrading an old project, remove all of the bootstrapping code (config/app.php or Yii submodule)

## Configure Phone Home

Add the following ENV variables to your enviroment file in all environments

```
AIRTABLE_API_KEY
AIRTABLE_BASE
```

Using the base ID from the Craft Inventory Airtable and the API key from 1Password for the Viget Airtable account.
