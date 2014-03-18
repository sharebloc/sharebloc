Deployment

1) Create tables
2) Edit DB credentials in config/Settings.class.php
3) Edit config/log4php.xml, line
 <param name="file" value="/opt/dslabs/trillium/log/data_entry_log4php.txt" />
 Apache should have write access to this file.

Example:

touch /var/log/httpd/dataentry_log4php.log
touch /var/log/httpd/dataentry_log4php_test.log
chown apache:apache /var/log/httpd/dataentry_log4php.log
chown apache:apache /var/log/httpd/dataentry_log4php_test.log
chmod 755  /var/log/httpd/dataentry_log4php.log
chmod 755  /var/log/httpd/dataentry_log4php_test.log

touch /var/log/httpd/dataentry_log4php.log
touch /var/log/httpd/dataentry_log4php_test.log

4)
cd /var/www/tagtagrevenge/
chown -R apache:apache html
chmod -R 755  html/logos
chown -R apache:apache log
chmod -R 755  log

chown -R apache:apache tpl/templates_c
chmod -R 755  tpl/templates_c


/var/www/test-tagtagrevenge/log/dataentry_log4php_test.log
/var/www/tagtagrevenge/log/dataentry_log4php_test.log


