# Missing Images Issue and Fix

## Symptom

After migrating from WordPress to Hugo, many blog posts still show missing images (404 errors), even though the raw export included images for ~98% of posts.

For example, in `content/blog/2021/12/10/mask-clip/index.md`:

```markdown
{{< figure src="/uploads/2021/08/WhatsApp-Image-2021-08-13-at-7.31.02-PM.jpeg" alt="Device repair process 1" >}}
{{< figure src="/2021/08/WhatsApp-Image-2021-08-13-at-7.31.02-PM.jpeg" alt="Device repair process 2" >}}
```

These paths do not match the actual location under `static/img/uploads/...`, causing Hugo to 404 on those image references.

## Root Cause

The migration script `fix_missing_images.py` only normalizes paths starting with `uploads/` or paths without a leading slash. Paths beginning with `/uploads/...` or `/YYYY/...` fall through unchanged.

```diff
- if clean_path.startswith('uploads/'):
-     clean_path = '/img/' + clean_path
- elif not clean_path.startswith('/'):
-     clean_path = '/img/uploads/' + clean_path
```

## Solution

Update the path normalization to handle all cases, including leading-slash patterns:

```diff
- if clean_path.startswith('uploads/'):
-     clean_path = '/img/' + clean_path
- elif not clean_path.startswith('/'):
-     clean_path = '/img/uploads/' + clean_path
+ if clean_path.startswith('/uploads/'):
+     clean_path = '/img' + clean_path
+ elif clean_path.startswith('uploads/'):
+     clean_path = '/img/' + clean_path
+ elif clean_path.startswith('/'):
+     clean_path = '/img/uploads' + clean_path
+ else:
+     clean_path = '/img/uploads/' + clean_path
```

This ensures any image reference—whether `/uploads/...`, `uploads/...`, `/2021/...`, or `2021/...`—maps correctly under `/img/uploads/...`.

## Applying the Fix

The `fix_missing_images.py` script has been updated. To re-run it and apply fixes:

```bash
python3 migration/scripts/fix_missing_images.py
```

Optionally, re-run the quick image audit to confirm improved coverage:

```bash
python3 migration/scripts/quick_image_check.py
```