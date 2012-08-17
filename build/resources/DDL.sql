SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `azfcms` ;
CREATE SCHEMA IF NOT EXISTS `azfcms` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `azfcms` ;

-- -----------------------------------------------------
-- Table `azfcms`.`Navigation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`Navigation` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`Navigation` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `parentId` INT UNSIGNED NULL ,
  `tid` INT UNSIGNED NOT NULL ,
  `l` INT UNSIGNED NULL ,
  `r` INT UNSIGNED NULL ,
  `disabled` INT UNSIGNED NOT NULL DEFAULT 1 ,
  `url` VARCHAR(125) NULL ,
  `final` MEDIUMTEXT NOT NULL ,
  `plugins` MEDIUMTEXT NOT NULL ,
  `abstract` MEDIUMTEXT NOT NULL ,
  `home` TINYINT(1) NOT NULL DEFAULT false ,
  `title` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `azfcms`.`ACLGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`ACLGroup` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`ACLGroup` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(60) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `aclgroupname` ON `azfcms`.`ACLGroup` (`name` ASC) ;


-- -----------------------------------------------------
-- Table `azfcms`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`User` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`User` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `loginName` VARCHAR(45) NOT NULL ,
  `firstName` VARCHAR(45) NOT NULL ,
  `lastName` VARCHAR(45) NOT NULL ,
  `password` VARCHAR(40) NOT NULL ,
  `email` VARCHAR(45) NOT NULL ,
  `verified` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `verificationKey` VARCHAR(32) NOT NULL ,
  `cTime` TIMESTAMP NOT NULL DEFAULT NOW() ,
  `rTime` TIMESTAMP NOT NULL DEFAULT '2000-01-01' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `email_UNIQUE` ON `azfcms`.`User` (`email` ASC) ;

CREATE UNIQUE INDEX `loginName_UNIQUE` ON `azfcms`.`User` (`loginName` ASC) ;


-- -----------------------------------------------------
-- Table `azfcms`.`Navigation_ACLGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`Navigation_ACLGroup` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`Navigation_ACLGroup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `navigationId` INT NOT NULL ,
  `aclGroupId` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `index2` ON `azfcms`.`Navigation_ACLGroup` (`navigationId` ASC, `aclGroupId` ASC) ;


-- -----------------------------------------------------
-- Table `azfcms`.`User_ACLGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`User_ACLGroup` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`User_ACLGroup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `userId` INT NOT NULL ,
  `aclGroupId` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `index2` ON `azfcms`.`User_ACLGroup` (`userId` ASC, `aclGroupId` ASC) ;


-- -----------------------------------------------------
-- Table `azfcms`.`Plugin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`Plugin` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`Plugin` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `type` VARCHAR(45) NOT NULL ,
  `name` VARCHAR(60) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  `region` VARCHAR(45) NOT NULL ,
  `params` MEDIUMTEXT NOT NULL ,
  `weight` SMALLINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `azfcms`.`NavigationPlugin`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`NavigationPlugin` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`NavigationPlugin` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `navigationId` INT NOT NULL ,
  `pluginId` INT NOT NULL ,
  `weight` SMALLINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `index2` ON `azfcms`.`NavigationPlugin` (`navigationId` ASC, `pluginId` ASC) ;


-- -----------------------------------------------------
-- Table `azfcms`.`Acl`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`Acl` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`Acl` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `resource` VARCHAR(45) NULL ,
  `description` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `resource_UNIQUE` ON `azfcms`.`Acl` (`resource` ASC) ;


-- -----------------------------------------------------
-- Table `azfcms`.`Acl_AclGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `azfcms`.`Acl_AclGroup` ;

CREATE  TABLE IF NOT EXISTS `azfcms`.`Acl_AclGroup` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `aclId` INT NOT NULL ,
  `aclGroupId` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE UNIQUE INDEX `aclaclgroupid` ON `azfcms`.`Acl_AclGroup` (`aclId` ASC, `aclGroupId` ASC) ;


-- -----------------------------------------------------
-- procedure navigation_completeUserMenu
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`navigation_completeUserMenu`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`navigation_completeUserMenu` (IN userId INT)
BEGIN
SELECT DISTINCT n.* FROM User_ACLGroup ua
JOIN Navigation_ACLGroup na ON ua.aclGroupId = na.aclGroupId
JOIN  Navigation n ON na.navigationId = n.id
WHERE ua.userId = userId AND n.disabled = 0
ORDER BY l;
END





