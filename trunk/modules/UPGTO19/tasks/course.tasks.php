<?php

$CourseUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "ALTER IGNORE TABLE `__CL_COURSE__qwz_exercise`
         ADD `quizEndMessage` TEXT NOT NULL;",
        "Add message at the end of a quiz",
        Upgrade_TaskConstants::ON_ERROR_INTERRUPT
    )
);

$CourseUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "CREATE TABLE IF NOT EXISTS `__CL_COURSE__qwz_users_random_questions` (
            `id` int(11) NOT NULL auto_increment,
            `user_id` int(11) NOT NULL,
            `exercise_id` int(11) NOT NULL,
            `questions` text NOT NULL,
            PRIMARY KEY  (`id`)
         ) ENGINE=MyISAM;",
        "Create table to store random quiz questions",
        Upgrade_TaskConstants::ON_ERROR_INTERRUPT
    )
);

$CourseUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "ALTER IGNORE TABLE `__CL_COURSE__qwz_exercise`
         ADD `useSameShuffle` ENUM( '0', '1' ) NOT NULL DEFAULT '0' AFTER `shuffle`;",
        "Add field to use same random questions in a quiz",
        Upgrade_TaskConstants::ON_ERROR_INTERRUPT
    )
);

$CourseUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "UPDATE `__CL_COURSE__qwz_exercise`
         SET useSameShuffle = 0;",
        "Set default option for use same random questions mode",
        Upgrade_TaskConstants::ON_ERROR_CONTINUE
    )
);

$CourseUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "CREATE TABLE IF NOT EXISTS `__CL_COURSE__bb_rel_forum_userstonotify` (
            `notify_id` int(10) NOT NULL auto_increment,
            `user_id` int(10) NOT NULL default '0',
            `forum_id` int(10) NOT NULL default '0',
            PRIMARY KEY  (`notify_id`),
            KEY `SECONDARY` (`user_id`,`forum_id`)
         ) TYPE=MyISAM;",
        "Create a table to store the forum notification preferences of a user",
        Upgrade_TaskConstants::ON_ERROR_CONTINUE
    )
);
