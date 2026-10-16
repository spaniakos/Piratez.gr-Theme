# GitHub releases and WordPress updates

Merge the release changes to `main` to publish the first installable release. GitHub Actions builds a ZIP, `release.json` and `SHA256SUMS`, then publishes a stable version tag using the built-in GitHub token. Enable Actions in the repository if disabled. No personal access token is needed.

For later releases, bump the main file header, the version constant and the readme stable tag together, update the changelog, and merge to `main`. Existing versions are never overwritten. The workflow can also be run manually on `main`.

Run `python3 tools/build_release.py` locally to inspect `dist/`. The allowlist in `release-config.json` controls runtime files; tests and development tools are excluded. The ZIP has one fixed top-level folder so WordPress can replace an existing installation.

Install the first updater-enabled release manually through WordPress Upload Theme / Upload Plugin. Subsequent releases appear in the standard WordPress update screens. With the PirateZ theme active, Appearance → PirateZ Updates & Modules also offers check, install, update and separate activation controls for the two optional modules. Each addon checks for its own releases even when another theme is active.

Only stable releases with matching package identity, SHA-256 and WordPress/PHP requirements are accepted. Private repositories and arbitrary third-party URLs are not supported. Failed downloads leave the installed version untouched. Standard WordPress permissions, nonces, filesystem restrictions and automatic-update choices still apply.

GitHub release checks are cached for six hours; Check for updates refreshes them. When GitHub is unavailable or rate-limited, the installed theme/modules keep working. Sites requiring FTP credentials can use the standard WordPress update screens.

## Manager extraction (theme 1.3.0)

PirateZ Manager 1.0.0 moves the optional catalogue to a standalone plugin. Publish Manager first, then theme 1.3.0; activation and theme-update order are both supported. Install Manager once from its release ZIP. It supplies the top-level PirateZ admin menu and manages the same existing addons.

The theme retains its independent updater. With Manager inactive it restores the legacy Appearance → PirateZ Updates & Modules screen. Activating Manager suppresses the legacy implementation on the next request. No content/settings import is needed, and render-blocking optimization is unchanged. Deactivate Manager for rollback; retain the previous theme ZIP for ordinary theme rollback.
