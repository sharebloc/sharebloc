<log4php:configuration xmlns:log4php="http://logging.apache.org/log4php/" threshold="all" debug="true">
    <--appender name="file" class="LoggerAppenderFile">
        <param name="file" value="/var/log/httpd/dataentry_log4php.log" />
        <layout class="LoggerLayoutPattern">
            <param name="ConversionPattern" value="%M:%L - %5p  %m%n" />
        </layout>
    </appender-->
    <appender name="trigger_error" class="LoggerAppenderPhp">
        <layout class="LoggerLayoutPattern">
            <param name="ConversionPattern" value="Data Entry - %5p  %m" />
        </layout>
    </appender>
    <logger name="data_entry_logger">
        <level value="warn" />
        <appender_ref ref="trigger_error" />
        <!--appender_ref ref="file" /-->
    </logger>
</log4php:configuration>