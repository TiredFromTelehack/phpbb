#!/bin/bash
#
# This file is part of the phpBB Forum Software package.
#
# @copyright (c) phpBB Limited <https://www.phpbb.com>
# @license GNU General Public License, version 2 (GPL-2.0)
#
# For full copyright and license information, please see
# the docs/CREDITS.txt file.
#
set -e
set -x

DB=$1
TRAVIS_PHP_VERSION=$2
path="$3"

if [ "$TRAVIS_PHP_VERSION" == "5.5" -a "$DB" == "mysqli" ]
then
	# Get the list of the executables files under a given path
	# The part "-name 'develop' -o -name 'vendor'" defines a set
	# of ignored directories.
	# The part "-path '*/bin/phpbbcli.php' -o -name 'composer.phar'"
	# defines a whitelist.

	executables_files=$( 							\
		find ${path}								\
			'('										\
				'('									\
					-name 'develop' -o				\
					-name 'vendor'					\
				')'									\
				-a -type d -prune -a -type f		\
			')'										\
			-o '('									\
				-not '('							\
					-path '*/bin/phpbbcli.php' -o	\
					-name 'composer.phar'			\
				')'									\
			-a '('									\
				-type f -a							\
				-not -perm 644						\
			')'										\
		')'											\
	)

	if [ "$executables_files" != '' ]
	then
		echo "$executables_files MUST have the right 644.";
		exit 1;
	fi
fi
