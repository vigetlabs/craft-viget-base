# Release Notes for Viget Base Module

## 3.0.1 - 2021-03-01

- Add custom sentence length support to `getParagraph()`
- Fix parts kit container margin when expanded
- Fix Tailwind docs filename
- Fix `svg()` deprecation error

## 3.0.0 - 2021-02-10

- Add Tailwind service for exposing config to PHP & Twig
- Add new config file for all services
- Drop support for `dev.php` and `parts-kit.php`
- Add repo link to docs site
- Fix parts kit search input padding

## 2.1.0 - 2021-02-04

- Fix when queue component doesn't implement the QueueInterface (like redis)
- Add parts kit search

## 2.0.1 - 2021-02-03

- Don't try and phone home if Craft isn't installed yet
- Check queue to see if existing phone home job exists before adding a new job

## 2.0.0 - 2020-11-12

- Adjustments to account for a Craft 3.5 change to determine how the debug bar should appear
- Increases Craft dependency to `^3.5.0`

## 1.3.0 - 2020-11-11

- Adds parts kit theme-ability: light (default), dark, and custom

## 1.2.1 - 2020-09-18

- Don't include templates in parts kit nav that start with `_` or `.`

## 1.2.0 - 2020-08-04

- Add Storybook style parts kit
- Add parts kit helper methods
- Add docs folder

## 1.1.0 - 2020-07-28

- Automatically enable debug bar for CP when in devMode
- Add fillInEntries method
- Setup Docker
- Setup test suite + CircleCI
- Add tests
- Add phone home functionality to write to Craft Inventory Airtable

## 1.0.3 - 2020-07-12

- Fix alias path

## 1.0.2 - 2020-07-11

- Fix reference to sample

## 1.0.1 - 2020-07-11

- Fix reference to sample

## 1.0.0 - 2020-07-11

- Initial release moving starter module functionality over to base module
