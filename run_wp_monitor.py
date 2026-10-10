#!/root/.local/bin/py
# Wrapper to run wp_cve_monitor.py (workaround for cron lifecycle_guard
# embedded-null-byte crash on interpreter+script-path commands).
import sys, os
os.chdir("/root/WORK/codevigilant_semgrep_rules")
sys.path.insert(0, "/root/WORK/codevigilant_semgrep_rules")
sys.argv = ["wp_cve_monitor.py"]
exec(open("/root/WORK/codevigilant_semgrep_rules/wp_cve_monitor.py").read())
