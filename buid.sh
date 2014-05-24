#!/bin/sh
BASEDIR=$(dirname $0)
export LC_CTYPE=fr_FR.UTF-8 # pour les noms de fichiers avec accents
/usr/bin/svn --non-interactive --trust-server-cert  update $BASEDIR/
/usr/bin/php $BASEDIR/../teipub/Teipub.php  "$BASEDIR/moliere.xml"  "$BASEDIR/moliere.sqlite" 2>&1
/usr/bin/php $BASEDIR/../teipub/Teipub.php  "$BASEDIR/critique-moliere.xml"  "$BASEDIR/critique-moliere.sqlite" 2>&1
exit 0
