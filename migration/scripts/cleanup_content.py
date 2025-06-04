#!/usr/bin/env python3
"""
Content Cleanup Script for Gadjoy Hugo Migration
Cleans up escaped characters and improves formatting
"""

import os
import re
from pathlib import Path

class ContentCleanup:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        self.processed_count = 0
        
    def clean_content(self, content):
        """Clean up content formatting"""
        if not content:
            return ""
        
        # Remove escaped newlines and tabs
        content = content.replace('\\n\\n\\n', '\n\n')
        content = content.replace('\\n\\n', '\n\n')
        content = content.replace('\\n', '\n')
        content = content.replace('\\t', '')
        content = re.sub(r'\t+', '', content)
        
        # Clean up multiple consecutive newlines
        content = re.sub(r'\n{4,}', '\n\n\n', content)
        content = re.sub(r'\n{3}', '\n\n', content)
        
        # Fix image formatting
        content = re.sub(r'!\[Image\]\(([^)]+)\)\n', r'{{< figure src="\1" alt="Repair Image" class="img-responsive" >}}\n\n', content)
        
        # Clean up headings
        content = re.sub(r'#+\s*(Before|After)\s*#+', r'### \1', content)
        content = re.sub(r'#+\s*(Before)\s*', r'### Before', content)
        content = re.sub(r'#+\s*(After)\s*', r'### After', content)
        
        # Add proper spacing around headings
        content = re.sub(r'\n(#{1,6}[^#\n]+)\n', r'\n\n\1\n\n', content)
        
        # Clean up bullet points
        content = re.sub(r'^-\s*([^-\n]+)', r'- \1', content, flags=re.MULTILINE)
        
        # Remove extra spaces
        content = re.sub(r' +', ' ', content)
        content = re.sub(r'\n +', '\n', content)
        
        # Clean up HTML entities and leftover tags
        content = content.replace('&gt;', '>')
        content = content.replace('&lt;', '<')
        content = content.replace('&amp;', '&')
        
        # Add proper introduction for repair posts
        if not content.strip():
            return "This device came to us for professional repair service. Our technicians successfully diagnosed and fixed the issue."
        
        # If content is very short, enhance it
        if len(content.strip()) < 50:
            device_info = ""
            if "iPhone" in content or "Apple" in content:
                device_info = "Apple device"
            elif "Samsung" in content:
                device_info = "Samsung device"
            elif "Redmi" in content or "Mi " in content:
                device_info = "Xiaomi device"
            else:
                device_info = "Device"
                
            content = f"{content.strip()}\n\nThis {device_info} was successfully repaired by our expert technicians. We provide professional repair services with quality parts and warranty."
        
        return content.strip()
    
    def add_banner_to_frontmatter(self, content, title):
        """Add banner image based on device type"""
        banner = "img/banners/banner-4.jpg"  # default
        
        if any(x in title.lower() for x in ['iphone', 'ipad', 'apple']):
            banner = "img/banners/banner-1.jpg"
        elif any(x in title.lower() for x in ['samsung', 'galaxy']):
            banner = "img/banners/banner-2.jpg"
        elif any(x in title.lower() for x in ['redmi', 'mi ', 'poco']):
            banner = "img/banners/banner-3.jpg"
        elif any(x in title.lower() for x in ['dell', 'hp', 'lenovo', 'laptop']):
            banner = "img/banners/banner-5.jpg"
        
        # Add banner to frontmatter
        lines = content.split('\n')
        frontmatter_end = -1
        for i, line in enumerate(lines):
            if line.strip() == '---' and i > 0:
                frontmatter_end = i
                break
        
        if frontmatter_end > 0:
            lines.insert(frontmatter_end, f'banner: "{banner}"')
            return '\n'.join(lines)
        
        return content
    
    def process_blog_posts(self):
        """Process all blog post files"""
        blog_dir = self.content_dir / "blog"
        
        for md_file in blog_dir.rglob("*.md"):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Split frontmatter and content
                parts = content.split('---')
                if len(parts) >= 3:
                    frontmatter = '---' + parts[1] + '---'
                    body = '---'.join(parts[2:])
                    
                    # Extract title for banner selection
                    title_match = re.search(r'title:\s*"([^"]+)"', frontmatter)
                    title = title_match.group(1) if title_match else ""
                    
                    # Clean the body content
                    clean_body = self.clean_content(body)
                    
                    # Reconstruct with banner
                    new_content = frontmatter + '\n\n' + clean_body
                    new_content = self.add_banner_to_frontmatter(new_content, title)
                    
                    # Write back
                    with open(md_file, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    
                    self.processed_count += 1
                    if self.processed_count % 100 == 0:
                        print(f"Processed {self.processed_count} files...")
                        
            except Exception as e:
                print(f"Error processing {md_file}: {str(e)}")
    
    def process_service_pages(self):
        """Clean up service pages"""
        service_files = [
            self.content_dir / "services" / "we-repair" / "index.md",
            self.content_dir / "services" / "we-build" / "index.md",
            self.content_dir / "contact" / "index.md",
            self.content_dir / "gallery" / "index.md",
            self.content_dir / "_index.md"
        ]
        
        for file_path in service_files:
            if file_path.exists():
                try:
                    with open(file_path, 'r', encoding='utf-8') as f:
                        content = f.read()
                    
                    # Split frontmatter and content
                    parts = content.split('---')
                    if len(parts) >= 3:
                        frontmatter = '---' + parts[1] + '---'
                        body = '---'.join(parts[2:])
                        
                        # Clean the body content
                        clean_body = self.clean_content(body)
                        
                        # Reconstruct
                        new_content = frontmatter + '\n\n' + clean_body
                        
                        # Write back
                        with open(file_path, 'w', encoding='utf-8') as f:
                            f.write(new_content)
                        
                        print(f"Cleaned {file_path.name}")
                        
                except Exception as e:
                    print(f"Error processing {file_path}: {str(e)}")
    
    def run_cleanup(self):
        """Run the complete cleanup process"""
        print("Starting content cleanup...")
        
        print("Processing blog posts...")
        self.process_blog_posts()
        
        print("Processing service pages...")
        self.process_service_pages()
        
        print(f"Cleanup completed! Processed {self.processed_count} blog posts.")

if __name__ == "__main__":
    cleanup = ContentCleanup()
    cleanup.run_cleanup()