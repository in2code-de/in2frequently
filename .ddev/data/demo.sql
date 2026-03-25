-- Demo content for in2frequently DDEV environment
-- Imported after `typo3 setup` has created all system tables.
-- typo3 setup does NOT create a root page by default (requires --create-site).
-- This script builds the full page structure from scratch.

-- Root page (pid=0 = top-level in tree, is_siteroot=1 = site entry point)
INSERT INTO pages
    (pid, tstamp, crdate, hidden, deleted, perms_userid, perms_groupid, perms_user, perms_group, perms_everybody, title, doktype, is_siteroot, slug, sorting)
VALUES (
    0,
    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    0, 0,
    1, 1, 31, 31, 1,
    'in2frequently Demo',
    1, 1, '/', 256
);

-- Main TypoScript template on root page (uid=1)
INSERT INTO sys_template
    (pid, tstamp, crdate, hidden, deleted, root, clear, title, constants, config)
VALUES (
    1,
    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    0, 0,
    1, 3,
    'Main Template',
    "@import 'EXT:fluid_styled_content/Configuration/TypoScript/constants.typoscript'",
    "@import 'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript'\n\npage = PAGE\npage.10 < styles.content.get"
);

-- Demo content page (child of root, pid=1)
INSERT INTO pages
    (pid, tstamp, crdate, hidden, deleted, perms_userid, perms_groupid, perms_user, perms_group, perms_everybody, title, doktype, slug, sorting)
VALUES (
    1,
    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    0, 0,
    1, 1, 31, 31, 1,
    'Demo Page',
    1, '/example', 256
);

SET @demo_page_uid = LAST_INSERT_ID();

-- Demo content element with in2frequently fields inactive (element always visible)
INSERT INTO tt_content
    (pid, tstamp, crdate, hidden, deleted, CType, header, bodytext, colPos, sorting, tx_in2frequently_active, tx_in2frequently_starttime, tx_in2frequently_endtime)
VALUES (
    @demo_page_uid,
    UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
    0, 0,
    'text',
    'Hello from in2frequently',
    '<p>This content element is always visible. Open it in the backend and enable the <strong>Recurring Visibility</strong> toggle (in the Access tab) to test the extension.</p><p>Example: set <em>Visible from</em> to <code>every 1st</code> and <em>Visible until</em> to <code>every 15th</code> to show this element only in the first half of each month.</p>',
    0, 256, 0, '', ''
);
