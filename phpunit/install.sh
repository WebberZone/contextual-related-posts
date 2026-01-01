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
	# setup up WordPress
	if [ ! -d $WP_CORE_DIR ]; then
		mkdir -p $WP_CORE_DIR
		# Retry svn checkout in case of transient network issues
		retry_run svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/src/ $WP_CORE_DIR
	fi

	# set up testing suite if it doesn't yet exist
	if [ ! -d $WP_TESTS_DIR ]; then
		# set up testing suite
		mkdir -p $WP_TESTS_DIR
		# Retry svn checkouts for includes and data
		retry_run svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/ $WP_TESTS_DIR/includes
		retry_run svn co --quiet https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/ $WP_TESTS_DIR/data
	fi

	if [ ! -f wp-tests-config.php ]; then
		# download wp-tests-config-sample.php with retries
		retry_run curl -sSL https://develop.svn.wordpress.org/${WP_TESTS_TAG}/wp-tests-config-sample.php -o "$WP_TESTS_DIR/wp-tests-config.php"
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
