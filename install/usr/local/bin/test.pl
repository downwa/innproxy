#!/usr/bin/perl

use strict;
use warnings;

my $cn; open INI,"/etc/innproxy.settings"; eval(<INI>);

printf "%s\n",$cn;

1;
