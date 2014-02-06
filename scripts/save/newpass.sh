#!/bin/sh

perl -MText::Password::Pronounceable -we 'printf("%s\n",uc(Text::Password::Pronounceable->generate(6,6)))'
