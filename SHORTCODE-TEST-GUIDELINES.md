# Editorial shortcodes: test guidelines

Use these snippets in a **post** (Block or Classic editor). In the block editor, use a **Shortcode** block or **Custom HTML**; in the classic editor, paste into the Text tab.

---

## 1. PirateZ Take (opinion / editorial)

**Shortcode:** `[piratez_take]...[/piratez_take]`

**Paste this into your post:**

```
[piratez_take]This is our take: ship the feature when it’s good enough, not when it’s perfect. The best blog post is the one that’s published.[/piratez_take]
```

**With custom title:**

```
[piratez_take title="Our stance"]We recommend enabling the TOC on long posts only.[/piratez_take]
```

---

## 2. Hard Truth (blunt / uncomfortable truth)

**Shortcode:** `[piratez_hard_truth]...[/piratez_hard_truth]`

**Paste this into your post:**

```
[piratez_hard_truth]Most readers will skim. If your first two paragraphs don’t hook them, they’re gone.[/piratez_hard_truth]
```

**With custom title:**

```
[piratez_hard_truth title="Reality check"]No one owes you their attention. Earn it.[/piratez_hard_truth]
```

---

## 3. Warning / Risk (caution)

**Shortcode:** `[piratez_warning]...[/piratez_warning]`

**Paste this into your post:**

```
[piratez_warning]Changing the theme will remove these callout styles. Content stays; only the presentation changes. Consider backing up custom CSS.[/piratez_warning]
```

**With custom title “Risk”:**

```
[piratez_warning title="Risk"]Running untested shortcodes in production can break layout. Test in a staging site first.[/piratez_warning]
```

---

## 4. Note / Insight (neutral note)

**Shortcode:** `[piratez_note]...[/piratez_note]`

**What is “Insight”?** It’s just an **optional custom title** for the Note shortcode. The default label is “Note”. If you use `title="Insight"`, the callout header shows “Insight” instead of “Note” — useful when you want a neutral callout that reads like a key takeaway or side remark.

**Paste this into your post:**

```
[piratez_note]All four shortcodes accept an optional title attribute. Use title="Your label" to override the default.[/piratez_note]
```

**With custom title “Insight”:**

```
[piratez_note title="Insight"]The TOC button appears only when the post has at least two headings (h2, h3, or h4).[/piratez_note]
```

---

## One post with all four (copy-paste block)

Paste the block below into one post to test every shortcode in sequence:

```
Here is a normal paragraph. Below are the four editorial callouts.

[piratez_take]This is a PirateZ Take: an opinion or editorial stance. Use it when you want to signal “this is our view.”[/piratez_take]

[piratez_hard_truth]This is a Hard Truth: a blunt or uncomfortable truth. Use it when you need to be direct.[/piratez_hard_truth]

[piratez_warning]This is a Warning. Use it for caution, risk, or “proceed with care” advice.[/piratez_warning]

[piratez_note]This is a Note (or Insight). Use it for neutral, helpful context or side remarks.[/piratez_note]

That’s all four. Check that each has a distinct border/color and that the title appears above the content.
```

---

## Quick reference

| Shortcode              | Default title | Use for                      |
| ---------------------- | ------------- | ---------------------------- |
| `[piratez_take]`       | PirateZ Take  | Opinion, editorial stance    |
| `[piratez_hard_truth]` | Hard Truth    | Blunt or uncomfortable truth |
| `[piratez_warning]`    | Warning       | Caution, risk                |
| `[piratez_note]`       | Note          | Neutral note or insight      |

**Optional title:** `[piratez_note title="Insight"]...[/piratez_note]` — replace the default label with your own.

---

## Where to see them

- **Front end:** View the post on the site; callouts should have a left border and title.
- **Admin:** **Shortcodes** in the admin menu lists these shortcodes and examples.
