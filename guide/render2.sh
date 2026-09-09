#!/bin/bash
# render2.sh <nazwa> <wysokosc> [offset-od-gory]
CHROME="/c/Program Files/Google/Chrome/Application/chrome.exe"
n="$1"; h="${2:-1000}"; top="$3"
cfg="cfg/$n.json"; [ -f "$cfg" ] || cfg=""
if [ -n "$top" ]; then
  node prepare.js "$n" "$cfg" "--top=$top" >/dev/null
  f="prepared/$n--$top.html"; o="shots/$n-$top.png"
else
  node prepare.js "$n" "$cfg" >/dev/null
  f="prepared/$n.html"; o="shots/$n.png"
fi
win=$(cygpath -w "$PWD/$f"); out=$(cygpath -w "$PWD/$o")
"$CHROME" --headless --disable-gpu --no-sandbox --hide-scrollbars --window-size=1600,"$h" --screenshot="$out" --virtual-time-budget=9000 "file:///${win//\//}" 2>/dev/null
printf "%-26s %s\n" "$o" "$(ls -lh $o | awk '{print $5}')"
