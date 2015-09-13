#!/bin/sh

# Smarty
wget -O Smarty-stable.tar.gz http://www.smarty.net/files/Smarty-stable.tar.gz
tar xf Smarty-stable.tar.gz
mv Smarty-*/libs .
rm -r Smarty-*

# jQuery
wget -O st/jquery.min.js http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js

# jQuery UI
wget http://jqueryui.com/resources/download/jquery-ui-1.11.4.zip
unzip jquery-ui-*.zip
mv jquery-ui-*/*.css st/
mv jquery-ui-*/*.js st/
rm -r jquery-ui-*

# jQuery UI i18n
wget -O st/jquery-ui-i18n.min.js http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/i18n/jquery-ui-i18n.min.js

# jQuery UI Redmond theme
wget http://jqueryui.com/resources/download/jquery-ui-themes-1.11.4.zip
unzip jquery-ui-themes-*.zip
mv jquery-ui-themes-*/themes/redmond/images st/
mv jquery-ui-themes-*/themes/redmond/*.css st/
rm -r jquery-ui-themes-*

# jPlayer
wget -O st/jquery.jplayer.min.js http://cdnjs.cloudflare.com/ajax/libs/jplayer/2.6.0/jquery.jplayer.min.js
wget -O st/Jplayer.swf http://cdnjs.cloudflare.com/ajax/libs/jplayer/2.6.0/Jplayer.swf

# jPlayer-metro-skin
wget -O st/jplayer.metro-fire.css https://raw.githubusercontent.com/miktemk/jPlayer-metro-skin/master/skin/metro-fire/jplayer.metro-fire.css

# FontAwesome for player
wget -O st/font-awesome.min.css https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/css/font-awesome.min.css
mkdir fonts
wget -O fonts/FontAwesome.otf https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/fonts/FontAwesome.otf
wget -O fonts/fontawesome-webfont.eot https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/fonts/fontawesome-webfont.eot
wget -O fonts/fontawesome-webfont.svg https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/fonts/fontawesome-webfont.svg
wget -O fonts/fontawesome-webfont.ttf https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/fonts/fontawesome-webfont.ttf
wget -O fonts/fontawesome-webfont.woff https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/fonts/fontawesome-webfont.woff
