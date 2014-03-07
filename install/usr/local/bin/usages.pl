#!/usr/bin/perl

use strict;
use warnings;

use EBox;
use EBox::Users::User;

EBox::init();

sub is_number{
  my $n = shift;
  my $ret = 1;
  $SIG{"__WARN__"} = sub {$ret = 0};
  eval { my $x = $n + 1 };
  return $ret
}

my $usersMod = EBox::Global->modInstance('users');
my $users = $usersMod->users();

foreach my $user (@$users) {
	my @fields=split /\s+/, $user->description;
        my $uid = $user->name;
	my $password  = $fields[0];
	my $datestamp = $fields[1];
	my $seconds   = $fields[2];
	if(! defined $datestamp) { $datestamp = 0; $seconds=0; }
	else {
		if(!is_number($datestamp)) {
			$password="$password $datestamp $seconds";
		}
	}
	my $usage=`usage $uid`;
	printf("%s,%s,%d,%d,%7.2f\n",$uid,$password,$datestamp,$seconds,$usage);
}

1;
