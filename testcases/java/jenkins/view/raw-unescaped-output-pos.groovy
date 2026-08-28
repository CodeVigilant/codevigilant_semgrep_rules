// POSITIVE: unescaped output builder of SCM/changelog-derived values in a Groovy view
raw(c.msgAnnotated)
raw(c.getCommitId())
raw(browser.getChangeSetLink(c))
raw(entry.msg)