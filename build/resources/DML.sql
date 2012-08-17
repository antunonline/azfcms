-- -----------------------------------------------------
-- Data for table `azfcms`.`Navigation`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `azfcms`.`Navigation` (`id`, `parentId`, `tid`, `l`, `r`, `disabled`, `url`, `final`, `plugins`, `abstract`, `home`, `title`) VALUES (1, NULL, 1, 1, 2, 0, '/', '{}', '{}', '{}', 1, 'Root');

COMMIT;

-- -----------------------------------------------------
-- Data for table `azfcms`.`ACLGroup`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `azfcms`.`ACLGroup` (`id`, `name`, `description`) VALUES (1, 'Guest', 'Default Guest group');
INSERT INTO `azfcms`.`ACLGroup` (`id`, `name`, `description`) VALUES (2, 'Root', 'Default Root group');

COMMIT;

-- -----------------------------------------------------
-- Data for table `azfcms`.`User`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `azfcms`.`User` (`id`, `loginName`, `firstName`, `lastName`, `password`, `email`, `verified`, `verificationKey`, `cTime`, `rTime`) VALUES (1, 'guest', 'Guest', 'Guest', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', 'guest@example.com', 1, 0, '01.01.2000', '01.01.2000');
INSERT INTO `azfcms`.`User` (`id`, `loginName`, `firstName`, `lastName`, `password`, `email`, `verified`, `verificationKey`, `cTime`, `rTime`) VALUES (2, 'root', 'Root', 'Root', 'root', 'root', 1, 0, '01.01.2000', '01.01.2000');

COMMIT;

-- -----------------------------------------------------
-- Data for table `azfcms`.`Navigation_ACLGroup`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `azfcms`.`Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (1, 1, 1);
INSERT INTO `azfcms`.`Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (2, 2, 1);
INSERT INTO `azfcms`.`Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (3, 3, 1);
INSERT INTO `azfcms`.`Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (4, 4, 1);
INSERT INTO `azfcms`.`Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (5, 5, 1);
INSERT INTO `azfcms`.`Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (6, 6, 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `azfcms`.`User_ACLGroup`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `azfcms`.`User_ACLGroup` (`id`, `userId`, `aclGroupId`) VALUES (1, 1, 1);
INSERT INTO `azfcms`.`User_ACLGroup` (`id`, `userId`, `aclGroupId`) VALUES (2, 2, 2);

COMMIT;

-- -----------------------------------------------------
-- Data for table `azfcms`.`Acl`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `azfcms`.`Acl` (`id`, `resource`, `description`) VALUES (1, 'cms.userStereotype.root', 'Root user');

COMMIT;

-- -----------------------------------------------------
-- Data for table `azfcms`.`Acl_AclGroup`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `azfcms`.`Acl_AclGroup` (`id`, `aclId`, `aclGroupId`) VALUES (1, 1, 2);

COMMIT;
