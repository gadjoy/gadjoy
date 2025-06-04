#!/usr/bin/env python3
"""
WordPress to Hugo Migration Script for Gadjoy Repair Service
Based on the original Gadjoy migration pattern
"""

import os
import re
import json
import subprocess
import shutil
from datetime import datetime
from pathlib import Path

class GadjoyMigration:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.wp_content_dir = self.base_dir / "migration" / "wp-export" / "wp-content"
        self.hugo_content_dir = self.base_dir / "content"
        self.hugo_static_dir = self.base_dir / "static"
        self.migration_log = []
    
    def get_db_container_name(self):
        """Detect the Docker container running the WordPress database (service 'db')."""
        try:
            result = subprocess.run(
                ["docker", "ps", "--filter", "label=com.docker.compose.service=db", "--format", "{{.Names}}"],
                capture_output=True, text=True
            )
            names = [n.strip() for n in result.stdout.splitlines() if n.strip()]
            if names:
                return names[0]
        except Exception:
            pass
        # Fallback to common container name from docker-compose
        return "gadjoy_db_1"
        
    def log(self, message):
        """Log migration messages"""
        print(f"[MIGRATION] {message}")
        self.migration_log.append(f"{datetime.now()}: {message}")
    
    def get_posts_from_db(self):
        """Query WordPress database for posts metadata"""
        self.log("Querying WordPress database for posts...")
        
        db_container = self.get_db_container_name()
        cmd = [
            "docker", "exec", db_container, "mysql", 
            "-u", "root", "-proot_password", "wordpress", 
            "-e", """
            SELECT 
                ID, post_title, post_name, post_content, post_excerpt,
                post_date, post_status, post_type,
                YEAR(post_date) as year, MONTH(post_date) as month, DAY(post_date) as day
            FROM wp_posts 
            WHERE post_type='post' AND post_status='publish' 
            ORDER BY post_date DESC;
            """
        ]
        
        result = subprocess.run(cmd, capture_output=True, text=False)
        return self.parse_mysql_output(result.stdout.decode('utf-8', errors='ignore'))
    
    def get_pages_from_db(self):
        """Query WordPress database for pages metadata"""
        self.log("Querying WordPress database for pages...")
        
        db_container = self.get_db_container_name()
        cmd = [
            "docker", "exec", db_container, "mysql", 
            "-u", "root", "-proot_password", "wordpress", 
            "-e", """
            SELECT 
                ID, post_title, post_name, post_content, post_excerpt,
                post_date, post_status, post_type
            FROM wp_posts 
            WHERE post_type='page' AND post_status='publish' 
            ORDER BY post_date DESC;
            """
        ]
        
        result = subprocess.run(cmd, capture_output=True, text=False)
        return self.parse_mysql_output(result.stdout.decode('utf-8', errors='ignore'))
    
    def parse_mysql_output(self, output):
        """Parse MySQL tabular output into list of dictionaries"""
        lines = output.strip().split('\n')
        if len(lines) < 2:
            return []
        
        headers = lines[0].split('\t')
        posts = []
        
        for line in lines[1:]:
            if line.strip():
                values = line.split('\t')
                post = dict(zip(headers, values))
                posts.append(post)
        
        return posts
    
    def convert_gutenberg_to_markdown(self, content):
        """Convert WordPress Gutenberg blocks to Markdown"""
        if not content:
            return ""
        
        # Handle Gutenberg gallery blocks: convert to Hugo figure shortcodes
        gallery_pattern = r'<!-- wp:gallery.*?-->(.*?)<!-- /wp:gallery -->'
        def replace_gallery(match):
            gallery_html = match.group(1)
            items = re.findall(r'<figure[^>]*>(.*?)</figure>', gallery_html, flags=re.DOTALL)
            md_parts = []
            for item in items:
                src_match = re.search(r'<img [^>]*src="([^"]+)"', item)
                caption_match = re.search(r'<figcaption>(.*?)</figcaption>', item, flags=re.DOTALL)
                src = src_match.group(1) if src_match else ''
                caption = caption_match.group(1).strip() if caption_match else ''
                md_parts.append(f'{{{{< figure src="{src}" caption="{caption}" >}}}}')
            return '\n\n'.join(md_parts)
        content = re.sub(gallery_pattern, replace_gallery, content, flags=re.DOTALL)

        # Handle Gutenberg image blocks
        img_pattern = r'<!-- wp:image.*?"src":"([^"]+)".*?-->(.*?)<!-- /wp:image -->'
        content = re.sub(img_pattern, r'![Image](\1)', content, flags=re.DOTALL)
        
        # Handle standard image blocks
        img_pattern2 = r'<figure class="wp-block-image[^"]*"><img src="([^"]+)"[^>]*></figure>'
        content = re.sub(img_pattern2, r'![Image](\1)', content)
        
        # Handle paragraphs
        content = re.sub(r'<!-- wp:paragraph -->\s*<p>(.*?)</p>\s*<!-- /wp:paragraph -->', r'\1\n', content, flags=re.DOTALL)
        
        # Handle headings
        content = re.sub(r'<!-- wp:heading[^>]*-->\s*<h([1-6])[^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->', r'\n\g<1> \2\n', content, flags=re.DOTALL)
        
        # Convert heading tags
        content = re.sub(r'<h([1-6])[^>]*>(.*?)</h[1-6]>', lambda m: '#' * int(m.group(1)) + ' ' + m.group(2), content)
        
        # Handle lists
        content = re.sub(r'<!-- wp:list -->.*?<ul[^>]*>(.*?)</ul>.*?<!-- /wp:list -->', r'\1', content, flags=re.DOTALL)
        content = re.sub(r'<li[^>]*>(.*?)</li>', r'- \1', content, flags=re.DOTALL)
        
        # Clean up remaining HTML tags
        content = re.sub(r'<p[^>]*>(.*?)</p>', r'\1\n\n', content, flags=re.DOTALL)
        content = re.sub(r'<br\s*/?>', '\n', content)
        content = re.sub(r'<[^>]+>', '', content)
        
        # Clean up extra whitespace
        content = re.sub(r'\n\s*\n\s*\n', '\n\n', content)
        content = content.strip()
        
        return content
    
    def create_blog_post(self, post):
        """Create Hugo blog post from WordPress post"""
        try:
            # Parse date
            post_date = datetime.strptime(post['post_date'], '%Y-%m-%d %H:%M:%S')
            year = post_date.strftime('%Y')
            month = post_date.strftime('%m')
            day = post_date.strftime('%d')
            
            # Create directory structure
            post_dir = self.hugo_content_dir / "blog" / year / month / day / post['post_name']
            post_dir.mkdir(parents=True, exist_ok=True)
            
            # Convert content
            content = self.convert_gutenberg_to_markdown(post['post_content'])
            
            # Extract categories from repair type
            categories = self.extract_categories(post['post_title'])
            
            # Create front matter
            front_matter = f"""---
title: "{post['post_title']}"
date: {post_date.strftime('%Y-%m-%dT%H:%M:%S')}
draft: false
categories: {categories}
tags: {self.extract_tags(post['post_title'])}
description: "{post.get('post_excerpt', '').replace('"', '\\"')}"
slug: "{post['post_name']}"
---

{content}
"""
            
            # Write post file
            post_file = post_dir / "index.md"
            with open(post_file, 'w', encoding='utf-8') as f:
                f.write(front_matter)
            
            self.log(f"Created blog post: {post['post_title']}")
            return True
            
        except Exception as e:
            self.log(f"Error creating post {post['post_title']}: {str(e)}")
            return False
    
    def extract_categories(self, title):
        """Extract device categories from post title"""
        categories = []
        
        # Device types
        if any(x in title.lower() for x in ['iphone', 'ipad', 'apple']):
            categories.append('Apple')
        elif any(x in title.lower() for x in ['samsung', 'galaxy']):
            categories.append('Samsung')
        elif any(x in title.lower() for x in ['redmi', 'mi ', 'poco']):
            categories.append('Xiaomi')
        elif any(x in title.lower() for x in ['oneplus', 'one plus']):
            categories.append('OnePlus')
        elif any(x in title.lower() for x in ['realme']):
            categories.append('Realme')
        elif any(x in title.lower() for x in ['vivo']):
            categories.append('Vivo')
        elif any(x in title.lower() for x in ['oppo']):
            categories.append('Oppo')
        elif any(x in title.lower() for x in ['dell', 'hp', 'lenovo', 'macbook', 'laptop']):
            categories.append('Laptops')
        elif any(x in title.lower() for x in ['ipad', 'tab', 'tablet']):
            categories.append('Tablets')
        else:
            categories.append('Electronics')
        
        # Repair types
        if any(x in title.lower() for x in ['display', 'screen']):
            categories.append('Display Repair')
        elif any(x in title.lower() for x in ['battery']):
            categories.append('Battery Replacement')
        elif any(x in title.lower() for x in ['dead', 'not switching', 'no display']):
            categories.append('Dead Device')
        elif any(x in title.lower() for x in ['software', 'os']):
            categories.append('Software Service')
        elif any(x in title.lower() for x in ['charging', 'port']):
            categories.append('Charging Issues')
        else:
            categories.append('General Repair')
        
        return categories
    
    def extract_tags(self, title):
        """Extract tags from post title"""
        tags = []
        
        # Extract device model
        words = title.split()
        for word in words:
            if any(char.isdigit() for char in word) and len(word) > 2:
                tags.append(word)
        
        # Add repair type tags
        if 'display' in title.lower():
            tags.append('display')
        if 'battery' in title.lower():
            tags.append('battery')
        if 'software' in title.lower():
            tags.append('software')
        if 'dead' in title.lower():
            tags.append('dead')
        
        return tags[:5]  # Limit to 5 tags
    
    def create_service_pages(self, pages):
        """Create service pages from WordPress pages"""
        for page in pages:
            try:
                # Map WordPress pages to Hugo structure
                if page['post_name'] == 'we_repair':
                    self.create_service_page(page, 'services/we-repair')
                elif page['post_name'] == 'we_build':
                    self.create_service_page(page, 'services/we-build')
                elif page['post_name'] == 'contact':
                    self.create_service_page(page, 'contact')
                elif page['post_name'] == 'faq':  # Gallery page
                    self.create_service_page(page, 'gallery')
                elif page['post_name'] == 'home':
                    self.create_homepage(page)
                
                self.log(f"Created page: {page['post_title']}")
                
            except Exception as e:
                self.log(f"Error creating page {page['post_title']}: {str(e)}")
    
    def create_service_page(self, page, path):
        """Create a service page"""
        page_dir = self.hugo_content_dir / path
        page_dir.mkdir(parents=True, exist_ok=True)
        
        content = self.convert_gutenberg_to_markdown(page['post_content'])
        
        front_matter = f"""---
title: "{page['post_title']}"
date: {page['post_date']}
draft: false
description: "{page.get('post_excerpt', '').replace('"', '\\"')}"
---

{content}
"""
        
        page_file = page_dir / "index.md"
        with open(page_file, 'w', encoding='utf-8') as f:
            f.write(front_matter)
    
    def create_homepage(self, page):
        """Create homepage content"""
        content = self.convert_gutenberg_to_markdown(page['post_content'])
        
        front_matter = f"""---
title: "Gadjoy Repair Service"
description: "Professional electronics and mobile device repair services in Bangalore"
---

{content}
"""
        
        homepage_file = self.hugo_content_dir / "_index.md"
        with open(homepage_file, 'w', encoding='utf-8') as f:
            f.write(front_matter)
    
    def copy_images(self):
        """Copy WordPress uploads to Hugo static directory"""
        self.log("Copying images from WordPress uploads...")
        
        wp_uploads = self.wp_content_dir / "uploads"
        hugo_uploads = self.hugo_static_dir / "img" / "uploads"
        
        if wp_uploads.exists():
            if hugo_uploads.exists():
                shutil.rmtree(hugo_uploads)
            shutil.copytree(wp_uploads, hugo_uploads)
            self.log(f"Copied uploads from {wp_uploads} to {hugo_uploads}")
    
    def update_image_paths(self):
        """Update image paths in content files"""
        self.log("Updating image paths in content files...")
        
        for md_file in self.hugo_content_dir.rglob("*.md"):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Update WordPress image URLs to Hugo static paths
                content = re.sub(
                    r'http://localhost:8080/wp-content/uploads/',
                    '/img/uploads/',
                    content
                )
                
                with open(md_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                    
            except Exception as e:
                self.log(f"Error updating images in {md_file}: {str(e)}")
    
    def create_migration_log(self):
        """Create migration log file"""
        log_file = self.base_dir / "migration_1" / "migration.log"
        log_file.parent.mkdir(exist_ok=True)
        
        with open(log_file, 'w', encoding='utf-8') as f:
            f.write("Gadjoy WordPress to Hugo Migration Log\n")
            f.write("=" * 50 + "\n\n")
            for entry in self.migration_log:
                f.write(entry + "\n")
    
    def run_migration(self):
        """Run the complete migration process"""
        self.log("Starting Gadjoy WordPress to Hugo migration...")
        
        # Clean and recreate blog content directory for a fresh migration
        blog_dir = self.hugo_content_dir / "blog"
        if blog_dir.exists():
            shutil.rmtree(blog_dir)
            self.log(f"Removed existing blog content at {blog_dir}")
        blog_dir.mkdir(parents=True, exist_ok=True)
        # Ensure services and image directories exist
        (self.hugo_content_dir / "services").mkdir(parents=True, exist_ok=True)
        (self.hugo_static_dir / "img").mkdir(parents=True, exist_ok=True)
        
        # Get data from WordPress
        posts = self.get_posts_from_db()
        pages = self.get_pages_from_db()
        
        self.log(f"Found {len(posts)} posts and {len(pages)} pages")
        
        # Create content
        success_count = 0
        for post in posts:
            if self.create_blog_post(post):
                success_count += 1
        
        self.log(f"Successfully migrated {success_count}/{len(posts)} posts")
        
        # Create service pages
        self.create_service_pages(pages)
        
        # Copy and update images
        self.copy_images()
        self.update_image_paths()
        
        # Create log
        self.create_migration_log()
        
        self.log("Migration completed!")
        return True

if __name__ == "__main__":
    migration = GadjoyMigration()
    migration.run_migration()