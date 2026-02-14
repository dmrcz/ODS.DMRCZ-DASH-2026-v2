#!/bin/bash

# Konfigurace
REPO_URL="https://github.com/dmrcz/ods.dmrcz-dash-2026-v2.git"
BRANCH="main" # Změňte na master, pokud nepoužíváte main

# Ověření, zda jsme v git repozitáři
if ! git rev-parse --is-inside-work-tree > /dev/null 2>&1; then
    echo "Chyba: Aktuální adresář není Git repozitář."
    exit 1
fi

# Stažení metadat z remote bez úpravy lokálních souborů
echo "Kontroluji aktualizace na $REPO_URL..."
git fetch origin "$BRANCH" -q

# Porovnání lokální verze se vzdálenou
UPSTREAM=${1:-'@{u}'}
LOCAL=$(git rev-parse @)
REMOTE=$(git rev-parse "$UPSTREAM")
BASE=$(git merge-base @ "$UPSTREAM")

if [ "$LOCAL" = "$REMOTE" ]; then
    echo "Aplikace je aktuální."
elif [ "$LOCAL" = "$BASE" ]; then
    echo "K DISPOZICI JSOU AKTUALIZACE! Spusťte 'git pull' pro aktualizaci."
elif [ "$REMOTE" = "$BASE" ]; then
    echo "Máte lokální změny, které nejsou na serveru (Need to push)."
else
    echo "Větve se rozcházejí (Diverged)."
fi
