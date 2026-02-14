#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
WP_TESTS_DIR=${WP_TESTS_DIR-$TMPDIR/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-$TMPDIR/wordpress/}

RETRIES=5
SLEEP=10

download_and_extract_wordpress_develop() {
	local ref=$1
	local archive_url=$2
	local dest_dir=$3

	rm -rf "$dest_dir"
	mkdir -p "$dest_dir"

	local archive_file
	archive_file=$(mktemp)
	if ! retry_run curl -sSL "$archive_url" -o "$archive_file"; then
		rm -f "$archive_file"
		return 1
	fi

	if ! tar -xzf "$archive_file" -C "$dest_dir" --strip-components=1; then
		rm -f "$archive_file"
		return 1
	fi

	rm -f "$archive_file"
	return 0
}

# retry_run: run a command and retry on failure
retry_run() {
	local n=0
	until "$@"; do
		n=$((n+1))
		if [ $n -ge $RETRIES ]; then
			echo "Command failed after $RETRIES attempts: $@" >&2
			return 1
		fi
		echo "Command failed. Retrying in $SLEEP seconds... ($n/$RETRIES)" >&2
		sleep $SLEEP
	done
	return 0
}

if [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+$ ]]; then
	WP_TESTS_TAG="branches/$WP_VERSION"
elif [[ $WP_VERSION == 'trunk' ]]; then
	WP_TESTS_TAG="trunk"
else
	# fetch the latest version with retries
	TMP_VER_FILE=$(mktemp)
	if ! retry_run curl -sSL https://api.wordpress.org/core/version-check/1.1/ -o "$TMP_VER_FILE"; then
		echo "Latest WordPress version could not be fetched after $RETRIES attempts" >&2
		rm -f "$TMP_VER_FILE"
		exit 1
	fi
	LATEST_VERSION=$(tail -1 "$TMP_VER_FILE")
	rm -f "$TMP_VER_FILE"
	if [[ -z "$LATEST_VERSION" ]]; then
		echo "Latest WordPress version could not be found"
		exit 1
	fi
	WP_TESTS_TAG="tags/$LATEST_VERSION"
fi

set -ex

install_wp_and_test_suite() {
	local wp_develop_dir="$TMPDIR/wordpress-develop"
	local wp_develop_url=""
	local wp_develop_ref=""

	if [[ $WP_TESTS_TAG == trunk ]]; then
		wp_develop_ref="trunk"
		wp_develop_url="https://codeload.github.com/WordPress/wordpress-develop/tar.gz/${wp_develop_ref}"
	elif [[ $WP_TESTS_TAG =~ ^branches/ ]]; then
		wp_develop_ref=${WP_TESTS_TAG#branches/}
		wp_develop_url="https://codeload.github.com/WordPress/wordpress-develop/tar.gz/${wp_develop_ref}"
	elif [[ $WP_TESTS_TAG =~ ^tags/ ]]; then
		wp_develop_ref=${WP_TESTS_TAG#tags/}
		wp_develop_url="https://codeload.github.com/WordPress/wordpress-develop/tar.gz/${wp_develop_ref}"
	fi

	# setup up WordPress
	if [ ! -d "$WP_CORE_DIR" ]; then
		mkdir -p "$WP_CORE_DIR"
		if [[ -n "$wp_develop_url" ]] && download_and_extract_wordpress_develop "$wp_develop_ref" "$wp_develop_url" "$wp_develop_dir"; then
			rm -rf "$WP_CORE_DIR"
			mkdir -p "$WP_CORE_DIR"
			cp -R "$wp_develop_dir/src/." "$WP_CORE_DIR"
		else
			# Fallback to SVN if GitHub is unavailable.
			retry_run svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/src/ "$WP_CORE_DIR"
		fi
	fi

	# set up testing suite if it doesn't yet exist
	if [ ! -d "$WP_TESTS_DIR" ]; then
		# set up testing suite
		mkdir -p "$WP_TESTS_DIR"
		if [[ -n "$wp_develop_url" ]] && [ -d "$wp_develop_dir/tests/phpunit" ]; then
			cp -R "$wp_develop_dir/tests/phpunit/includes" "$WP_TESTS_DIR/includes"
			cp -R "$wp_develop_dir/tests/phpunit/data" "$WP_TESTS_DIR/data"
		else
			# Fallback to SVN if GitHub is unavailable.
			retry_run svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/ "$WP_TESTS_DIR/includes"
			retry_run svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/ "$WP_TESTS_DIR/data"
		fi
	fi

	if [ ! -f wp-tests-config.php ]; then
		# download wp-tests-config-sample.php with retries
		if [ -f "$wp_develop_dir/wp-tests-config-sample.php" ]; then
			cp "$wp_develop_dir/wp-tests-config-sample.php" "$WP_TESTS_DIR/wp-tests-config.php"
		else
			retry_run curl -sSL https://develop.svn.wordpress.org/${WP_TESTS_TAG}/wp-tests-config-sample.php -o "$WP_TESTS_DIR/wp-tests-config.php"
		fi
		# remove all forward slashes in the end
		WP_CORE_DIR=$(echo $WP_CORE_DIR | sed "s:/\+$::")
		sed -i "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR"/wp-tests-config.php
		sed -i "s|localhost|${DB_HOST}|" "$WP_TESTS_DIR"/wp-tests-config.php
	fi

}

install_db() {
	# parse DB_HOST for port or socket references
	local PARTS=(${DB_HOST//\:/ })
	local DB_HOSTNAME=${PARTS[0]};
	local DB_SOCK_OR_PORT=${PARTS[1]};
	local EXTRA=""

	if ! [ -z $DB_HOSTNAME ] ; then
		if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
			EXTRA=" --host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
		elif ! [ -z $DB_SOCK_OR_PORT ] ; then
			EXTRA=" --socket=$DB_SOCK_OR_PORT"
		elif ! [ -z $DB_HOSTNAME ] ; then
			EXTRA=" --host=$DB_HOSTNAME --protocol=tcp"
		fi
	fi

	# create database
	mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA
}

install_wp_and_test_suite
install_db