$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure navigation_breadCrumbMenu
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`navigation_breadCrumbMenu`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`navigation_breadCrumbMenu` (IN nodeId INT, IN userId INT)
BEGIN
SELECT n.id , n.url FROM User_ACLGroup ua
JOIN Navigation_ACLGroup na ON ua.aclGroupId = na.aclGroupId
JOIN Navigation n1 ON n1.id = nodeId
JOIN Navigation n ON n.l <= n1.l AND n.r >= n1.r AND n.id = na.navigationId
WHERE n1.id = nodeId AND ua.userId = userId AND n.parentId IS NOT NULL
ORDER BY n.l ASC;
END
$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure navigation_rootMenu
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`navigation_rootMenu`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`navigation_rootMenu` (IN userId INT)
BEGIN
SELECT DISTINCT n.id, n.url FROM User_ACLGroup ua
JOIN Navigation_ACLGroup na ON ua.aclGroupId = na.aclGroupId
JOIN Navigation n ON n.id = na.navigationId AND n.parentId = 1
WHERE ua.userId = userId
ORDER BY n.l;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure navigation_contextMenu
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`navigation_contextMenu`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`navigation_contextMenu` (IN contextId INT, IN userId INT)
BEGIN
SELECT DISTINCT n.id, n.url FROM User_ACLGroup ua
JOIN Navigation_ACLGroup na ON ua.aclGroupId = na.aclGroupId
JOIN Navigation n ON n.id = na.navigationId AND n.parentId = contextId
WHERE ua.userId = userId
ORDER BY n.l;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure navigation_disable
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`navigation_disable`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`navigation_disable` (IN nodeId INT)
BEGIN
DECLARE l, r, i INT DEFAULT 0;
DECLARE node CURSOR FOR SELECT n.l, n.r FROM Navigation n WHERE n.id = nodeId;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET i = 1;
OPEN node;
REPEAT
FETCH node INTO l,r;
UNTIL i = 1
END REPEAT;
CLOSE node;
IF l != 0 THEN
 UPDATE Navigation n SET disabled = 1 WHERE n.l >= l AND n.r <= r;
END IF;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure navigation_enable
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`navigation_enable`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`navigation_enable` (IN nodeId INT)
BEGIN
DECLARE l, r, i INT DEFAULT 0;
DECLARE node CURSOR FOR SELECT n.l, n.r FROM Navigation n WHERE n.id = nodeId;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET i = 1;
OPEN node;
REPEAT
FETCH node INTO l,r;
UNTIL i = 1
END REPEAT;
CLOSE node;
IF l != 0 THEN
 UPDATE Navigation n SET disabled = 0 WHERE n.l >= l AND n.r <= r;
END IF;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure navigation_dynamicParams
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`navigation_dynamicParams`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`navigation_dynamicParams` (IN nodeId INT)
BEGIN
SELECT n.abstract AS abstract FROM Navigation n1
JOIN Navigation n ON n.l <= n1.l AND n.r >= n1.r
WHERE n1.id = nodeId AND n.parentId IS NOT NULL
ORDER BY n.l DESC;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetchConfiguration
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`fetchConfiguration`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`fetchConfiguration` (IN nodeId INT)
BEGIN
IF nodeId = -1 THEN
(SELECT 1 AS resultSet, n.title, n.id, n.url,n.final, n.`abstract`, n.plugins FROM Navigation n
WHERE n.home = 1)
UNION 
(SELECT 2 AS resultSet, n.title, n.id, n.url, n.final, n.`abstract`, n.plugins FROM Navigation n1
JOIN Navigation n ON n.l < n1.l AND n.r > n1.r
WHERE n1.id = nodeId
ORDER BY n.l DESC);
ELSE 
(SELECT 1 AS resultSet, n.title,n.id, n.url,n.final, n.`abstract`, n.plugins FROM Navigation n
WHERE n.id = nodeId)
UNION 
(SELECT 2 AS resultSet, n.title,n.id, n.url, n.final, n.`abstract`, n.plugins FROM Navigation n1
JOIN Navigation n ON n.l < n1.l AND n.r > n1.r
WHERE n1.id = nodeId
ORDER BY n.l DESC);
END IF;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure user_fetchAclRules
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`user_fetchAclRules`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`user_fetchAclRules` (IN userId INT)
BEGIN
 SELECT DISTINCT a.id, a.resource, a.privilege FROM User u
JOIN User_ACLGroup ua ON ua.userId = u.id
JOIN ACLGroup a ON ua.aclGroupId = a.id
WHERE u.id = userId;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure fetchExtensionPluginEnableConfiguration
-- -----------------------------------------------------

DROP procedure IF EXISTS `azfcms`.`fetchExtensionPluginEnableConfiguration`;

DELIMITER $$
USE `azfcms`$$
CREATE PROCEDURE `azfcms`.`fetchExtensionPluginEnableConfiguration` ()
BEGIN
(SELECT DISTINCT
    n.id as navigationId,
    n.title as title,
    n.l as l,
    n.r as r,
    1 as enabled,
    np.id as navigationPluginId,
    np.weight as weight,
    p.id as pluginId,
    p.`name` as `name`,
    p.region as region
FROM
    Navigation n
JOIN
    NavigationPlugin np
ON
    n.id = np.navigationId
JOIN
    Plugin p
ON 
    np.pluginId = p.id)
UNION
(SELECT DISTINCT
    n.id as navigationId,
    n.title as title,
    n.l as l,
    n.r as r,
    0 as enabled,
    0 as navigationPluginId,
    0 as weight,
    p.id as pluginId,
    p.`name` as `name`,
    p.region as region
FROM
    Navigation n
JOIN
    Plugin p
ON
    p.id NOT IN (SELECT np.pluginId FROM NavigationPlugin np WHERE np.navigationId = n.id)
) ORDER BY l,r,region, enabled desc, weight ;
END$$

DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
