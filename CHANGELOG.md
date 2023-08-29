# Release Notes for Viget Base Module

## 5.0.2 - 2023-08-29
- Allows collections in `Util::fillInEntries()` method.

## 5.0.1 - 2023-03-01
- Adds `yiiDebugBar` config option to disable the Yii debug bar while in dev mode.
- Don't query for edit entry button unless the user has permission to see button.

## 5.0.0 - 2022-08-19
- Initial Craft CMS 4 Release
- Converted into an auto-bootstrapping extension/module.

## 4.1.0 - 2022-05-24

- Parts kit UI ([#50](https://github.com/vigetlabs/craft-viget-base/pull/50))
  - main panel is no taller than content
  - no unforced vertical overflow
  - sidebar is sticky and scrolls independent of the main panel

## 4.0.0 - 2022-01-26

- Automatically redirect parts kit index request to first component ([#36](https://github.com/vigetlabs/craft-viget-base/issues/36))
- Change installation recommendation to bootstrap the module instead of initiating within an existing module. [See upgrade notes](http://code.viget.com/craft-viget-base/installation.html#upgrading)

## 3.1.3 - 2022-01-26

- Fix `f` keyboard shortcut triggering when CTRL or CMD is also pressed. (#44)

## 3.1.2 - 2021-09-07

- Fix issues with turbo compatibility by disabling it for the entire parts kit (#40)

## 3.1.1 - 2021-06-14

- Fix issue with accessing user early conflicting with some plugins (#34)

## 3.1.0 - 2021-04-20

- Fix parts kit sidebar styling for parts with long names
- Show field handles in dev environment
- Add custom `gtm()` Twig function

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
