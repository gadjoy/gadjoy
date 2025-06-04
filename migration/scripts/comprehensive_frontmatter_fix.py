#!/usr/bin/env python3
"""
Comprehensive frontmatter fix
"""

import os
import re
from pathlib import Path

class ComprehensiveFrontmatterFix:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        
    def fix_all_frontmatter(self):
        """Fix all frontmatter issues"""
        blog_dir = self.content_dir / "blog"
        fixed_count = 0
        
        for md_file in blog_dir.rglob("*.md"):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Find the pattern where --- is attached to the summary line
                if '."---' in content or 'included."---' in content:
                    # Fix by adding newline before ---
                    content = re.sub(r'(summary: "[^"]+")---', r'\1\n---', content)
                    
                    with open(md_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    
                    fixed_count += 1
                        
            except Exception as e:
                print(f"Error fixing {md_file}: {str(e)}")
        
        print(f"Fixed frontmatter formatting in {fixed_count} files.")

if __name__ == "__main__":
    fixer = ComprehensiveFrontmatterFix()
    fixer.fix_all_frontmatter()