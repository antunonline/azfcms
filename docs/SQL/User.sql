CREATE  TABLE IF NOT EXISTS `azfcms`.`User` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `loginName` VARCHAR(45) NOT NULL ,
  `firstName` VARCHAR(45) NOT NULL ,
  `lastName` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB