#!/usr/bin/env python3
"""
Final blog cleanup script
"""

import os
import re
from pathlib import Path

class FinalBlogCleanup:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        
    def clean_post_content(self, file_path):
        """Clean individual post content"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Split frontmatter and content
            parts = content.split('---')
            if len(parts) >= 3:
                frontmatter = parts[1]
                body = '---'.join(parts[2:])
                
                # Remove "Professional Service" from content headings
                body = re.sub(r'## ([^-]+) - Professional Service', r'## \1', body)
                body = re.sub(r'# ([^-]+) - Professional Service', r'# \1', body)
                
                # Remove the repair process sections completely
                repair_process_section = r'### Our Repair Process.*?(?=###|---|\Z)'
                body = re.sub(repair_process_section, '', body, flags=re.DOTALL)
                
                # Remove "Why Choose Gadjoy" sections
                why_choose_section = r'### Why Choose Gadjoy.*?(?=###|---|\Z)'
                body = re.sub(why_choose_section, '', body, flags=re.DOTALL)
                
                # Remove footer messages
                footer_pattern = r'\*Having issues with your.*?\*'
                body = re.sub(footer_pattern, '', body, flags=re.DOTALL)
                
                # Clean up multiple newlines
                body = re.sub(r'\n{3,}', '\n\n', body)
                body = body.strip()
                
                # Reconstruct content
                new_content = '---' + frontmatter + '---\n\n' + body
                
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                    
        except Exception as e:
            print(f"Error processing {file_path}: {str(e)}")
    
    def process_all_posts(self):
        """Process all blog posts"""
        blog_dir = self.content_dir / "blog"
        processed = 0
        
        for md_file in blog_dir.rglob("*.md"):
            self.clean_post_content(md_file)
            processed += 1
            
            if processed % 300 == 0:
                print(f"Cleaned {processed} posts...")
        
        print(f"Final cleanup completed! Processed {processed} posts.")

if __name__ == "__main__":
    cleanup = FinalBlogCleanup()
    cleanup.process_all_posts()