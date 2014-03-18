# WARN! LINUX SHELL SCRIPT!

# SHAREBLOC BETA

cd /var/www/sharebloc_beta/
sudo mv logos html/logos
sudo cp -Rp /var/www/vendorstack_beta/logos /var/www/sharebloc_beta/html/logos
sudo semanage fcontext -a -t httpd_sys_rw_content_t '/var/www/sharebloc_beta/html/logos(/.*)?'
sudo chmod -R 775  /var/www/sharebloc_beta/html/logos
sudo restorecon -FR /var/www

--------------------------------------------

# SHAREBLOC

cd /var/www/sharebloc
sudo mv logos html/logos

sudo cp -Rp /var/www/htdocs/vendorstack/logos /var/www/sharebloc/html/logos
sudo semanage fcontext -a -t httpd_sys_rw_content_t '/var/www/sharebloc/html/logos(/.*)?'
sudo chmod -R 775  /var/www/sharebloc/html/logos
sudo restorecon -FR /var/www/sharebloc

# [bear@web1 logos]$ ./
# Display all 13283 possibilities? (y or n)^C
# [bear@web1 logos]$ ls logos/
# Display all 7658 possibilities? (y or n)^C

sudo sh -c 'mv -n /var/www/sharebloc/html/logos/logos/* /var/www/sharebloc/html/logos'

# [bear@web1 logos]$ ./
# Display all 16545 possibilities? (y or n)
# [bear@web1 logos]$ ./logos/
# Display all 4396 possibilities? (y or n)
# [bear@web1 logos]$ ./logos/

sudo rm -Rf /var/www/sharebloc/html/logos/logos/