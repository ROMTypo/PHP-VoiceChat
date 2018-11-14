# PHP VoiceChat
This is a voice chat built with PHP + JavaScript
This client is really easy to setup, just clone and visit.

## Setup
1. Clone project
2. Make sure your webserver allows php read write
3. Visit website

## How it works
Currently it can record voice based on voice detection, then send it to the server. It will take those clips, base64 them, and save the base64 to a file.
Each new voice recording saved, it will remove the old ones. The client will make a request to get the file and if there are new ones, play them.

## Features
* Voice Detection
* Muting

## Planned Features
* User login
* Channels

## Bugs/Problems
* You can hear yourself
