@echo off

php xspf.phar batch "./Batch.yml" --no-ansi

echo
echo
echo "Begin interactive conflict solving"

php xspf.phar duplicates:show --no-progress -- duplicates.txt
