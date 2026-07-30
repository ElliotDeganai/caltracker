#!/bin/bash
set -e

MESSAGE="$1"

echo "Vérification des changements..."

if [ -z "$(git status --porcelain)" ]; then
    echo "Aucun changement à publier."
    exit 0
fi

git add -A

if [ -z "$MESSAGE" ]; then
    FILES=$(git diff --cached --name-only | xargs -n1 basename | sort -u | tr '\n' ',' | sed 's/,/, /g' | sed 's/, $//')
    TIMESTAMP=$(date "+%Y-%m-%d %H:%M")
    MESSAGE="Update: ${FILES:0:80} ($TIMESTAMP)"
fi

echo "Commit : $MESSAGE"
git commit -m "$MESSAGE"

echo "Push vers GitHub..."
git push origin main

echo "✅ Publié avec succès !"
