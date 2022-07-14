#!/bin/bash
# backup
FILE=odissey-dbdump-`date +"%Y%m%d"`.sql
DBSERVER=127.0.0.1
DATABASE=odisey
USER=odisey
PASS=zPJQ3ehx
DEST=/var/www/odisey.ru/backups
unalias rm  2> /dev/null
rm ${DEST}/${FILE}     2> /dev/null
rm ${DEST}/${FILE}.gz  2> /dev/null
mysqldump --opt --user=${USER} --password=${PASS} --routines --databases ${DATABASE} > ${DEST}/${FILE}
gzip $DEST/$FILE
echo "${FILE}.gz was created:"
ls -l ${DEST}/${FILE}.gz

