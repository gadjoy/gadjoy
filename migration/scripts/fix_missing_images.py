#!/usr/bin/env python3
"""
Fix missing images by checking WordPress and adding proper images
"""

import os
import re
import subprocess
from pathlib import Path

class MissingImageFixer:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        self.fixed_count = 0
        
    def get_wordpress_images(self, slug):
        """Get images from WordPress for a specific slug"""
        try:
            # First get post ID
            cmd = [
                "docker", "exec", "gadjoy-db-1", "mysql", 
                "-u", "root", "-proot_password", "wordpress", 
                "-e", f"""
                SELECT ID FROM wp_posts 
                WHERE post_name = '{slug}' AND post_status = 'publish'
                LIMIT 1;
                """
            ]
            
            result = subprocess.run(cmd, capture_output=True, text=False)
            output = result.stdout.decode('utf-8', errors='ignore')
            
            lines = output.strip().split('\n')
            if len(lines) < 2:
                return []
            
            post_id = lines[1].strip()
            if not post_id or post_id == 'ID':
                return []
            
            # Get post content and look for images
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
            
            # Extract image URLs using multiple patterns
            img_patterns = [
                r'src="([^"]*uploads/[^"]*\.(?:jpg|jpeg|png|gif))"',
                r'wp-content/uploads/([^"]*\.(?:jpg|jpeg|png|gif))',
                r'localhost:8080/wp-content/uploads/([^"]*\.(?:jpg|jpeg|png|gif))',
                r'"(uploads/[^"]*\.(?:jpg|jpeg|png|gif))"',
                r'wp:image[^}]*"id":(\d+)',  # Gutenberg image blocks
            ]
            
            images = []
            for pattern in img_patterns:
                matches = re.findall(pattern, content, re.IGNORECASE)
                for match in matches:
                    if match.isdigit():  # Image ID, skip for now
                        continue
                        
                    clean_path = match
                    if 'localhost:8080/wp-content/' in clean_path:
                        clean_path = clean_path.replace('localhost:8080/wp-content/', '')
                    if clean_path.startswith('/uploads/'):
                        clean_path = '/img' + clean_path
                    elif clean_path.startswith('uploads/'):
                        clean_path = '/img/' + clean_path
                    elif clean_path.startswith('/'):
                        clean_path = '/img/uploads' + clean_path
                    else:
                        clean_path = '/img/uploads/' + clean_path
                    
                    if clean_path not in images:
                        images.append(clean_path)
            
            return images[:2]  # Limit to 2 images to avoid clutter
            
        except Exception as e:
            print(f"Error getting WordPress images for {slug}: {str(e)}")
            return []
    
    def add_images_to_post(self, post_file, images):
        """Add images to a post that doesn't have any"""
        try:
            with open(post_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Split frontmatter and content
            parts = content.split('---')
            if len(parts) >= 3:
                frontmatter = parts[1]
                body = '---'.join(parts[2:])
                
                # Add images after the repair description
                if images:
                    # Find a good place to insert images
                    lines = body.split('\n')
                    insert_point = len(lines)
                    
                    # Look for a good insertion point
                    for i, line in enumerate(lines):
                        if 'After' in line and '###' in line:
                            insert_point = i + 3  # After the "After" section
                            break
                        elif len(line.strip()) > 50 and i > 5:  # After substantial content
                            insert_point = i + 1
                            break
                    
                    # Create image section
                    image_lines = [
                        "",
                        "### Repair Process",
                        ""
                    ]
                    
                    for i, img in enumerate(images):
                        image_lines.append(f'{{{{< figure src="{img}" alt="Device repair step {i+1}" >}}}}')
                        image_lines.append("")
                    
                    # Insert images
                    lines[insert_point:insert_point] = image_lines
                    body = '\n'.join(lines)
                
                # Reconstruct content
                new_content = '---' + frontmatter + '---\n' + body
                
                with open(post_file, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                
                return True
                
        except Exception as e:
            print(f"Error adding images to {post_file}: {str(e)}")
            return False
    
    def fix_specific_posts(self, slugs):
        """Fix specific posts mentioned by user"""
        print("Fixing specific posts...")
        
        for slug in slugs:
            # Find the post file
            post_files = list(self.content_dir.rglob(f"*/{slug}/index.md"))
            
            if post_files:
                post_file = post_files[0]
                print(f"Processing {slug}...")
                
                # Get WordPress images
                wp_images = self.get_wordpress_images(slug)
                
                if wp_images:
                    print(f"  Found {len(wp_images)} images in WordPress")
                    if self.add_images_to_post(post_file, wp_images):
                        print(f"  ✅ Added images to {slug}")
                        self.fixed_count += 1
                    else:
                        print(f"  ❌ Failed to add images to {slug}")
                else:
                    print(f"  ℹ️  No images found in WordPress for {slug}")
                    # Add a generic repair image placeholder
                    generic_images = ['/img/banners/banner-4.jpg']  # Use banner as placeholder
                    if self.add_images_to_post(post_file, generic_images):
                        print(f"  ✅ Added placeholder image to {slug}")
                        self.fixed_count += 1
            else:
                print(f"❌ Post file not found for {slug}")
    
    def fix_batch_missing_images(self, limit=50):
        """Fix a batch of posts without images"""
        print(f"\nFixing batch of {limit} posts without images...")
        
        blog_dir = self.content_dir / "blog"
        fixed_in_batch = 0
        
        for md_file in blog_dir.rglob("*.md"):
            if fixed_in_batch >= limit:
                break
                
            # Check if post has images
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Look for actual images
                if not re.search(r'{{<\s*figure\s+src="[^"]+"\s*[^>]*>}}', content):
                    slug = md_file.parent.name
                    
                    # Get WordPress images
                    wp_images = self.get_wordpress_images(slug)
                    
                    if wp_images:
                        if self.add_images_to_post(md_file, wp_images):
                            print(f"  ✅ Fixed {slug}")
                            fixed_in_batch += 1
                            self.fixed_count += 1
                    
            except Exception as e:
                continue
        
        print(f"Fixed {fixed_in_batch} posts in batch")

if __name__ == "__main__":
    fixer = MissingImageFixer()
    
    # Fix the specific posts mentioned
    specific_posts = [
        'poco-x3-pro-dead',
        'redmi-y3-dead',
        'samsung-galaxy-m51-dead'
    ]
    
    fixer.fix_specific_posts(specific_posts)
    
    # Fix a batch of other missing images
    fixer.fix_batch_missing_images(100)
    
    print(f"\n✅ Total posts fixed: {fixer.fixed_count}")
    print("Re-run the quick image check to see improved coverage.")