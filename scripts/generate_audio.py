import os
import sys
import json
import asyncio
import edge_tts

async def generate_voiceover(text, output_mp3_path, output_srt_path=None, voice="en-US-ChristopherNeural"):
    """
    Generate neural voiceover MP3 and synchronized SRT subtitles using Edge TTS
    """
    os.makedirs(os.path.dirname(os.path.abspath(output_mp3_path)), exist_ok=True)
    
    communicate = edge_tts.Communicate(text, voice)
    submaker = edge_tts.SubMaker()
    
    with open(output_mp3_path, "wb") as file:
        async for chunk in communicate.stream():
            if chunk["type"] == "audio":
                file.write(chunk["data"])
            elif chunk["type"] in ["WordBoundary", "SentenceBoundary"]:
                submaker.feed(chunk)

    if output_srt_path:
        os.makedirs(os.path.dirname(os.path.abspath(output_srt_path)), exist_ok=True)
        with open(output_srt_path, "w", encoding="utf-8") as srt_file:
            srt_file.write(submaker.get_srt())

    result = {
        "status": "success",
        "audio_path": output_mp3_path,
        "srt_path": output_srt_path,
        "voice": voice,
        "file_size": os.path.getsize(output_mp3_path)
    }
    print(json.dumps(result))

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Usage: python generate_audio.py <input_text_or_file> <output_mp3> [output_srt] [voice]"}))
        sys.exit(1)

    input_arg = sys.argv[1]
    output_mp3 = sys.argv[2]
    output_srt = sys.argv[3] if len(sys.argv) > 3 else None
    voice = sys.argv[4] if len(sys.argv) > 4 else "en-US-ChristopherNeural"

    # If input is a file path
    if os.path.isfile(input_arg):
        with open(input_arg, "r", encoding="utf-8") as f:
            text = f.read()
    else:
        text = input_arg

    asyncio.run(generate_voiceover(text, output_mp3, output_srt, voice))
