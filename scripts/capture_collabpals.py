import os
import sys
import json
import time
from datetime import datetime, timezone
from playwright.sync_api import sync_playwright

PARSER_VERSION = "1.0.1"

def extract_niche_data(target_query):
    """
    Search and extract niche data from CollabPals YouTube Niche Finder
    """
    # Laravel public storage path for evidence screenshots so they are web accessible
    evidence_dir = os.path.abspath("public/storage/evidence")
    os.makedirs(evidence_dir, exist_ok=True)
    # Also link to storage/app/public/evidence if needed
    os.makedirs("storage/app/public/evidence", exist_ok=True)

    result = {
        "source_name": "CollabPals YouTube Niche Finder",
        "source_url": "https://www.collabpals.com/tools/youtube-niche-finder",
        "matched_query": target_query,
        "source_niche_name": None,
        "niche_score": None,
        "demand_score": None,
        "competition_score": None,
        "monetization_score": None,
        "cpm_range": None,
        "growth_trend": None,
        "faceless_friendly": None,
        "equipment_level": None,
        "sub_niches": [],
        "evidence_screenshot_path": None,
        "evidence_text": {},
        "capture_method": "browser_automation",
        "parser_version": PARSER_VERSION,
        "confidence": 0.0,
        "verification_status": "unverified",
        "captured_at": datetime.now(timezone.utc).isoformat(),
        "notes": None,
    }

    with sync_playwright() as p:
        browser = p.chromium.launch(channel="msedge", headless=True)
        context = browser.new_context(viewport={"width": 1366, "height": 900})
        page = context.new_page()

        try:
            print(f"[{target_query}] Opening CollabPals...")
            page.goto("https://www.collabpals.com/tools/youtube-niche-finder", wait_until="networkidle", timeout=35000)
            time.sleep(2)

            search_box = page.locator('input[placeholder*="Search niches"]').first
            if not search_box.is_visible():
                result["notes"] = "Search input not found on page"
                result["verification_status"] = "needs_review"
                return result

            # Type the target query
            search_box.fill("")
            time.sleep(0.5)
            search_box.fill(target_query)
            time.sleep(2)

            # Wait for search results container
            page.wait_for_timeout(1000)

            # Find matching card in results
            # The niche card has the niche name as a heading or text
            card_locator = page.locator(f'div:has-text("{target_query}"):has-text("CPM Range")').last
            if not card_locator.is_visible():
                card_locator = page.locator(f'text="{target_query}"').first.locator('xpath=./ancestor::div[contains(., "CPM Range")][1]')

            if not card_locator or not card_locator.is_visible():
                result["notes"] = f"Niche card '{target_query}' not visible after search"
                result["verification_status"] = "needs_review"
                return result

            # Scroll into view and capture screenshot of the exact card and context
            card_locator.scroll_into_view_if_needed()
            page.wait_for_timeout(500)

            safe_query = target_query.lower().replace(" ", "_")
            timestamp_sec = int(time.time())
            screenshot_filename = f"collabpals_{safe_query}_{timestamp_sec}.png"
            rel_screenshot_path = f"storage/evidence/{screenshot_filename}"
            full_screenshot_path = os.path.join(evidence_dir, screenshot_filename)
            card_locator.screenshot(path=full_screenshot_path)
            # Also save to storage/app/public/evidence
            card_locator.screenshot(path=os.path.join("storage/app/public/evidence", screenshot_filename))
            result["evidence_screenshot_path"] = rel_screenshot_path

            card_text = card_locator.inner_text()
            lines = [l.strip() for l in card_text.split("\n") if l.strip()]
            result["evidence_text"] = {"raw_lines": lines, "full_text": card_text}

            # Precise parser based on CollabPals card layout:
            # ['78', 'Electric Vehicles', 'Automotive', 'Low', 'Competition', '$6-$18', 'CPM Range', '38', 'Views per 1K Subs', 'Basic', 'Rising', 'VIDEO FORMATS', 'Reviews', 'Range tests', 'Charging guides', 'Comparisons', 'EXAMPLE CHANNELS', 'Out of Spec, Electric Viking, Cleanerwatt', 'Explore 4 micro-niches']
            for i, line in enumerate(lines):
                if line.isdigit() and int(line) <= 100 and result["niche_score"] is None:
                    result["niche_score"] = int(line)
                elif line == "Competition" and i > 0:
                    comp = lines[i-1]
                    result["evidence_text"]["competition_level"] = comp
                elif line == "CPM Range" and i > 0:
                    result["cpm_range"] = lines[i-1]
                elif line == "Views per 1K Subs" and i > 0:
                    result["evidence_text"]["views_per_1k_subs"] = lines[i-1]
                elif line.lower() in ["phone", "phone only", "basic", "basic setup", "professional"]:
                    result["equipment_level"] = line
                elif line.lower() in ["rising", "stable", "declining"]:
                    result["growth_trend"] = line
                elif line.lower() == "faceless":
                    result["faceless_friendly"] = True

            # If faceless not explicitly found on the card
            if result["faceless_friendly"] is None:
                result["faceless_friendly"] = ("faceless" in card_text.lower())

            # Source niche name
            for line in lines:
                if target_query.lower() in line.lower() and not any(k in line.lower() for k in ["explore", "video", "search", "found", "niches"]):
                    result["source_niche_name"] = line
                    break
            if not result["source_niche_name"]:
                result["source_niche_name"] = target_query

            # Extract video formats and example channels
            if "VIDEO FORMATS" in lines:
                vf_idx = lines.index("VIDEO FORMATS")
                formats = []
                for fl in lines[vf_idx+1:]:
                    if fl in ["EXAMPLE CHANNELS", "Explore 4 micro-niches", "Explore 5 micro-niches"] or fl.startswith("Explore"):
                        break
                    formats.append(fl)
                result["evidence_text"]["video_formats"] = formats

            if "EXAMPLE CHANNELS" in lines:
                ec_idx = lines.index("EXAMPLE CHANNELS")
                if ec_idx + 1 < len(lines):
                    result["evidence_text"]["example_channels"] = lines[ec_idx+1]

            # Click explore micro-niches button
            micro_btn = card_locator.locator('button:has-text("micro-niches")').first
            if micro_btn.is_visible():
                micro_btn.click()
                time.sleep(1)

                # Capture modal popover or expanded content
                dialog = page.locator('div[role="dialog"], div[class*="modal"], div[class*="popover"], [class*="dialog"]').first
                if dialog.is_visible():
                    d_text = dialog.inner_text()
                    d_lines = [dl.strip() for dl in d_text.split("\n") if dl.strip()]
                    result["evidence_text"]["sub_niches_dialog"] = d_lines
                    # Micro-niches are items in the modal
                    sub_items = []
                    for item in d_lines:
                        if not any(k in item.lower() for k in ["micro", "close", "back", "niche", "explore"]) and not item.isdigit() and len(item) > 2:
                            sub_items.append(item)
                    result["sub_niches"] = sub_items
                else:
                    # Fallback to video formats as micro-niches if modal not present
                    result["sub_niches"] = result["evidence_text"].get("video_formats", [])

            # Final verification status check
            if result["niche_score"] is not None and result["source_niche_name"] and result["cpm_range"]:
                result["confidence"] = 1.0
                result["verification_status"] = "verified"
            else:
                result["confidence"] = 0.6
                result["verification_status"] = "needs_review"

        except Exception as e:
            result["notes"] = f"Capture exception: {str(e)}"
            result["verification_status"] = "needs_review"
            result["confidence"] = 0.0
        finally:
            browser.close()

    return result

if __name__ == "__main__":
    query = sys.argv[1] if len(sys.argv) > 1 else "Electric Vehicles"
    res = extract_niche_data(query)
    print(json.dumps(res, indent=2))
