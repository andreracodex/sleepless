from playwright.sync_api import sync_playwright
import json
import re

with sync_playwright() as p:
    print("Launching browser...")
    browser = p.chromium.launch(channel='msedge', headless=True)
    page = browser.new_page(viewport={"width": 1400, "height": 900})
    print("Loading CollabPals Niche Finder...")
    page.goto('https://www.collabpals.com/tools/youtube-niche-finder', wait_until='networkidle', timeout=60000)
    
    # Extract all niche cards via browser evaluate
    data = page.evaluate('''() => {
        const h3s = Array.from(document.querySelectorAll('h3'));
        const results = [];
        
        for (const h3 of h3s) {
            const title = h3.innerText.trim();
            if (!title) continue;
            
            // Find container element that has score and other details
            let card = h3;
            for (let i = 0; i < 5; i++) {
                if (!card.parentElement) break;
                card = card.parentElement;
                if (card.innerText.includes('Score') || card.innerText.includes('CPM') || card.innerText.includes('Competition')) {
                    break;
                }
            }
            
            const text = card.innerText;
            const lines = text.split('\\n').map(l => l.trim()).filter(l => l.length > 0);
            
            results.push({
                title: title,
                full_text: text,
                lines: lines
            });
        }
        
        return results;
    }''')
    
    print(f"Extracted raw data for {len(data)} niches.")
    
    parsed_niches = []
    for item in data:
        title = item['title']
        lines = item['lines']
        text = item['full_text']
        
        # Score is typically the first number or appears before/after title
        score = None
        for l in lines[:5]:
            if l.isdigit() and 20 <= int(l) <= 100:
                score = int(l)
                break
                
        # Category (often the line right below title)
        category = "General"
        try:
            title_idx = lines.index(title)
            if title_idx + 1 < len(lines):
                next_l = lines[title_idx + 1]
                if next_l not in ['Low', 'Medium', 'High', 'Rising', 'Stable', 'Faceless']:
                    category = next_l
        except ValueError:
            pass

        # Competition
        comp_match = re.search(r'(Low|Medium|High)\s*Competition', text, re.IGNORECASE)
        competition = comp_match.group(1).title() if comp_match else "Low"

        # CPM Range
        cpm_match = re.search(r'(\$[\d\.\,]+\s*-\s*\$[\d\.\,]+)\s*CPM', text, re.IGNORECASE)
        cpm_range = cpm_match.group(1).replace(' ', '') if cpm_match else "$5-$15"

        # Views per 1k subs
        views_match = re.search(r'(\d+)\s*Views per 1K Subs', text, re.IGNORECASE)
        views_per_1k = int(views_match.group(1)) if views_match else 30

        # Faceless
        faceless = "Faceless" in text

        # Equipment
        equipment = "Basic"
        if "Intermediate" in text:
            equipment = "Intermediate"
        elif "Advanced" in text:
            equipment = "Advanced"

        # Trend
        trend = "Rising" if "Rising" in text else ("Stable" if "Stable" in text else "Unknown")

        # Example channels
        channels_match = re.search(r'EXAMPLE CHANNELS\s*\n([^\n]+)', text, re.IGNORECASE)
        example_channels = channels_match.group(1).strip() if channels_match else ""

        # Formats
        formats_match = re.search(r'VIDEO FORMATS\s*\n((?:[^\n]+\n){1,5})', text, re.IGNORECASE)
        formats = [f.strip() for f in formats_match.group(1).strip().split('\n')] if formats_match else []

        slug = re.sub(r'[^a-z0-9]+', '-', title.lower()).strip('-')

        parsed_niches.append({
            "name": title,
            "slug": slug,
            "category": category,
            "niche_score": score,
            "cpm_range": cpm_range,
            "competition": competition,
            "views_per_1k": views_per_1k,
            "faceless": faceless,
            "equipment": equipment,
            "trend": trend,
            "example_channels": example_channels,
            "formats": formats,
        })
        
    print(f"Parsed {len(parsed_niches)} niches successfully.")
    with open("storage/app/collabpals_all_niches.json", "w", encoding="utf-8") as f:
        json.dump(parsed_niches, f, indent=2, ensure_ascii=False)
        
    print("Saved to storage/app/collabpals_all_niches.json")
    browser.close()
