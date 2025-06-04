# Deploying the Gadjoy Hugo Site

This document explains how to deploy the Gadjoy Repair Service website, which was migrated from WordPress to Hugo. It covers both the migration and deployment process.

## 1. Migration from WordPress (One-Time)

1. **Spin up WordPress locally** using Docker Compose (`wordpress/docker-compose.yml`).
2. **Import the WordPress backup** (see `migration/wp-export/`).
3. **Run the migration script**:
   ```bash
   python3 migration/scripts/wp_to_hugo.py
   ```
   This extracts posts, pages, and media from WordPress, converting them to Hugo Markdown and copying images to `static/img/uploads/`.
4. **Review migration logs and missing images** in `docs/migration/`.
5. **Adjust Hugo config and layouts** as needed.

See `docs/migration/overview.md` for a detailed workflow.

## 2. Building the Hugo Site

1. Install [Hugo Extended](https://gohugo.io/installation/).
2. Build the site locally:
   ```bash
   hugo
   ```
   The static site will be generated in the `public/` directory.

## 3. Deploying to GitHub Pages

1. **Push your code to GitHub** (including the `public/` directory if not using Actions).
2. **Set up GitHub Actions** (recommended) to build and deploy automatically on push to `main`.
3. **Configure GitHub Pages** in repository settings:
   - Set the source to GitHub Actions or the `public/` folder.
   - Add your custom domain if needed.
4. **Update DNS records** at your domain registrar:
   ```
   Type  Name   Value
   A     @      185.199.108.153
   A     @      185.199.109.153
   A     @      185.199.110.153
   A     @      185.199.111.153
   CNAME www    yourusername.github.io
   ```

## 4. Alternative Hosting

You can also deploy the `public/` directory to Netlify, Vercel, or any static host.

## 5. References

- See `docs/migration/overview.md` for the full migration process and troubleshooting.
- See `migration/scripts/` for migration utilities.

---

For questions or issues, please open an issue or contact the site owner.
