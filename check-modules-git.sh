#!/bin/bash
for mod in Authentication Basicdata Corsec Logs Usermanagement; do
  echo "=== $mod ==="
  cd /var/www/corsec/Modules/$mod 2>/dev/null || { echo "folder not found"; echo ""; continue; }
  toplevel=$(git rev-parse --show-toplevel 2>/dev/null)
  if [ "$toplevel" == "/var/www/corsec/Modules/$mod" ]; then
    echo "✅ independent repo"
    branch=$(git branch --show-current)
    echo "   branch: $branch"
    git status -s
  else
    echo "⚠️  NOT independent (toplevel: $toplevel)"
  fi
  echo ""
done
