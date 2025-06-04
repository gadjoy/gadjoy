#!/usr/bin/env python3
"""
Universal Theme Optimization Script
Formats content to match Universal theme expectations
"""

import os
import re
from pathlib import Path
import random

class UniversalThemeOptimizer:
    def __init__(self):
        self.base_dir = Path(__file__).parent.parent.parent
        self.content_dir = self.base_dir / "content"
        self.data_dir = self.base_dir / "data"
        
    def optimize_blog_posts(self):
        """Optimize blog posts for Universal theme"""
        blog_dir = self.content_dir / "blog"
        processed = 0
        
        for md_file in blog_dir.rglob("*.md"):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Split frontmatter and content
                parts = content.split('---')
                if len(parts) >= 3:
                    frontmatter_content = parts[1]
                    body = '---'.join(parts[2:])
                    
                    # Extract existing frontmatter data
                    title_match = re.search(r'title:\s*"([^"]+)"', frontmatter_content)
                    title = title_match.group(1) if title_match else ""
                    
                    # Add missing Universal theme frontmatter fields
                    if 'author:' not in frontmatter_content:
                        frontmatter_content += '\nauthor: "Gadjoy Repair Team"'
                    
                    if 'summary:' not in frontmatter_content and title:
                        # Create intelligent summary based on title
                        device = title.split(' - ')[0] if ' - ' in title else title.split(' ')[0]
                        issue = title.split(' - ')[1] if ' - ' in title else "repair service"
                        summary = f"Professional {issue.lower()} for {device}. Expert technicians, quality parts, warranty included."
                        frontmatter_content += f'\nsummary: "{summary}"'
                    
                    # Ensure description is filled
                    if 'description: ""' in frontmatter_content and title:
                        device = title.split(' - ')[0] if ' - ' in title else title.split(' ')[0]
                        description = f"Professional repair service for {device}. Expert diagnosis and quality repairs in Bangalore."
                        frontmatter_content = frontmatter_content.replace('description: ""', f'description: "{description}"')
                    
                    # Enhance body content for very short posts
                    if len(body.strip()) < 200 and title:
                        enhanced_body = self.create_enhanced_repair_content(title, body)
                        body = enhanced_body
                    
                    # Reconstruct content
                    new_content = '---' + frontmatter_content + '---\n\n' + body
                    
                    with open(md_file, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    
                    processed += 1
                    if processed % 300 == 0:
                        print(f"Optimized {processed} blog posts...")
                        
            except Exception as e:
                print(f"Error optimizing {md_file}: {str(e)}")
        
        print(f"Blog optimization completed! Processed {processed} posts.")
    
    def create_enhanced_repair_content(self, title, existing_content):
        """Create enhanced content for repair posts"""
        device = title.split(' - ')[0] if ' - ' in title else title.split(' ')[0]
        issue = title.split(' - ')[1] if ' - ' in title else "general repair"
        
        enhanced = f"""## {device} {issue.title()} - Professional Service

{existing_content.strip()}

### Our Repair Process

1. **Free Diagnosis** - We thoroughly examine your device to identify all issues
2. **Transparent Quote** - Clear pricing with no hidden costs
3. **Quality Repair** - Using genuine parts and professional techniques  
4. **Testing** - Comprehensive functionality testing before return
5. **Warranty** - 30-day warranty on all repair work

### Why Choose Gadjoy for {device} Repairs?

- **Expert Technicians** - Specialized in {device.split()[0] if device.split() else device} repairs
- **Quick Turnaround** - Most repairs completed within 24-48 hours
- **Quality Parts** - We use only genuine or high-quality compatible parts
- **Competitive Pricing** - Fair and transparent pricing
- **Warranty Coverage** - All repairs backed by our service guarantee

---

*Having issues with your {device.lower()}? Visit Gadjoy Repair Service in Bangalore for professional electronics repair.*
"""
        return enhanced
    
    def create_homepage_sections(self):
        """Create data files for Universal theme homepage sections"""
        # Create data directory
        self.data_dir.mkdir(exist_ok=True)
        
        # Create carousel data
        carousel_data = """---
weight: 1
title: "Professional Electronics Repair"
description: "Expert repair services for phones, tablets, laptops, and desktops in Bangalore"
image: "img/banners/banner-1.jpg"
---

---
weight: 2  
title: "Quick Turnaround Time"
description: "Most repairs completed within 24-48 hours with quality guarantee"
image: "img/banners/banner-2.jpg"
---

---
weight: 3
title: "30-Day Warranty"
description: "All repairs backed by our comprehensive service warranty"
image: "img/banners/banner-3.jpg"
---
"""
        
        with open(self.data_dir / "carousel.yaml", 'w') as f:
            f.write(carousel_data)
        
        # Create features data
        features_data = """---
weight: 1
name: "Expert Technicians"
icon: "fa fa-tools"
description: "Highly skilled professionals with years of electronics repair experience"
---

---
weight: 2
name: "Quality Parts"
description: "We use only genuine or high-quality compatible replacement parts"
icon: "fa fa-check-circle"
---

---
weight: 3
name: "Fast Service"
description: "Quick diagnosis and repair with most devices ready within 24-48 hours"
icon: "fa fa-clock"
---

---
weight: 4
name: "Warranty Included"
description: "30-day comprehensive warranty on all repair services"
icon: "fa fa-shield-alt"
---

---
weight: 5
name: "Transparent Pricing"
description: "Clear, upfront pricing with no hidden costs or surprise fees"
icon: "fa fa-dollar-sign"
---

---
weight: 6
name: "Free Diagnosis"
description: "Complimentary device diagnosis to identify all issues before repair"
icon: "fa fa-search"
---
"""
        
        with open(self.data_dir / "features.yaml", 'w') as f:
            f.write(features_data)
        
        # Create testimonials data
        testimonials_data = """---
text: "Excellent service! My iPhone was repaired quickly and works perfectly. The staff was very professional and the pricing was fair."
name: "Rahul K."
position: "Customer"
avatar: "img/testimonials/person-1.jpg"
---

---
text: "Great experience with laptop repair. They fixed my Dell laptop's display issue in just one day. Highly recommended!"
name: "Priya S."
position: "Customer"  
avatar: "img/testimonials/person-2.jpg"
---

---
text: "Professional service for my Samsung phone. The battery replacement was done perfectly and the warranty gives me confidence."
name: "Amit M."
position: "Customer"
avatar: "img/testimonials/person-3.jpg"
---

---
text: "Best repair service in Bangalore. They fixed my MacBook when others couldn't. Great work and reasonable prices."
name: "Sneha R."
position: "Customer"
avatar: "img/testimonials/person-4.jpg"
---
"""
        
        with open(self.data_dir / "testimonials.yaml", 'w') as f:
            f.write(testimonials_data)
        
        print("Created Universal theme data files!")
    
    def optimize_service_pages(self):
        """Optimize service pages for Universal theme"""
        # We Repair page
        we_repair_content = """---
title: "We Repair"
description: "Professional electronics repair services for all major brands and devices"
---

## Professional Electronics Repair Services

At Gadjoy Repair Service, we specialize in comprehensive electronics repair for all your devices. Our expert technicians handle everything from smartphones to laptops with precision and care.

### Device Categories We Service

#### 📱 Mobile Phones & Smartphones
- **iPhone Repairs** - Display, battery, charging port, camera, water damage
- **Samsung Galaxy** - Screen replacement, software issues, hardware repairs  
- **Xiaomi/Redmi** - Display combos, dead device recovery, battery replacement
- **OnePlus, Realme, Vivo, Oppo** - All hardware and software issues

#### 💻 Laptops & MacBooks  
- **Display Repairs** - Cracked screens, LCD replacement, hinge repairs
- **Hardware Issues** - Battery, keyboard, charging port, motherboard
- **Software Services** - OS installation, virus removal, data recovery
- **Performance Upgrades** - RAM upgrade, SSD installation, general service

#### 📟 Tablets & iPads
- **Display Replacement** - Cracked glass, LCD issues, touch problems
- **Battery Service** - Power issues, charging problems
- **Software Repair** - Operating system, app issues, factory reset

#### 🖥️ Desktop Computers
- **Hardware Diagnosis** - Power supply, motherboard, component issues
- **Software Services** - Windows installation, driver updates, optimization
- **Upgrade Services** - RAM, storage, graphics card installation

### Our Repair Process

1. **Free Diagnosis** 📋
   - Comprehensive device examination
   - Issue identification and explanation
   - Transparent cost estimate

2. **Professional Repair** 🔧
   - Quality parts and components
   - Expert technician service
   - Clean, organized workspace

3. **Quality Testing** ✅
   - Full functionality verification
   - Performance optimization
   - Safety and reliability checks

4. **Warranty Coverage** 🛡️
   - 30-day service warranty
   - Parts warranty included
   - Support and follow-up

### Why Choose Our Repair Services?

- ⚡ **Quick Turnaround** - Most repairs completed in 24-48 hours
- 🎯 **Expert Technicians** - Years of specialized repair experience  
- 💎 **Quality Parts** - Genuine and premium compatible components
- 💰 **Fair Pricing** - Competitive rates with transparent quotes
- 🏆 **Proven Results** - Thousands of successful repairs completed
- 📍 **Convenient Location** - Easy access in Bangalore

---

*Ready to get your device repaired? Contact us today for a free diagnosis and quote!*
"""
        
        we_repair_file = self.content_dir / "services" / "we-repair" / "index.md"
        we_repair_file.parent.mkdir(parents=True, exist_ok=True)
        with open(we_repair_file, 'w', encoding='utf-8') as f:
            f.write(we_repair_content)
        
        print("Optimized service pages!")
    
    def run_optimization(self):
        """Run complete Universal theme optimization"""
        print("Starting Universal theme optimization...")
        
        print("Creating homepage sections...")
        self.create_homepage_sections()
        
        print("Optimizing service pages...")
        self.optimize_service_pages()
        
        print("Optimizing blog posts...")
        self.optimize_blog_posts()
        
        print("Universal theme optimization completed!")

if __name__ == "__main__":
    optimizer = UniversalThemeOptimizer()
    optimizer.run_optimization()