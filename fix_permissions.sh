#!/bin/sh
CHOWN=$(which "chown")
DIRNAME=$(which "dirname")
UNAME=$(which "uname")
SUDO=$(which "sudo")

PROJECT_DIR=$($DIRNAME "${BASH_SOURCE[0]}")
OS=$($UNAME)
if [ "$OS" = "Linux" ]; then
	OWNER="www-data:www-data"
	$SUDO $CHOWN -R "$OWNER" "$PROJECT_DIR"
elif [ "$OS" = "Darwin" ]; then
	OWNER="_www:_www"
	$SUDO $CHOWN -R "$OWNER" "$PROJECT_DIR"
fi
if [ $? = 0 ]; then
	echo "Successfully set owner to $OWNER ($OS) for folder $PROJECT_DIR"
	exit 0
else
	echo "Error while setting owner to $OWNER ($OS) for folder $PROJECT_DIR"
	exit 1
fi
