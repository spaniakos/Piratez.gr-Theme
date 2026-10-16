# Manager extraction integration

The coordinated `spaniakos/piratez-manager` PR includes `tests/wordpress.cjs`, exercised against this theme's 1.3.0 source and the 1.2.0 source as the initial installation. It verifies both update orders, single menu ownership, independent updater operation, saved settings, Manager deactivation fallback and operation under a standard theme. See Manager's `tests/README.md` for baseline/dependency setup.

The existing `tests/releases.cjs` covers the theme fallback installer without Manager. Its fixtures now target 1.3.0 → 1.3.1.
