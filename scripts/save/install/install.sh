#!/bin/sh

apt-get remove --purge zentyal-captiveportal
apt-get install zentyal-captiveportal

cp usr/share/perl5/EBox/CaptivePortal/CGI/Popup.pm /usr/share/perl5/EBox/CaptivePortal/CGI/Popup.pm
cp usr/share/perl5/EBox/CaptivePortal/CGI/Popup.pm /usr/share/perl5/EBox/CaptivePortal/CGI/Popup.pm
cp usr/share/zentyal/templates/captiveportal/popup.mas /usr/share/zentyal/templates/captiveportal/popup.mas
cp usr/share/zentyal/templates/captiveportal/login.mas /usr/share/zentyal/templates/captiveportal/login.mas
cp usr/share/zentyal/templates/captiveportal/popupLaunch.mas /usr/share/zentyal/templates/captiveportal/popupLaunch.mas
cp usr/share/zentyal/stubs/captiveportal/captiveportal-apache2.conf.mas /usr/share/zentyal/stubs/captiveportal/captiveportal-apache2.conf.mas
