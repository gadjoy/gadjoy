#!/usr/bin/env python3
"""
Fix image paths that have double path elements
"""

import os
import re
from pathlib import Path

class ImagePathFixer:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        self.fixed_count = 0
        
    def fix_image_paths(self):
        """Fix image paths with double path elements"""
        blog_dir = self.content_dir / "blog"
        
        for md_file in blog_dir.rglob("*.md"):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                original_content = content
                
                # Fix double path issues
                content = re.sub(
                    r'src="/img/uploads/http://uploads/',
                    'src="/img/uploads/',
                    content
                )
                
                content = re.sub(
                    r'src="/uploads/http://uploads/',
                    'src="/img/uploads/',
                    content
                )
                
                content = re.sub(
                    r'src="http://uploads/',
                    'src="/img/uploads/',
                    content
                )
                
                # Fix any remaining double slashes
                content = re.sub(
                    r'src="/img/uploads//+',
                    'src="/img/uploads/',
                    content
                )
                
                if content != original_content:
                    with open(md_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    self.fixed_count += 1
                    
            except Exception as e:
                print(f"Error fixing {md_file}: {str(e)}")
        
        print(f"Fixed image paths in {self.fixed_count} files.")

if __name__ == "__main__":
    fixer = ImagePathFixer()
    fixer.fix_image_paths()