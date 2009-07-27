<?php

/*$mainUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "ALTER IGNORE TABLE `__CL_MAIN__cours`
         ADD COLUMN `creationDate` DATETIME DEFAULT NULL;",
        "Add creationDate to course table",
        Upgrade_TaskConstants::ON_ERROR_CONTINUE
    )
);

$mainUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "ALTER IGNORE TABLE `__CL_MAIN__cours`
         ADD COLUMN `expirationDate` DATETIME DEFAULT NULL;",
        "Add expirationDate to course table",
        Upgrade_TaskConstants::ON_ERROR_CONTINUE
    )
);

$mainUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "ALTER IGNORE TABLE `__CL_MAIN__cours`
         ADD COLUMN `defaultProfileId` INT(11) NOT NULL;",
        "Add defaultProfileId to course table",
        Upgrade_TaskConstants::ON_ERROR_INTERRUPT
    )
);*/

$mainUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "ALTER IGNORE TABLE `__CL_MAIN__cours`
         ADD COLUMN `status` enum('enable','pending','disable','trash','date') NOT NULL DEFAULT 'enable';",
        "Add status to course table",
        Upgrade_TaskConstants::ON_ERROR_INTERRUPT
    )
);

$mainUpgradeTasks->add(
    new Upgrade_Task_Sql(
        "UPDATE `__CL_MAIN__cours`
         SET `status`   = 'enable';",
        "Set default status for existing courses",
        Upgrade_TaskConstants::ON_ERROR_INTERRUPT
    )
);
