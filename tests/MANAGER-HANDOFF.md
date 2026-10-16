# Manager extraction integration

The `spaniakos/piratez-manager` repository includes `tests/wordpress.cjs`, exercised against this theme's 1.3.0 source and optionally the 1.2.0 source as the initial installation. It verifies single management ownership, saved settings, independent updater operation, a standard theme and the installer-only fallback. See Manager's `tests/README.md` for baseline/dependency setup.

The theme has no legacy manager copy. Tests cover missing Manager installation with verified packages, separate activation, active/open state, reactivation after deactivation, renamed Manager folders, occupied directories, rejected packages/nonces and filesystem restrictions.

The theme's `tests/releases.cjs` independently verifies native theme/addon updates without Manager, plus existing reading-time, sitemap, theme-preference, anchor and accessibility regressions. Its fixture versions target 1.3.0 → 1.3.1 and use localhost port 9402.
