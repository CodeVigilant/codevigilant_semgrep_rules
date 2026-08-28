// NEGATIVE: constant string literal passthrough only (safe)
raw(" &#187; ")
raw("&#8212;")
raw("&#x2192;") // right arrow
text(c.msgAnnotated)               // escaping builder path is fine
a(href: b.url) { text(b.displayName) }