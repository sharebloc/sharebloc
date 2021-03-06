<log4php:configuration xmlns:log4php="http://logging.apache.org/log4php/" threshold="all" debug="true">
    <appender name="file" class="LoggerAppenderRollingFile">
        <param name="file" value="/opt/dslabs/trillium/vendorstack/log/vs_log4php.txt" />
        <param name="maxFileSize" value="10M" />
        <param name="maxBackupIndex" value="3" />
        <layout class="LoggerLayoutPattern">
            <param name="ConversionPattern" value="%d{d H:i:s} %M:%L %5p  %m%n" />
        </layout>
    </appender>
    <appender name="trigger_error" class="LoggerAppenderPhp" threshold="WARN">
        <layout class="LoggerLayoutPattern">
            <param name="ConversionPattern" value="VS - %5p  %m" />
        </layout>
    </appender>
    <logger name="vs_logger">
        <level value="info" />
        <appender_ref ref="trigger_error" />
        <appender_ref ref="file" />
    </logger>
</log4php:configuration>