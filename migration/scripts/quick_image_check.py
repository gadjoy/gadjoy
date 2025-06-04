#!/usr/bin/env python3
"""
Quick check for actual image coverage in specific posts
"""

import os
import re
import subprocess
from pathlib import Path

class QuickImageCheck:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        
    def has_actual_images(self, file_path):
        """Check if post has actual images (not just placeholders)"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Look for actual image markup (not placeholders)
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
            return False
    
    def get_post_by_slug(self, slug):
        """Find post file by slug"""
        for md_file in self.content_dir.rglob("*.md"):
            if md_file.parent.name == slug:
                return md_file
        return None
    
    def check_specific_posts(self, slugs):
        """Check specific posts for images"""
        results = {}
        
        for slug in slugs:
            post_file = self.get_post_by_slug(slug)
            if post_file:
                has_images = self.has_actual_images(post_file)
                results[slug] = {
                    'file': str(post_file),
                    'has_images': has_images
                }
                print(f"{slug}: {'✅ HAS IMAGES' if has_images else '❌ NO IMAGES'}")
            else:
                print(f"{slug}: NOT FOUND")
                results[slug] = {'file': None, 'has_images': False}
        
        return results
    
    def quick_sample_check(self, sample_size=50):
        """Quick check of a sample of posts"""
        blog_dir = self.content_dir / "blog"
        all_posts = list(blog_dir.rglob("*.md"))
        
        print(f"\nQuick sample check of {min(sample_size, len(all_posts))} posts...")
        
        with_images = 0
        without_images = 0
        
        sample_posts = all_posts[:sample_size]
        
        for post_file in sample_posts:
            if self.has_actual_images(post_file):
                with_images += 1
            else:
                without_images += 1
                print(f"❌ No images: {post_file.parent.name}")
        
        total = with_images + without_images
        coverage = (with_images / total * 100) if total > 0 else 0
        
        print(f"\nSample Results:")
        print(f"Posts with images: {with_images}/{total}")
        print(f"Sample coverage: {coverage:.1f}%")
        
        return coverage

if __name__ == "__main__":
    checker = QuickImageCheck()
    
    # Check the specific posts mentioned
    print("Checking specific posts mentioned:")
    specific_slugs = [
        'poco-x3-pro-dead',
        'redmi-y3-dead', 
        'samsung-galaxy-m51-dead'
    ]
    
    results = checker.check_specific_posts(specific_slugs)
    
    # Quick sample check
    sample_coverage = checker.quick_sample_check(100)
    
    print(f"\n" + "="*50)
    print(f"QUICK IMAGE AUDIT SUMMARY")
    print(f"="*50)
    print(f"Specific posts checked: {len(specific_slugs)}")
    print(f"Sample coverage estimate: {sample_coverage:.1f}%")
    
    if sample_coverage < 80:
        print("⚠️  Low image coverage detected!")
        print("Many posts likely missing actual images despite migration.")
    elif sample_coverage < 95:
        print("⚠️  Moderate image coverage - some posts missing images.")
    else:
        print("✅ Good image coverage in sample.")