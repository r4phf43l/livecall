# livecall
2023 version

LiveCall a Chat Plugin for GLPI

# Install

```
# install
-Inside GLPI's plugins folder create a new folder: livecall
-Copy files
-Activate
```
# Usage
You can add some extra features to your plugin like this
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

RocketChat LiveHelperChat.

## Disclaimer

This Plugin generates cookies using GLPI data: name, firstname, profile, location, email.

Enjoy.
