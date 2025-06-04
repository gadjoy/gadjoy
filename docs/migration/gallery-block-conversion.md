# Gallery Block Conversion Issue and Fix

## Background
Some WordPress posts used Gutenberg gallery blocks to display multiple images side‑by‑side with captions. Because the migration script did not handle these gallery blocks, the Markdown converter stripped out gallery HTML and all inner images, resulting in missing media in the final Hugo posts.

## Symptom
Migrated posts that contained a gallery block (e.g. two images in a column) show no images in the generated Markdown. For example, the Dell Latitude post below lost its gallery entirely:

```markdown
### Before

Device condition before our professional repair service.

### After

Device restored to full working condition after expert repair.

The device was received in dead and no display condition

After we did a diagnostic on the board and a chip level service was required
```

Refer to `content/blog/2025/02/18/dell-latitude-5400-no-display/index.md` to see the missing gallery markup.

## Root Cause
The `convert_gutenberg_to_markdown()` function in `wp_to_hugo.py` only handled individual image blocks (`wp:image`) and generic HTML stripping removed gallery HTML and nested `<img>` tags.

## Fix
Extend `convert_gutenberg_to_markdown()` to detect `wp:gallery` blocks, extract each `<img>` and optional `<figcaption>`, and emit Hugo figure shortcodes:

```diff
--- a/migration/scripts/wp_to_hugo.py
+++ b/migration/scripts/wp_to_hugo.py
@@ def convert_gutenberg_to_markdown(self, content):
-        # Handle Gutenberg image blocks
+        # Handle Gutenberg gallery blocks: convert to Hugo figure shortcodes
+        gallery_pattern = r'<!-- wp:gallery.*?-->(.*?)<!-- /wp:gallery -->'
+        def replace_gallery(match):
+            gallery_html = match.group(1)
+            items = re.findall(r'<figure[^>]*>(.*?)</figure>', gallery_html, flags=re.DOTALL)
+            md_parts = []
+            for item in items:
+                src_match = re.search(r'<img [^>]*src="([^"]+)"', item)
+                caption_match = re.search(r'<figcaption>(.*?)</figcaption>', item, flags=re.DOTALL)
+                src = src_match.group(1) if src_match else ''
+                caption = caption_match.group(1).strip() if caption_match else ''
+                md_parts.append(f'{{{{< figure src="{src}" caption="{caption}" >}}}}')
+            return '\n\n'.join(md_parts)
+        content = re.sub(gallery_pattern, replace_gallery, content, flags=re.DOTALL)
+
+        # Handle Gutenberg image blocks
```

## Re-run Migration
After applying this patch, rerun the WordPress→Hugo migration to regenerate all posts:

```bash
cd migration/scripts
python3 wp_to_hugo.py
```

Then update image paths and perform a quick audit:

```bash
python3 fix_image_paths.py
python3 quick_image_check.py
```

## Verification
Inspect affected posts (e.g., `content/blog/2025/02/18/dell-latitude-5400-no-display/index.md`) to confirm that gallery images and captions now appear as Hugo figure shortcodes.