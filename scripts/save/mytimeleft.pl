#!/usr/bin/perl

use strict;
use warnings;

if($#ARGV < 0) { die "Missing cookie value"; }

sub myuid0 {
        my $cookie = shift;
        # grep "^192.168.2..* - [12][0-9][0-9].* .*EBox::CaptivePortal::Auth_EBox=$cookie\"" /var/log/zentyal-captiveportal/access.log | head -n 1 | awk '{print $3}'
        # Retrieve uid using cookie value
        my $access = "/var/log/zentyal-captiveportal/access.log";
        my $string0 = "^192.168.2..* - [12][0-9][0-9].* .*EBox::CaptivePortal::Auth_EBox=$cookie\"";
        open(my $FH0, $access) or die "Failed to open file $access ($!)";
        my @buf0 = <$FH0>;
        close($FH0);
        my @accesses = grep (/$string0/, @buf0);
        # e.g. 192.168.2.68 - 218 [24/Oct/2013:10:21:52 -0800] "GET /Popup HTTP/1.1" 200 1972 "https://192.168.2.1:4443/Popup" "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1678.0 Safari/537.36" "-" "EBox::CaptivePortal::Auth_EBox=f5bdd45979bb80f88f9e4c454ce523602b8f498691a908e7a156acbece089ac1"
        my $uid = 0;
        if($#accesses >= 0) {
                chomp($accesses[0]);
                my @fields=split / /, $accesses[0];
#                my @fields=split /\s+/, $accesses[0];
                $uid=$fields[2];
        }
        return $uid;
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
                chomp($user[0]);
                my @fields=split / /, $user[0];
                $uid = $fields[1];
        }
        return $uid;
}

sub mytimeleft {
	my $uid = shift;
	# Retrieve time left using uid
	my $srce = "/tmp/usages.csv";
	my $string1 = "^$uid,";
	open(my $FH, $srce) or die "Failed to open file $srce ($!)";
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
	return $timeleft;
}

# uid=218; d=$(cat /tmp/usages.csv | grep "^$uid," | awk -F ',' '{print $3+$4}'); date --date "@$d"

my $uid = myuid(${ARGV[0]});
#my $timeleft = mytimeleft($uid);
#printf("%d\n",$timeleft);

    my $timeleft = mytimeleft($uid); 
    if(   $timeleft > 604800) { $timeleft /= 604800; $timeleft = sprintf("%.2f weeks",$timeleft); }
    elsif($timeleft > 86400) { $timeleft /= 86400; $timeleft = sprintf("%.2f days",$timeleft); }
    elsif($timeleft > 3600) { $timeleft /= 3600; $timeleft = sprintf("%.2f hours",$timeleft); }
    elsif($timeleft > 60) { $timeleft /= 60; $timeleft = sprintf("%.2f minutes",$timeleft); }
    else { $timeleft = sprintf("%.2f seconds",$timeleft); }

printf("%s for %s\n",$timeleft,$uid);
