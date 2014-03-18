Development instance should have installed:
- php
- mysql
- memcached
- svn
- apache

1) Checkout current trunk to your desired location
2) Configure apache host to have \html as a www root
3) Change permissions for next dir (next includes the commands for selinux, they are in sql_updates\permissions.txt file)
4) Dump DB or DB structure and restore it on your DB server. Be sure to delete or clear important data like passwords and emails for all real users in next tables: join_requests, oauth, subscriptions, user to never send them email by mistake
5) Set up memcached
6) Configure you SB instance by editing next two files:
includes\class.Settings.php:
    const DB_HOST = "10.55.31.194";
    const DB_USER = "web";
    const DB_PASSWORD = "scsdc";
    const DB_DBNAME = "vendorstack";
    const CACHE_MEMCACHE_PORT = 11211;

includes\log4php.xml (extended logging)
    <param name="file" value="/opt/dslabs/trillium/vendorstack/log/vs_log4php.txt" /> - path to file
    <level value="warn" /> - level of logs to log

7) Probably that's all, though i can miss easily something and ready to answer on your questions if any and update this document.

Please note that sometimes you may find strange solutions and crutches in the code, and some non-coordination between parts of code. This was made by me as well as by previous developer, and I'm sorry for this.

