
UPDATE email_settings
    SET setting_text = 'Periodically let me know what\'s new on ShareBloc.'
WHERE setting_id = '1';

UPDATE email_settings
    SET setting_text = 'I do not want ShareBloc to e-mail me anymore.'
WHERE setting_id = '99';