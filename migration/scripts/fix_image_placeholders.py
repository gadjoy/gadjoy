#!/usr/bin/env python3
"""
Fix image placeholders and extract missing images from WordPress
"""

import os
import re
import subprocess
from pathlib import Path

class ImagePlaceholderFixer:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        
    def fix_responsive_banners(self):
        """Replace responsive banner placeholders with better content"""
        blog_dir = self.content_dir / "blog"
        fixed_count = 0
        
        for md_file in blog_dir.rglob("*.md"):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                original_content = content
                
                # Replace generic image placeholders with device-specific content
                content = re.sub(
                    r'\[Image showing device before repair\]',
                    'Device condition before our professional repair service.',
                    content
                )
                
                content = re.sub(
                    r'\[Image showing device after repair\]',
                    'Device restored to full working condition after expert repair.',
                    content
                )
                
                # Replace generic "Repair Image" alt text
                content = re.sub(
                    r'alt="Repair Image"',
                    'alt="Professional device repair"',
                    content
                )
                
                # Remove "class="img-responsive"" which might be causing issues
                content = re.sub(
                    r'class="img-responsive"\s*',
                    '',
                    content
                )
                
                if content != original_content:
                    with open(md_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    fixed_count += 1
                    
            except Exception as e:
                print(f"Error fixing {md_file}: {str(e)}")
        
        print(f"Fixed image placeholders in {fixed_count} files.")
    
    def extract_missing_image(self, post_id, slug):
        """Extract image URLs from WordPress for a specific post"""
        try:
            # Get post content and look for image URLs
            cmd = [
                "docker", "exec", "gadjoy-db-1", "mysql", 
                "-u", "root", "-proot_password", "wordpress", 
                "-e", f"""
                SELECT post_content FROM wp_posts 
                WHERE ID = {post_id}
                LIMIT 1;
                """
            ]
            
            result = subprocess.run(cmd, capture_output=True, text=False)
            content = result.stdout.decode('utf-8', errors='ignore')
            
            # Extract image URLs
            img_patterns = [
                r'src="([^"]*uploads/[^"]*\.(?:jpg|jpeg|png|gif))"',
                r'wp-content/uploads/([^"]*\.(?:jpg|jpeg|png|gif))',
                r'http://localhost:8080/wp-content/uploads/([^"]*\.(?:jpg|jpeg|png|gif))'
            ]
            
            images = []
            for pattern in img_patterns:
                matches = re.findall(pattern, content, re.IGNORECASE)
                images.extend(matches)
            
            return images
            
        except Exception as e:
            print(f"Error extracting images for post {post_id}: {str(e)}")
            return []
    
    def add_missing_images_to_posts(self):
        """Add missing images to posts that have them in WordPress"""
        results_file = self.base_dir / "migration" / "image_check_results.txt"
        
        if not results_file.exists():
            print("Image check results file not found. Run check_missing_images.py first.")
            return
        
        with open(results_file, 'r') as f:
            content = f.read()
        
        # Extract posts with images found in WordPress
        found_section = content.split("Posts with images found in WordPress database:")[1]
        found_section = found_section.split("Posts truly without images:")[0]
        
        lines = found_section.strip().split('\n')
        added_images = 0
        
        for line in lines:
            if line.startswith("Slug:"):
                # Extract slug and post ID
                parts = line.split()
                if len(parts) >= 4:
                    slug = parts[1]
                    post_id = parts[3].rstrip(')')
                    
                    # Find the corresponding blog post file
                    blog_files = list(self.content_dir.rglob(f"*/{slug}/index.md"))
                    if blog_files:
                        blog_file = blog_files[0]
                        
                        # Extract images from WordPress
                        images = self.extract_missing_image(post_id, slug)
                        
                        if images:
                            # Add images to the post
                            self.add_images_to_post(blog_file, images, slug)
                            added_images += 1
                            print(f"✅ Added {len(images)} image(s) to {slug}")
        
        print(f"Added images to {added_images} posts.")
    
    def add_images_to_post(self, blog_file, images, slug):
        """Add images to a specific blog post"""
        try:
            with open(blog_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Split frontmatter and content
            parts = content.split('---')
            if len(parts) >= 3:
                frontmatter = parts[1]
                body = '---'.join(parts[2:])
                
                # Add images to the body
                image_content = "\n\n### Repair Images\n\n"
                for i, img in enumerate(images[:3]):  # Limit to 3 images
                    # Clean up image path
                    clean_img = img.replace('http://localhost:8080/wp-content/', '')
                    if not clean_img.startswith('/'):
                        clean_img = '/' + clean_img
                    
                    image_content += f'{{{{< figure src="{clean_img}" alt="Device repair process {i+1}" >}}}}\n\n'
                
                # Insert images after existing content
                if body.strip():
                    body = body.strip() + image_content
                else:
                    body = image_content
                
                # Reconstruct content
                new_content = '---' + frontmatter + '---\n\n' + body
                
                with open(blog_file, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                    
        except Exception as e:
            print(f"Error adding images to {blog_file}: {str(e)}")

if __name__ == "__main__":
    fixer = ImagePlaceholderFixer()
    print("Fixing image placeholders...")
    fixer.fix_responsive_banners()
    
    print("Adding missing images from WordPress...")
    fixer.add_missing_images_to_posts()