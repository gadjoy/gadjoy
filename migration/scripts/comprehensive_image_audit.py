#!/usr/bin/env python3
"""
Comprehensive audit of missing images in blog posts
"""

import os
import re
import subprocess
from pathlib import Path

class ImageAudit:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        self.posts_with_images = 0
        self.posts_without_images = 0
        self.missing_posts = []
        
    def has_actual_images(self, file_path):
        """Check if post has actual images (not just placeholders)"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Look for actual image markup
            image_patterns = [
                r'{{<\s*figure\s+src="[^"]+"\s*[^>]*>}}',  # Hugo figure shortcode
                r'!\[[^\]]*\]\([^)]+\)',  # Markdown image
                r'<img[^>]+src="[^"]+"[^>]*>',  # HTML img tag
            ]
            
            for pattern in image_patterns:
                if re.search(pattern, content):
                    return True
            
            return False
            
        except Exception as e:
            print(f"Error checking {file_path}: {str(e)}")
            return False
    
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
            
            # Get post content
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
                r'localhost:8080/wp-content/uploads/([^"]*\.(?:jpg|jpeg|png|gif))'
            ]
            
            images = []
            for pattern in img_patterns:
                matches = re.findall(pattern, content, re.IGNORECASE)
                for match in matches:
                    if match not in images:
                        if match.startswith('http'):
                            images.append(match)
                        elif match.startswith('uploads/'):
                            images.append('/img/' + match)
                        else:
                            images.append('/img/uploads/' + match)
            
            return images[:3]  # Limit to 3 images
            
        except Exception as e:
            print(f"Error getting WordPress images for {slug}: {str(e)}")
            return []
    
    def add_wordpress_images_to_post(self, file_path, images):
        """Add WordPress images to a post"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Split frontmatter and content
            parts = content.split('---')
            if len(parts) >= 3:
                frontmatter = parts[1]
                body = '---'.join(parts[2:])
                
                # Add images section
                if images:
                    image_section = "\n\n### Repair Images\n\n"
                    for i, img in enumerate(images):
                        image_section += f'{{{{< figure src="{img}" alt="Device repair process step {i+1}" >}}}}\n\n'
                    
                    body = body.strip() + image_section
                
                # Reconstruct content
                new_content = '---' + frontmatter + '---\n\n' + body
                
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                
                return True
                
        except Exception as e:
            print(f"Error adding images to {file_path}: {str(e)}")
            return False
    
    def audit_all_posts(self):
        """Audit all blog posts for image coverage"""
        blog_dir = self.content_dir / "blog"
        processed = 0
        
        print("Auditing all blog posts for actual image coverage...")
        
        for md_file in blog_dir.rglob("*.md"):
            if self.has_actual_images(md_file):
                self.posts_with_images += 1
            else:
                self.posts_without_images += 1
                
                # Extract slug from path
                slug = md_file.parent.name
                
                # Check WordPress for images
                wp_images = self.get_wordpress_images(slug)
                
                if wp_images:
                    print(f"📷 Found {len(wp_images)} images in WP for {slug}, adding...")
                    if self.add_wordpress_images_to_post(md_file, wp_images):
                        self.posts_with_images += 1
                        self.posts_without_images -= 1
                    else:
                        self.missing_posts.append({
                            'path': str(md_file),
                            'slug': slug,
                            'wp_images': wp_images
                        })
                else:
                    self.missing_posts.append({
                        'path': str(md_file),
                        'slug': slug,
                        'wp_images': []
                    })
            
            processed += 1
            if processed % 100 == 0:
                print(f"Processed {processed} posts... (With images: {self.posts_with_images}, Without: {self.posts_without_images})")
        
        # Final report
        total_posts = self.posts_with_images + self.posts_without_images
        coverage_percent = (self.posts_with_images / total_posts * 100) if total_posts > 0 else 0
        
        print(f"\n" + "="*60)
        print(f"COMPREHENSIVE IMAGE AUDIT RESULTS")
        print(f"="*60)
        print(f"Total posts processed: {total_posts}")
        print(f"Posts with images: {self.posts_with_images}")
        print(f"Posts without images: {self.posts_without_images}")
        print(f"Actual image coverage: {coverage_percent:.1f}%")
        print(f"Posts still missing images: {len(self.missing_posts)}")
        
        # Save detailed report
        report_file = self.base_dir / "migration" / "comprehensive_image_audit.txt"
        with open(report_file, 'w') as f:
            f.write("COMPREHENSIVE IMAGE AUDIT REPORT\n")
            f.write("=" * 60 + "\n\n")
            f.write(f"Total posts: {total_posts}\n")
            f.write(f"Posts with images: {self.posts_with_images}\n")
            f.write(f"Posts without images: {self.posts_without_images}\n")
            f.write(f"Image coverage: {coverage_percent:.1f}%\n\n")
            
            f.write("POSTS STILL WITHOUT IMAGES:\n")
            f.write("-" * 40 + "\n")
            for post in self.missing_posts:
                f.write(f"Slug: {post['slug']}\n")
                f.write(f"Path: {post['path']}\n")
                f.write(f"WP Images Found: {len(post['wp_images'])}\n")
                if post['wp_images']:
                    for img in post['wp_images']:
                        f.write(f"  - {img}\n")
                f.write("\n")
        
        print(f"\nDetailed report saved to: {report_file}")
        
        return coverage_percent

if __name__ == "__main__":
    auditor = ImageAudit()
    final_coverage = auditor.audit_all_posts()
    
    if final_coverage < 95:
        print(f"\n⚠️  Warning: Image coverage is only {final_coverage:.1f}%")
        print("Consider adding placeholder images or checking WordPress database for missing images.")
    else:
        print(f"\n✅ Good image coverage: {final_coverage:.1f}%")