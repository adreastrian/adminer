<?php

require_once __DIR__ . '/Plugins/Appearance.php';
require_once __DIR__ . '/Plugins/AdminerDatabaseHide.php';
require_once __DIR__ . '/Plugins/AdminerEditTextarea.php';
require_once __DIR__ . '/Plugins/AdminerEnumOption.php';
require_once __DIR__ . '/Plugins/AdminerEditForeign.php';
require_once __DIR__ . '/Plugins/AdminerTablesFilter.php';
require_once __DIR__ . '/Plugins/AdminerJsonPreview.php';
require_once __DIR__ . '/Plugins/AdminerCollations.php';
require_once __DIR__ . '/Plugins/AutoLogin.php';

return [
    new Appearance,
    new AdminerDatabaseHide([
        'information_schema',
        'mysql',
        'performance_schema',
        'sys',
    ]),
    new AdminerEditTextarea,
    new AdminerEnumOption,
    new AdminerEditForeign,
    new AdminerTablesFilter,
    new AdminerJsonPreview,
    new AdminerCollations,
    new AutoLogin,
];
