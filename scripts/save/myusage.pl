#!/usr/bin/perl

use strict;
use warnings;

if($#ARGV < 0) { die "Missing cookie value"; }

sub myuid {
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
                my @fields=split /\s+/, $accesses[0];
                $uid=$fields[2];
        }
        return $uid;
}

sub myusage {
	my $uid = shift;
	# Retrieve usage using uid (We want all usage by this uid, not just the current cookie)
	my $srce = "/tmp/usages.csv";
	my $string1 = "^$uid,";
	open(my $FH, $srce) or die "Failed to open file $srce ($!)";
	my @buf = <$FH>;
	close($FH);
	my @lines = grep (/$string1/, @buf);
	my $usage = "   0.00\n";
	# e.g. 220,PASSWORD,0,0,  65.64
	if($#lines >= 0) {
		my @fields2=split /,/, $lines[0];
		$usage=$fields2[4];
	}
	return $usage;
}

my $uid = myuid(${ARGV[0]});
my $usage = myusage($uid);
print $usage;
