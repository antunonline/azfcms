START TRANSACTION;
INSERT INTO `Navigation` (`id`, `parentId`, `tid`, `l`, `r`, `disabled`, `url`, `final`, `plugins`, `abstract`, `home`, `title`) VALUES (1, NULL, 1, 1, 2, 0, '/', '{}', '{}', '{}', 1, 'Root');

COMMIT;

START TRANSACTION;
INSERT INTO `ACLGroup` (`id`, `name`, `description`) VALUES (1, 'Guest', 'Default Guest group');
INSERT INTO `ACLGroup` (`id`, `name`, `description`) VALUES (2, 'Root', 'Default Root group');

COMMIT;

START TRANSACTION;
INSERT INTO `User` (`id`, `loginName`, `firstName`, `lastName`, `password`, `email`, `verified`, `verificationKey`, `cTime`, `rTime`) VALUES (1, 'guest', 'Guest', 'Guest', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', 'guest@example.com', 1, 0, '01.01.2000', '01.01.2000');
INSERT INTO `User` (`id`, `loginName`, `firstName`, `lastName`, `password`, `email`, `verified`, `verificationKey`, `cTime`, `rTime`) VALUES (2, 'root', 'Root', 'Root', 'sha1rootpassword', 'root', 1, 0, '01.01.2000', '01.01.2000');

COMMIT;

START TRANSACTION;
INSERT INTO `Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (1, 1, 1);
INSERT INTO `Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (2, 2, 1);
INSERT INTO `Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (3, 3, 1);
INSERT INTO `Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (4, 4, 1);
INSERT INTO `Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (5, 5, 1);
INSERT INTO `Navigation_ACLGroup` (`id`, `navigationId`, `aclGroupId`) VALUES (6, 6, 1);

COMMIT;

START TRANSACTION;
INSERT INTO `User_ACLGroup` (`id`, `userId`, `aclGroupId`) VALUES (1, 1, 1);
INSERT INTO `User_ACLGroup` (`id`, `userId`, `aclGroupId`) VALUES (2, 2, 2);

COMMIT;

START TRANSACTION;
INSERT INTO `Acl` (`id`, `resource`, `description`) VALUES (1, 'cms.userStereotype.root', 'Root user');
INSERT INTO `Acl` (`id`, `resource`, `description`) VALUES (2, 'resource.admin.rw', 'Read and write admin access');
INSERT INTO `Acl` (`id`, `resource`, `description`) VALUES (3, 'resource.user.login', 'User login privilege. Used by guest users to access their registered accounts.');

COMMIT;

START TRANSACTION;
INSERT INTO `Acl_AclGroup` (`id`, `aclId`, `aclGroupId`) VALUES (1, 1, 2); -- Add root access to root group
INSERT INTO `Acl_AclGroup` (`id`, `aclId`, `aclGroupId`) VALUES (2, 3, 1); -- Add login access to guest group

COMMIT;
