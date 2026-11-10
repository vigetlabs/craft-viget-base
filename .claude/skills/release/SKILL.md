---
name: release
description: Walk through a release of this Composer package one step at a time - check CHANGELOG.md against the commits since the last tag, agree the version number, set the changelog heading, then tag, create the GitHub release, and confirm Packagist. Use this whenever the user wants to release, tag, publish, ship, or cut a new version of craft-viget-base, asks if the package is ready to release, or asks to prepare or finish the changelog for a release.
---

# Release

Guide the maintainer through one release of `viget/craft-viget-base`.

The steps are in the "Releasing" section of `CONTRIBUTING.md`. Read that section first. If it differs from this file, follow `CONTRIBUTING.md` and tell the user about the difference.

## How to work

Do one step at a time. Report the result in a few lines, then wait for the user before you start the next step.

A pushed tag publishes to Packagist immediately, and sites get the version on their next `composer update`. A published tag cannot be moved or reused safely. So for each command that changes something outside the working tree (commit, push, pull request, tag, GitHub release):

1. Show the exact command.
2. Offer to run it. The user can also run it themselves.
3. Run it only after the user says yes. A yes for one command is not a yes for the next one.

Changelog wording, the version number, branch names, commit messages and pull request text are the user's decisions. Propose them and wait for an answer.

## Steps

### 1. Preflight

Collect these facts and report them. Do not fix anything yet.

- The current branch is `v6`, the working tree is clean, and `v6` equals `origin/v6` after `git fetch --tags origin`.
- The last tag: `git describe --tags --abbrev=0`.
- The CI result for the newest commit on `v6`: `gh run list --branch v6 --workflow ci --limit 1`.

If CI failed or is still in progress, stop and tell the user.

### 2. Check the changelog

List the changes since the last tag:

```
git log <last-tag>..HEAD --oneline
git diff <last-tag>..HEAD --stat -- src composer.json
```

Read the diff of each commit that changes `src/` or `composer.json`, then compare it with the top section of `CHANGELOG.md`. Report three things:

- **Missing entries:** changes that a site developer can see, with no entry.
- **Incorrect entries:** entries that do not agree with the code. Compare each entry with the last released version, not with an earlier state of a pull request. An entry is only a change if the behavior differs from the last tag.
- **Entries with no matching change.**

Commits that change only tests, CI or development tooling need no entry. Say which commits you left out for this reason.

Propose the wording for each correction. Edit `CHANGELOG.md` only after the user agrees.

### 3. Agree the version number

Propose a number and give the reason. Composer's `^` constraints assume Semantic Versioning: a breaking change needs a new major version, a new feature a new minor version, and a fix a new patch version. A "Breaking" entry in the section is a sign that the major number must change.

After the user chooses:

- Make sure that the tag does not exist: `git tag -l X.Y.Z`.
- Find version numbers in the source that must agree with it: `git grep -n -E '@(since|deprecated)' -- src`. Report each one that names a different version.

### 4. Set the changelog heading

Change the top heading in `CHANGELOG.md` to `## X.Y.Z - YYYY-MM-DD`. Use today's date unless the user gives a different release date.

`v6` accepts changes only through pull requests. Propose a branch name, a commit message and the pull request text, then do the branch, commit, push and pull request one at a time, each with the user's yes. The user merges the pull request.

After the merge:

```
git checkout v6
git pull
```

Make sure that the heading is on `v6` and that the `ci` workflow passes on the merged commit.

### 5. Tag

Make sure that `HEAD` is the merged changelog commit and equals `origin/v6`. Then show the commands:

```
git tag -a X.Y.Z -m "X.Y.Z release"
git push origin X.Y.Z
```

The tag is annotated and has no `v` prefix, the same as the other 6.x tags. Tell the user that the push is the point where the version becomes public, then offer to run the commands.

### 6. GitHub release

Copy the changelog section for the version into a temporary file that git ignores. Show the notes to the user. Then show the command and offer to run it:

```
gh release create X.Y.Z --verify-tag --title X.Y.Z --notes-file <file>
```

`--verify-tag` stops the command if the tag is not on GitHub, so it cannot create a tag by accident. Delete the temporary file afterwards.

### 7. Confirm

- `gh release view X.Y.Z` shows the release.
- Packagist shows the version. Read `https://repo.packagist.org/p2/viget/craft-viget-base.json` if you have network access to it. If you do not, ask the user to look at https://packagist.org/packages/viget/craft-viget-base. If the version is not there after a few minutes, a maintainer can select "Update" on that page.

Finish with a short summary: the version, the tag, the release URL and the Packagist result.
