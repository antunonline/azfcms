CREATE  TABLE IF NOT EXISTS `azfcms`.`Navigation_ACLGroup` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `navigationId` INT UNSIGNED NOT NULL ,
  `aclGroupId` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `navigationId` (`navigationId` ASC) ,
  INDEX `aclGroupId_` (`aclGroupId` ASC) ,
  CONSTRAINT `navigationId`
    FOREIGN KEY (`navigationId` )
    REFERENCES `azfcms`.`Navigation` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `aclGroupId_`
    FOREIGN KEY (`aclGroupId` )
    REFERENCES `azfcms`.`ACLGroup` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB