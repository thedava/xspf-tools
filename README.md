# xspf-tools

Tools for manipulating and creating [xspf](http://xspf.org/) playlists (e.g. VLC Player Playlists)

[![Build Status](https://travis-ci.org/thedava/xspf-tools.svg?branch=master)](https://travis-ci.org/thedava/xspf-tools)

This tool will always create a *.bak file if a playlist will be modified (just in case).


## Clean

```php xspf.phar clean```

Remove all *.bak files


## Create

```php xspf.phar create <playlist-file> <file-or-folder> (<file-or-folder>)...```

Create a new xspf playlist and add the given files and folders to it

## Merge

```php xspf.phar merge [-u|--unique] [--] <target> [<source>]...```

Merge multiple playlists into one single file

**-u|--unique**<br>
Remove duplicates

## Order

```php xspf.phar order <order-type> <playlist-file>```

Order xspf playlists using the available order types


Available order types:

**asc**<br>
All entries in the playlist will be sorted by their file name (not path) in ascending order

**desc**<br>
Same as *asc* but in opposite order

**random**<br>
All entries will be sorted in a random order

**asc-length** (not available yet)<br>
All entries in the playlist will be sorted by their video length (shortest first, longest last).

**desc-length** (not available yet)<br>
All entries in the playlist will be sorted by their video length (longest first, shortest last).

### Example Scenario

You want to listen to a folder full of music but you want to listen to them in a random order and you don't want to listen to a song twice.

Steps:
- Create a playlist of that folder (using VLC Player)
- Shuffle the playlist using **xspf-order**
- Open the playlist file in your player and listen to your music (with shuffle off)

If you want to stop listening and continue later at the same position then you can edit the playlist file in your player. Remove all songs in the playlist before/above your current song and save the playlist. When you continue listening, every song you already listened to is removed and the progress continues.


## Update

```php xspf.phar update <playlist-file>```

Update the given playlist file (set durations, etc.)


## Validate

```php xspf.phar validate <playlist-file>```

Checks if all files in the playlist exist and removes the missing files from the playlist
