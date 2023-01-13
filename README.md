# livecall
2023 version

LiveCall a Chat Plugin for GLPI

This plugin was inspired on this work https://github.com/l33one/livechat.
It is not a fork. Is a new complete new piece.

# Install

```
# install
-Inside GLPI's plugins folder create a new folder: livecall
-Copy files
-Activate
```
# Usage
You can add some extra features to your plugin like this:
```
RocketChat( function() {
    this.registerGuest( {
        name: livechat('name'),
        email: livechat('email'),
        department: livechat('location')
    } );
} );
```

## Suports

Tested with RocketChat and LiveHelperChat.

## Disclaimer

This Plugin generates cookies using GLPI data: name, firstname, profile, location, email.

Enjoy.
