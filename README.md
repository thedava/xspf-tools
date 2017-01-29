# xspf-order
Order xspf playlists using the available order types

The old file will be saved in a .bak-file (just in case)



**asc**

All entries in the playlist will be sorted by their file name (not path) in ascending order


**desc**

Same as *asc* but in opposite order


**random**

All entries will be sorted in a random order



### Usage

```bash
user@machine:~$ php order-xspf.phar
Version 0.2

Usage: php order-xspf.phar <order_type> <playlist_file>

Order Types:
    asc:    The file will be ordered by video file names in ascending order
    desc:   The file will be ordered by video file names in descending order
    random: The file will be ordered in random order
```
