#!/bin/bash
# render.sh <nazwa> <wysokosc>  — zrzut zawsze przy szerokości 1600 px (tak jak przy pomiarach)
CHROME="/c/Program Files/Google/Chrome/Application/chrome.exe"
n="$1"; h="${2:-1000}"
node prepare.js "$n" "cfg/$n.json" >/dev/null 2>&1 || node prepare.js "$n" >/dev/null
win=$(cygpath -w "$PWD/prepared/$n.html"); out=$(cygpath -w "$PWD/shots/$n.png")
"$CHROME" --headless --disable-gpu --no-sandbox --hide-scrollbars --window-size=1600,"$h" --screenshot="$out" --virtual-time-budget=9000 "file:///${win//\//}" 2>/dev/null
printf "%-24s %s\n" "$n" "$(ls -lh shots/$n.png | awk '{print $5}')"
