# Gadjoy Repair Service Website

A static website for Gadjoy Repair Service, migrated from WordPress to Hugo. This site features service pages, a blog, testimonials, and a gallery, all generated from Markdown content and managed with Hugo.

## 🎨 Collections

The portfolio includes various art collections:
- Acrylic on Canvas
- Acrylic and Oil on Canvas
- Acrylic with Texture
- Oil on Canvas
- Water Color on Paper
- Glass Tiles on Wooden Plate (Mosaic)
- Mixed Media Works

## 🛠 Technical Stack

- **Static Site Generator**: [Hugo](https://gohugo.io/)
- **Migration**: Custom Python scripts (see `migration/scripts/`) to convert WordPress content to Hugo
- **Original Source**: WordPress (Docker Compose setup in `wordpress/`)
- **Hosting**: GitHub Pages, Netlify, or any static host
- **CI/CD**: GitHub Actions (optional)

## 🚀 Local Development

### Prerequisites

1. Install [Hugo Extended](https://gohugo.io/installation/) (version 0.123.0 or later)
2. Python 3 (for migration scripts)
3. Docker (for local WordPress if needed)
4. Git

### Setup

1. Clone the repository:
   ```bash
   git clone git@github.com:YourUsername/gadjoy.git
   cd gadjoy
   ```
2. Start the Hugo development server:
   ```bash
   hugo server -D
   ```
3. View the site at: `http://localhost:1313/`

## 📝 Content Management

- **Blog posts**: `content/blog/YYYY/MM/DD/slug/index.md`
- **Service pages**: `content/services/we-repair/`, `content/services/we-build/`
- **Contact page**: `content/contact/index.md`
- **Gallery**: `content/gallery/index.md`
- **Testimonials, features, carousel**: `data/` directory (YAML/JSON)
- **Images**: `static/img/uploads/` (migrated from WordPress)

## 🔄 Migration from WordPress

1. **Spin up WordPress locally** using Docker Compose (`wordpress/docker-compose.yml`).
2. **Import the WordPress backup** (see `migration/wp-export/`).
3. **Run migration scripts** in `migration/scripts/` (notably `wp_to_hugo.py`) to extract posts, pages, and media, converting them to Hugo Markdown and copying images.
4. **Review and adjust**: Check for missing images or formatting issues (see `docs/migration/`).
5. **Build and deploy** the Hugo site.

See `docs/migration/overview.md` for a detailed migration workflow.

## 🔄 Deployment

The site can be deployed to GitHub Pages, Netlify, or any static host. For GitHub Pages:

1. Push your code to GitHub.
2. Use a GitHub Actions workflow or build locally and push the `public/` directory.
3. Configure your custom domain in repository settings and update DNS records as needed.

## 🌐 Domain Configuration

To use a custom domain:
1. Add your domain in GitHub repository settings under Pages
2. Configure your DNS with the following records:
   ```
   Type  Name   Value
   A     @      185.199.108.153
   A     @      185.199.109.153
   A     @      185.199.110.153
   A     @      185.199.111.153
   CNAME www    yourusername.github.io
   ```

## 📄 License

All content and images are property of Gadjoy Repair Service. Website code is available under the MIT license.

## 🤝 Contributing

For technical issues or suggestions:
1. Create an issue
2. Fork the repository
3. Create a pull request

For content updates, please contact the site owner directly.

## 📞 Contact

For inquiries about services, use the contact form on the website.
