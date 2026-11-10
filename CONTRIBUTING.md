# Contributing

## Releasing

[Packagist](https://packagist.org/packages/viget/craft-viget-base) publishes a new version when a tag is pushed to GitHub. `composer.json` has no `version` field, so the tag name is the version.

Release from `v6`, the branch for Craft CMS 5. This branch accepts changes only through pull requests.

1. **Check the changelog.** Compare the top section of `CHANGELOG.md` with the changes since the last tag:

   ```
   git fetch --tags origin
   git log $(git describe --tags --abbrev=0 origin/v6)..origin/v6 --oneline
   ```

   Each change that a site developer can see needs an entry. Changes to tests and CI do not.

2. **Choose the version number.** Composer's `^` constraints assume [Semantic Versioning](https://semver.org/): a breaking change needs a new major version, a new feature a new minor version, and a fix a new patch version.

3. **Set the changelog heading.** Change the top heading in `CHANGELOG.md` to `## X.Y.Z - YYYY-MM-DD`, with the release date. Open a pull request with this change and merge it.

4. **Check CI.** Make sure that the `ci` workflow passes on the merged commit on `v6`.

5. **Tag the release.** Use an annotated tag with no `v` prefix, on the merged changelog commit:

   ```
   git checkout v6
   git pull
   git tag -a X.Y.Z -m "X.Y.Z release"
   git push origin X.Y.Z
   ```

   The push publishes the version. Do not move or reuse a tag after you push it.

6. **Create the GitHub release.** Use the tag name as the title and the changelog section for the version as the notes:

   ```
   gh release create X.Y.Z --verify-tag --title X.Y.Z --notes-file <file>
   ```

7. **Check Packagist.** Make sure that the [package page](https://packagist.org/packages/viget/craft-viget-base) shows the new version. If it does not, a maintainer can select "Update" on that page.

If you use Claude Code, the `release` skill (`.claude/skills/release/SKILL.md`) goes through these steps with you.
