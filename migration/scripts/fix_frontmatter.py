#!/usr/bin/env python3
"""
Fix frontmatter formatting issues
"""

import os
import re
from pathlib import Path

class FrontmatterFixer:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        
    def fix_blog_frontmatter(self):
        """Fix frontmatter formatting in blog posts"""
        blog_dir = self.content_dir / "blog"
        fixed_count = 0
        
        for md_file in blog_dir.rglob("*.md"):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Look for malformed frontmatter
                if '---\n\n' in content and content.count('---') == 2:
                    # Fix the frontmatter
                    parts = content.split('---')
                    if len(parts) >= 3:
                        frontmatter = parts[1].strip()
                        body = '---'.join(parts[2:]).strip()
                        
                        # Reconstruct with proper formatting
                        new_content = f"---\n{frontmatter}\n---\n\n{body}"
                        
                        with open(md_file, 'w', encoding='utf-8') as f:
                            f.write(new_content)
                        
                        fixed_count += 1
                        
            except Exception as e:
                print(f"Error fixing {md_file}: {str(e)}")
        
        print(f"Fixed frontmatter in {fixed_count} files.")

if __name__ == "__main__":
    fixer = FrontmatterFixer()
    fixer.fix_blog_frontmatter()