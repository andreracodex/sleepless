import os
import sys
import json
import time
from playwright.sync_api import sync_playwright

def inspect_collabpals():
    with sync_playwright() as p:
        browser = p.chromium.launch(channel="msedge", headless=True)
        page = browser.new_page(viewport={"width": 1280, "height": 900})
        print("Navigating to CollabPals...")
        page.goto("https://www.collabpals.com/tools/youtube-niche-finder", wait_until="networkidle", timeout=30000)
        time.sleep(2)

        # Let's see the search input or buttons
        search_inputs = page.query_selector_all("input")
        print(f"Found {len(search_inputs)} inputs")
        for i, inp in enumerate(search_inputs):
            print(f"Input {i}: placeholder='{inp.get_attribute('placeholder')}', name='{inp.get_attribute('name')}'")

        # Let's save a full page screenshot
        os.makedirs("storage/app/evidence", exist_ok=True)
        screenshot_path = "storage/app/evidence/collabpals_initial.png"
        page.screenshot(path=screenshot_path, full_page=True)
        print(f"Screenshot saved to {screenshot_path}")

        # Let's inspect the cards/niche list
        # Check text content of the page
        body_text = page.inner_text("body")
        lines = [line.strip() for line in body_text.split("\n") if line.strip()]
        
        # Save lines to a debug file
        with open("storage/app/evidence/collabpals_lines.txt", "w", encoding="utf-8") as f:
            for l in lines:
                f.write(l + "\n")
        print(f"Dumped {len(lines)} lines to storage/app/evidence/collabpals_lines.txt")

        browser.close()

if __name__ == "__main__":
    inspect_collabpals()
