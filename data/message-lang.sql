SET @now := now();

REPLACE INTO perihelion_Lang VALUES ('messageDraft', 'Draft Message', 0, 'メッセージ作成', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageTo', 'To', 0, '宛先', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageFlag', 'Flag', 0, 'フラグ', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageReadState', 'Read', 0, '既読', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageSubject', 'Subject', 0, '件名', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messagesContent', 'Content', 0, '内容', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('sendMessage', 'Send', 0, '送信', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageRead', 'xxxxxxx', 0, 'xxxxxxx', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageView', 'Message', 0, 'メッセージ', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageConfirmDelete', 'Confirm Delete', 0, '削除確認', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageInbox', 'Inbox', 0, 'インボックス', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('message', 'Message', 0, 'メッセージ', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageSendDateTime', 'Date Time', 0, '日時', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageReadStateOpened', 'Opened', 0, '既読', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageReadStateUnopened', 'Unopened', 0, '未読', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageCorrespondents', 'Correspondents', 0, 'コレスポンデント', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageReply', 'Reply', 0, '返事', 0, @now);
REPLACE INTO perihelion_Lang VALUES ('messageSubmitReply', 'Submit', 0, '送信', 0, @now);

-- REPLACE INTO perihelion_Lang VALUES ('xxxxxxx', 'xxxxxxx', 0, 'xxxxxxx', 0, @now);

