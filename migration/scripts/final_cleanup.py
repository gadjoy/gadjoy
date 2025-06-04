#!/usr/bin/env python3
"""
Final content cleanup for better formatting
"""

import os
import re
from pathlib import Path

class FinalCleanup:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        
    def fix_before_after_headings(self, content):
        """Fix Before/After heading formatting"""
        # Fix merged BeforeAfter headings
        content = re.sub(r'### BeforeAfter', '### Before\n\n[Image showing device before repair]\n\n### After\n\n[Image showing device after repair]', content)
        
        # Fix standalone Before/After without proper spacing
        content = re.sub(r'### Before\s*### After', '### Before\n\n[Image showing device condition before repair]\n\n### After\n\n[Image showing completed repair]', content)
        
        return content
    
    def enhance_short_content(self, content, title):
        """Enhance very short content with more details"""
        if len(content.strip()) < 100:
            # Extract device and issue from title
            device_type = "device"
            issue_type = "repair"
            
            if any(x in title.lower() for x in ['iphone', 'ipad']):
                device_type = "Apple device"
            elif any(x in title.lower() for x in ['samsung', 'galaxy']):
                device_type = "Samsung device"
            elif any(x in title.lower() for x in ['redmi', 'mi ', 'poco']):
                device_type = "Xiaomi device"
            elif any(x in title.lower() for x in ['dell', 'hp', 'lenovo']):
                device_type = "laptop"
            
            if any(x in title.lower() for x in ['display', 'screen']):
                issue_type = "display issue"
            elif any(x in title.lower() for x in ['battery']):
                issue_type = "battery problem"
            elif any(x in title.lower() for x in ['dead', 'not switching']):
                issue_type = "power issue"
            
            enhanced_content = f"""## Repair Summary

This {device_type} came to us with a {issue_type}. Our experienced technicians performed a thorough diagnosis and successfully completed the repair.

### Service Details

- **Device:** {title.split(' - ')[0] if ' - ' in title else title}
- **Issue:** {title.split(' - ')[1] if ' - ' in title else issue_type.title()}
- **Status:** Successfully Repaired
- **Warranty:** 30-day service warranty included

### Our Process

1. **Initial Diagnosis:** Thorough examination to identify the root cause
2. **Quotation:** Transparent pricing provided before starting work
3. **Repair:** Using genuine parts and professional techniques
4. **Quality Check:** Comprehensive testing before device return
5. **Warranty:** 30-day guarantee on all repair work

{content}

---

*Need a similar repair? Contact Gadjoy Repair Service for professional electronics repair in Bangalore.*"""
            
            return enhanced_content
            
        return content
    
    def process_all_posts(self):
        """Process all blog posts for final cleanup"""
        blog_dir = self.content_dir / "blog"
        processed_count = 0
        
        for md_file in blog_dir.rglob("*.md"):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Split frontmatter and content
                parts = content.split('---')
                if len(parts) >= 3:
                    frontmatter = '---' + parts[1] + '---'
                    body = '---'.join(parts[2:])
                    
                    # Extract title
                    title_match = re.search(r'title:\s*"([^"]+)"', frontmatter)
                    title = title_match.group(1) if title_match else ""
                    
                    # Apply fixes
                    body = self.fix_before_after_headings(body)
                    body = self.enhance_short_content(body, title)
                    
                    # Reconstruct
                    new_content = frontmatter + '\n\n' + body
                    
                    # Write back
                    with open(md_file, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    
                    processed_count += 1
                    if processed_count % 200 == 0:
                        print(f"Enhanced {processed_count} posts...")
                        
            except Exception as e:
                print(f"Error processing {md_file}: {str(e)}")
        
        print(f"Final cleanup completed! Enhanced {processed_count} posts.")
    
    def fix_homepage(self):
        """Fix homepage content formatting"""
        homepage_file = self.content_dir / "_index.md"
        
        if homepage_file.exists():
            try:
                with open(homepage_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Split frontmatter and content
                parts = content.split('---')
                if len(parts) >= 3:
                    frontmatter = '---' + parts[1] + '---'
                    
                    # Create better homepage content
                    new_body = """
## Welcome to Gadjoy Repair Service

At our store, we specialize in repairing a wide range of gadgets, including phones, tablets, laptops, and desktops. While we have a particular expertise in Apple products, our skilled technicians are well-equipped to handle repairs for various other brands as well.

With years of experience and a passion for technology, we ensure that every device is treated with the utmost care and precision. Whether it's a cracked screen, battery issues, or software glitches, we're here to restore your gadgets to peak performance.

## Our Focus Areas

### 🔧 Repair Services
- Laptops & MacBooks
- Tablets & iPads  
- Mobile Phones
- Desktop Computers

### 💻 Development Services
- AI & Machine Learning
- Freelance Development
- Quantitative Analysis
- Engineering Projects
- Website Building

### 🖨️ 3D Printing
- Artistic Projects
- Hobby Items
- Custom Gadgets

## Why Choose Gadjoy?

### Experienced Technicians
Our team consists of highly skilled professionals who understand the intricacies of repair work. With extensive experience, they efficiently execute tasks with precision, ensuring quick turnaround times without compromising on quality.

### Clear Communication
We believe that clear communication is key to a successful repair experience. Our team keeps you informed every step of the way, ensuring you understand the repair process, timelines, and any concerns you may have.

### Problem Resolution
At Gadjoy Repair Services, we are committed to resolving any issue your device may have—no matter the problem or device type! Our skilled technicians assess each situation to determine the best course of action.

## Location

**Gadjoy Repair Services**  
#300, Adj D'Mart, Varthur Rd  
Siddapura, Karnataka 560066  
Bangalore, India

📞 Contact us for all your device repair needs  
📧 Professional service with warranty included
"""
                    
                    # Reconstruct
                    new_content = frontmatter + new_body
                    
                    # Write back
                    with open(homepage_file, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    
                    print("Homepage content improved!")
                    
            except Exception as e:
                print(f"Error processing homepage: {str(e)}")

if __name__ == "__main__":
    cleanup = FinalCleanup()
    print("Starting final content enhancement...")
    cleanup.fix_homepage()
    cleanup.process_all_posts()