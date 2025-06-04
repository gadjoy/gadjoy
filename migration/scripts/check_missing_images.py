#!/usr/bin/env python3
"""
Check WordPress database for images associated with posts without images
"""

import os
import re
import subprocess
from pathlib import Path

class ImageChecker:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.missing_images_file = self.base_dir / "migration" / "posts_without_images.txt"
        self.found_images = []
        self.truly_missing = []
        
    def get_post_id_from_slug(self, slug):
        """Get WordPress post ID from slug"""
        try:
            cmd = [
                "docker", "exec", "gadjoy-db-1", "mysql", 
                "-u", "root", "-proot_password", "wordpress", 
                "-e", f"""
                SELECT ID, post_title FROM wp_posts 
                WHERE post_name = '{slug}' AND post_status = 'publish'
                LIMIT 1;
                """
            ]
            
            result = subprocess.run(cmd, capture_output=True, text=False)
            output = result.stdout.decode('utf-8', errors='ignore')
            
            lines = output.strip().split('\n')
            if len(lines) >= 2:
                data = lines[1].split('\t')
                if len(data) >= 1:
                    return data[0]  # Return post ID
            return None
            
        except Exception as e:
            print(f"Error getting post ID for {slug}: {str(e)}")
            return None
    
    def check_post_images(self, post_id):
        """Check if post has associated images in WordPress"""
        try:
            # Check wp_postmeta for featured image
            cmd = [
                "docker", "exec", "gadjoy-db-1", "mysql", 
                "-u", "root", "-proot_password", "wordpress", 
                "-e", f"""
                SELECT meta_value FROM wp_postmeta 
                WHERE post_id = {post_id} AND meta_key = '_thumbnail_id'
                LIMIT 1;
                """
            ]
            
            result = subprocess.run(cmd, capture_output=True, text=False)
            output = result.stdout.decode('utf-8', errors='ignore')
            
            if len(output.strip().split('\n')) >= 2:
                return True  # Has featured image
            
            # Check for images in post content
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
            output = result.stdout.decode('utf-8', errors='ignore')
            
            # Check if content contains image references
            if any(x in output.lower() for x in ['<img', 'wp-image', '.jpg', '.jpeg', '.png', '.gif']):
                return True
            
            return False
            
        except Exception as e:
            print(f"Error checking images for post {post_id}: {str(e)}")
            return False
    
    def process_missing_images(self):
        """Process the list of posts without images"""
        if not self.missing_images_file.exists():
            print("Missing images file not found!")
            return
        
        with open(self.missing_images_file, 'r') as f:
            lines = f.readlines()
        
        print("Checking WordPress database for missing images...")
        processed = 0
        
        for line in lines[3:]:  # Skip header lines
            if line.strip() and 'content/blog/' in line:
                # Extract slug from path
                path_parts = line.strip().split('/')
                if len(path_parts) >= 2:
                    slug = path_parts[-2]  # Get directory name (slug)
                    
                    post_id = self.get_post_id_from_slug(slug)
                    if post_id:
                        has_images = self.check_post_images(post_id)
                        
                        if has_images:
                            self.found_images.append({
                                'path': line.strip(),
                                'slug': slug,
                                'post_id': post_id
                            })
                            print(f"✅ Found images in WP for: {slug} (ID: {post_id})")
                        else:
                            self.truly_missing.append({
                                'path': line.strip(),
                                'slug': slug,
                                'post_id': post_id
                            })
                    
                    processed += 1
                    if processed % 20 == 0:
                        print(f"Processed {processed} posts...")
        
        print(f"\nResults:")
        print(f"Posts with images in WordPress: {len(self.found_images)}")
        print(f"Posts truly without images: {len(self.truly_missing)}")
        
        # Save results
        results_file = self.base_dir / "migration" / "image_check_results.txt"
        with open(results_file, 'w') as f:
            f.write("Image Check Results\n")
            f.write("=" * 40 + "\n\n")
            
            f.write(f"Posts with images found in WordPress database: {len(self.found_images)}\n")
            f.write("-" * 50 + "\n")
            for item in self.found_images:
                f.write(f"Slug: {item['slug']} (ID: {item['post_id']})\n")
                f.write(f"Path: {item['path']}\n\n")
            
            f.write(f"\nPosts truly without images: {len(self.truly_missing)}\n")
            f.write("-" * 50 + "\n")
            for item in self.truly_missing:
                f.write(f"Slug: {item['slug']} (ID: {item['post_id']})\n")
                f.write(f"Path: {item['path']}\n\n")
        
        print(f"Results saved to: {results_file}")

if __name__ == "__main__":
    checker = ImageChecker()
    checker.process_missing_images()