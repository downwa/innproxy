# Copyright (C) 2011-2013 Zentyal S.L.
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License, version 2, as
# published by the Free Software Foundation.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

use strict;
use warnings;

package EBox::CaptivePortal::CGI::Popup;

use base 'EBox::CaptivePortal::CGI::Base';

use EBox::Gettext;
use EBox::CaptivePortal;
use Apache2::RequestUtil;

use CGI::Cookie;

sub new # (error=?, msg=?, cgi=?)
{
    my $class = shift;
    my $self = $class->SUPER::new('title' => '',
                                  'template' => '/captiveportal/popup.mas',
                                  @_);
    bless($self, $class);
    return $self;
}

# cat /var/lib/zentyal-captiveportal/sessions/${cookie:0:32} | grep user: | cut -d "'" -f 2
sub myuid {
        my $uid = 0;
        my $cookie = shift;
        $cookie = substr($cookie, 0, 32);
        my $session = "/var/lib/zentyal-captiveportal/sessions/$cookie";
        open(my $FH, $session) or die "Failed to open file $session ($!)"; #return 0;
        my @buf = <$FH>;
        close($FH);
        my @user = grep (/^user:/, @buf);
        if($#user >= 0) {
                # my @fields=split /\'/, $user[0]; # Used to have single-ticks around uid
		chomp($user[0]);
                my @fields=split / /, $user[0];
                $uid = $fields[1];
        }
	my $log = EBox::logger;
	$log->info("Session $session uid=$uid"); # $log->info, warn, error
        return $uid;
}

sub myusage {
	my $uid = shift;
        # Retrieve usage using uid (We want all usage by this uid, not just the current cookie)
        my $srce = "/tmp/usages.csv";
        my $string1 = "^$uid,";
        open(my $FH, $srce) or return 0; #die "Failed to open file $srce ($!)";
        my @buf = <$FH>;
        close($FH);
        my @lines = grep (/$string1/, @buf);
        my $usage = "   0.00\n";
        # e.g. 220,PASSWORD,0,0,  65.64
	my $log = EBox::logger;
        if($#lines >= 0) {
                my @fields2=split /,/, $lines[0];
                $usage=$fields2[4];
        }
	$log->info("uid=$uid usage=$usage"); # $log->info, warn, error
        return $usage;
}

sub mytimeleft {
	my $log = EBox::logger;
        my $uid = shift;
        # Retrieve time left using uid
        my $srce = "/tmp/usages.csv";
        my $string1 = "^$uid,";
        open(my $FH, $srce) or return 0; # die "Failed to open file $srce ($!)";
        my @buf = <$FH>;
        close($FH);
        my @lines = grep (/$string1/, @buf);
        my $timeleft = "0\n";
        # e.g. 220,PASSWORD,0,0,  65.64
        if($#lines >= 0) {
                my @fields2=split /,/, $lines[0];
                my $datestamp=$fields2[2];
                my $seconds=$fields2[3];
                my $endtime=$datestamp+$seconds;
                $timeleft=$endtime-time();
        }
	$log->info("uid=$uid timeleft=$timeleft"); # $log->info, warn, error
        return $timeleft;
}

sub _print
{
    my $self = shift;

    my $interval = _readInterval();
    if (not $interval) {
        $interval = 60;
    }
    my %cookies = CGI::Cookie->fetch;
    my $cookie = $cookies{'EBox::CaptivePortal::Auth_EBox'}->value;
    my $uid = myuid($cookie);
    my $usage = myusage($uid);
    my $timeleft = mytimeleft($uid);
    my $timeout="";
    if($timeleft < 0) { $timeout="logout();"; } # NOT <= 0 since timeleft will be ZERO on new logins (NO uid associated with our cookie yet)
    if(   $timeleft > 604800) { $timeleft /= 604800; $timeleft = sprintf("%.2f weeks",$timeleft); }
    elsif($timeleft > 86400) { $timeleft /= 86400; $timeleft = sprintf("%.2f days",$timeleft); }
    elsif($timeleft > 3600) { $timeleft /= 3600; $timeleft = sprintf("%.2f hours",$timeleft); }
    elsif($timeleft > 60) { $timeleft /= 60; $timeleft = sprintf("%.2f minutes",$timeleft); }
    else { $timeleft = sprintf("%f seconds",$timeleft); }

    print($self->cgi()->header(-charset=>'utf-8'));
    $self->{params} = [ interval => $interval, usage => $usage, timeleft => $timeleft, timeout => $timeout, uid => $uid, cookie => $cookie ];
    $self->_body;
}

sub _readInterval
{
    my $interval;

    my $path =  EBox::CaptivePortal::PERIOD_FILE;
    open my $FH, '<', $path  or
        return undef;
    $interval = <$FH>;
    close $FH;
    return $interval;
}

sub _top
{
}

sub _loggedIn
{
    return 1;
}

sub _menu
{
    return;
}

1;
