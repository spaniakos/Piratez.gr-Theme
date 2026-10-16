# Editorial handoff — theme 1.4.0

Install PirateZ Editorial 1.0.0 or later and use its settings preview/import while theme 1.3.0 is still active. Only then update to theme 1.4.0. Existing source theme settings are retained. Editorial detects theme 1.3.x and enters migration mode: its extracted frontend engines stay passive while the old theme owns them. Preview/import settings, then switch the theme release.

Theme 1.4.0 owns placement and appearance, Editorial owns content behavior. No embedded fallback engine is retained. Without Editorial the theme remains safe, but shortcode rendering and article tools need the plugin. Rollback pairs theme 1.3.0 with deactivated Editorial and preserved theme settings; reconcile subsequent Editorial settings changes explicitly.

The global render-blocking implementation and theme updater are unchanged.
