#!/usr/bin/env python3
"""
Clean up blog content issues:
1. Remove repetitive repair process text
2. Fix titles (remove "Professional Service")
3. Check for missing images
"""

import os
import re
from pathlib import Path

class BlogContentCleanup:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        self.posts_without_images = []
        self.posts_fixed = 0
        
    def clean_blog_post(self, file_path):
        """Clean individual blog post"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            
            # Split frontmatter and content
            parts = content.split('---')
            if len(parts) >= 3:
                frontmatter = parts[1]
                body = '---'.join(parts[2:])
                
                # Fix title in frontmatter - remove "Professional Service"
                title_match = re.search(r'title:\s*"([^"]+)"', frontmatter)
                if title_match:
                    original_title = title_match.group(1)
                    # Remove "- Professional Service" or "Professional Service"
                    cleaned_title = re.sub(r'\s*-?\s*Professional Service$', '', original_title)
                    frontmatter = frontmatter.replace(f'title: "{original_title}"', f'title: "{cleaned_title}"')
                
                # Remove repetitive repair process sections from body
                repair_process_pattern = r'### Our Repair Process\s*\n\n1\. \*\*Free Diagnosis\*\*.*?5\. \*\*Warranty\*\* - 30-day warranty on all repair work\s*\n\n### Why Choose Gadjoy for.*?repairs\?\s*\n\n- \*\*Expert Technicians\*\*.*?\n\n---\s*\n\n\*Having issues with your.*?\*'
                
                if re.search(repair_process_pattern, body, re.DOTALL):
                    body = re.sub(repair_process_pattern, '', body, flags=re.DOTALL)
                    body = body.strip()
                
                # Check if post has images
                has_images = bool(re.search(r'!\[.*?\]\(.*?\)|{{<\s*figure.*?}}|\[Image.*?\]', body))
                if not has_images:
                    self.posts_without_images.append(str(file_path))
                
                # Reconstruct content
                new_content = '---' + frontmatter + '---\n\n' + body
                
                # Only write if content changed
                if new_content != original_content:
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    self.posts_fixed += 1
                    
        except Exception as e:
            print(f"Error processing {file_path}: {str(e)}")
    
    def process_all_posts(self):
        """Process all blog posts"""
        blog_dir = self.content_dir / "blog"
        total_posts = 0
        
        for md_file in blog_dir.rglob("*.md"):
            self.clean_blog_post(md_file)
            total_posts += 1
            
            if total_posts % 300 == 0:
                print(f"Processed {total_posts} posts...")
        
        print(f"\nCleanup completed!")
        print(f"Total posts processed: {total_posts}")
        print(f"Posts fixed: {self.posts_fixed}")
        print(f"Posts without images: {len(self.posts_without_images)}")
        
        # Write posts without images to file
        if self.posts_without_images:
            log_file = self.base_dir / "migration" / "posts_without_images.txt"
            with open(log_file, 'w') as f:
                f.write("Blog posts without images:\n")
                f.write("=" * 40 + "\n\n")
                for post in self.posts_without_images:
                    # Extract meaningful path
                    rel_path = post.replace(str(self.content_dir), "content")
                    f.write(f"{rel_path}\n")
            
            print(f"Posts without images saved to: {log_file}")
    
    def add_missing_images_placeholders(self):
        """Add placeholder content for posts without images"""
        placeholder_content = """
### Before

The device was brought to us with the reported issue requiring professional attention.

### After

After thorough diagnosis and expert repair work, the device was restored to full working condition.
"""
        
        added_placeholders = 0
        for post_path in self.posts_without_images[:50]:  # Limit to first 50 for testing
            try:
                with open(post_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Check if it's a very short post
                parts = content.split('---')
                if len(parts) >= 3:
                    body = '---'.join(parts[2:]).strip()
                    
                    if len(body) < 100:  # Very short content
                        # Add placeholder content
                        new_body = body + placeholder_content
                        new_content = '---' + parts[1] + '---\n\n' + new_body
                        
                        with open(post_path, 'w', encoding='utf-8') as f:
                            f.write(new_content)
                        
                        added_placeholders += 1
                        
            except Exception as e:
                print(f"Error adding placeholder to {post_path}: {str(e)}")
        
        print(f"Added placeholders to {added_placeholders} posts")

if __name__ == "__main__":
    cleanup = BlogContentCleanup()
    cleanup.process_all_posts()
    
    # Ask if user wants to add placeholders
    if cleanup.posts_without_images:
        print(f"\nFound {len(cleanup.posts_without_images)} posts without images.")
        print("These have been logged to migration/posts_without_images.txt")
        
        # Optionally add placeholders for very short posts
        cleanup.add_missing_images_placeholders()